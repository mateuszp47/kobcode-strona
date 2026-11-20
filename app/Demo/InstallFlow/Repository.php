<?php
namespace App\Demo\InstallFlow;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

final class Repository
{
    public const ORDER_STATUSES = ['new' => 'Nowe', 'scheduled' => 'Zaplanowane', 'in_progress' => 'W realizacji', 'completed' => 'Zakończone', 'cancelled' => 'Anulowane'];
    public const OFFER_STATUSES = ['draft' => 'Szkic', 'sent' => 'Wysłana', 'accepted' => 'Zaakceptowana', 'rejected' => 'Odrzucona'];
    public const AVAILABILITY = ['available' => 'Dostępny', 'busy' => 'Zajęty', 'leave' => 'Nieobecny'];

    public function db() { return DB::connection('installflow'); }
    public function clients(): Collection { return $this->db()->table('clients')->orderBy('name')->get(); }
    public function employees(): Collection { return $this->db()->table('employees')->orderBy('name')->get(); }
    public function orders(): Collection
    {
        return $this->db()->table('orders')->join('clients', 'clients.id', '=', 'orders.client_id')
            ->leftJoin('employees', 'employees.id', '=', 'orders.employee_id')
            ->select('orders.*', 'clients.name as client_name', 'clients.city', 'employees.name as employee_name')
            ->orderByDesc('orders.updated_at')->orderByDesc('orders.id')->get();
    }
    public function offers(): Collection
    {
        return $this->db()->table('offers')->join('clients', 'clients.id', '=', 'offers.client_id')
            ->leftJoin('orders', 'orders.offer_id', '=', 'offers.id')->select('offers.*', 'clients.name as client_name', 'orders.id as order_id')->orderByDesc('offers.id')->get();
    }
    public function find(string $table, int $id): object
    {
        abort_unless(in_array($table, ['clients', 'employees', 'offers', 'orders']), 404);
        return $this->db()->table($table)->find($id) ?? abort(404);
    }
    public function metrics(): array
    {
        $orders = $this->orders(); $offers = $this->offers();
        $completed = $orders->where('status', 'completed');
        $team = $this->employees()->map(fn ($employee) => (object) ['name' => $employee->name,
            'active' => $orders->where('employee_id', $employee->id)->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'completed' => $completed->where('employee_id', $employee->id)->count(),
            'value' => $completed->where('employee_id', $employee->id)->sum('gross_cents')]);
        $months = collect(range(5, 0))->map(function ($offset) use ($completed) {
            $date = now()->startOfMonth()->subMonths($offset);
            return ['label' => ['sty', 'lut', 'mar', 'kwi', 'maj', 'cze', 'lip', 'sie', 'wrz', 'paź', 'lis', 'gru'][$date->month - 1],
                'value' => $completed->filter(fn ($order) => str_starts_with($order->completed_at ?? '', $date->format('Y-m')))->sum('gross_cents')];
        });
        return ['clients' => $this->db()->table('clients')->count(),
            'active' => $orders->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'completed' => $completed->count(), 'scheduled' => $orders->where('status', 'scheduled')->count(),
            'offers_value' => $offers->whereNotIn('status', ['rejected'])->sum('gross_cents'),
            'accepted_value' => $offers->where('status', 'accepted')->sum('gross_cents'),
            'revenue' => $completed->sum('gross_cents'), 'team' => $team, 'months' => $months,
            'statuses' => collect(self::ORDER_STATUSES)->map(fn ($label, $key) => ['label' => $label, 'count' => $orders->where('status', $key)->count()]),
            'upcoming' => $orders->whereNotIn('status', ['completed', 'cancelled'])->filter(fn ($order) => $order->scheduled_at && $order->scheduled_at >= now()->startOfDay()->toDateTimeString())->sortBy('scheduled_at')->take(5),
            'activity' => $this->db()->table('order_events')->join('orders', 'orders.id', '=', 'order_events.order_id')
                ->select('order_events.*', 'orders.title')->orderByDesc('order_events.created_at')->orderByDesc('order_events.id')->limit(7)->get()];
    }
    public function event(int $id, string $message): void
    {
        $this->db()->table('order_events')->insert(['order_id' => $id, 'message' => $message, 'created_at' => now()]);
    }
}
