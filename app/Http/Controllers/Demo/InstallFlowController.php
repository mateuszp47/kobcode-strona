<?php
namespace App\Http\Controllers\Demo;

use App\Demo\InstallFlow\{Money, Repository};
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstallFlowController extends Controller
{
    public function __construct(private Repository $repo) {}

    private function page(string $view, string $title, array $data = [])
    {
        return view('demo.installflow.'.$view, $data + ['title' => $title, 'orderStatuses' => Repository::ORDER_STATUSES,
            'offerStatuses' => Repository::OFFER_STATUSES, 'availability' => Repository::AVAILABILITY]);
    }
    private function validate(Request $request, array $rules): array
    {
        return $request->validate($rules, [
            'required' => 'Uzupełnij pole :attribute.', 'email' => 'Podaj poprawny adres e-mail.', 'max' => 'Pole :attribute przekracza dozwolony limit (:max).',
            'in' => 'Wybierz poprawną wartość pola :attribute.', 'exists' => 'Wybrany rekord nie istnieje w tej sesji demo.',
            'date' => 'Podaj poprawną datę.', 'date_format' => 'Podaj datę w poprawnym formacie.', 'integer' => 'Podaj liczbę całkowitą.',
            'numeric' => 'Podaj poprawną liczbę.', 'min' => 'Minimalna wartość pola :attribute to :min.', 'regex' => 'Użyj liczby z maksymalnie dwoma miejscami po przecinku.',
        ], ['name' => 'nazwa', 'title' => 'tytuł', 'client_id' => 'klient', 'employee_id' => 'pracownik', 'items' => 'pozycje oferty', 'status' => 'status', 'city' => 'miasto', 'note' => 'notatka']);
    }
    private function limit(string $table, int $limit = 200): void
    {
        abort_if($this->repo->db()->table($table)->count() >= $limit, 422, 'Osiągnięto limit rekordów w demo. Przywróć dane początkowe, aby zacząć od nowa.');
    }
    public function dashboard() { return $this->page('dashboard', 'Przegląd firmy', ['metrics' => $this->repo->metrics()]); }
    public function reports() { return $this->page('reports', 'Raporty i wyniki', ['metrics' => $this->repo->metrics()]); }
    public function reset(Request $request)
    {
        $request->session()->forget(['installflow.token', 'installflow.started']);
        return redirect()->route('installflow.dashboard')->with('success', 'Przywrócono dane początkowe. Pozostałe sesje nie zostały zmienione.');
    }
    public function clients(Request $request)
    {
        $filters = $this->validate($request, ['q' => 'nullable|string|max:100', 'type' => ['nullable', Rule::in(['person', 'company'])]]);
        $clients = $this->repo->clients()->filter(fn ($row) => (! ($filters['q'] ?? '') || mb_stripos($row->name.' '.$row->email.' '.$row->phone.' '.$row->city, $filters['q']) !== false)
            && (! ($filters['type'] ?? '') || $row->type === $filters['type']));
        return $this->page('clients', 'Klienci', compact('clients'));
    }
    public function clientForm(?int $id = null)
    {
        return $this->page('client-form', $id ? 'Edytuj klienta' : 'Nowy klient', ['client' => $id ? $this->repo->find('clients', $id) : null]);
    }
    public function saveClient(Request $request, ?int $id = null)
    {
        if ($id) $this->repo->find('clients', $id); else $this->limit('clients');
        $data = $this->validate($request, ['name' => 'required|string|max:150', 'type' => ['required', Rule::in(['person', 'company'])],
            'email' => 'nullable|email|max:150', 'phone' => 'nullable|string|max:30', 'city' => 'required|string|max:100', 'address' => 'nullable|string|max:200', 'notes' => 'nullable|string|max:3000']);
        $data['updated_at'] = now();
        if ($id) $this->repo->db()->table('clients')->where('id', $id)->update($data);
        else $id = $this->repo->db()->table('clients')->insertGetId($data + ['created_at' => now()]);
        return redirect()->route('installflow.clients.show', $id)->with('success', 'Zapisano dane klienta w tej sesji demo.');
    }
    public function client(int $id)
    {
        return $this->page('client', 'Szczegóły klienta', ['client' => $this->repo->find('clients', $id),
            'offers' => $this->repo->offers()->where('client_id', $id), 'orders' => $this->repo->orders()->where('client_id', $id),
            'contacts' => $this->repo->db()->table('contacts')->where('client_id', $id)->orderByDesc('contacted_at')->orderByDesc('id')->get()]);
    }
    public function contact(Request $request, int $id)
    {
        $this->repo->find('clients', $id); $this->limit('contacts', 400);
        $data = $this->validate($request, ['channel' => ['required', Rule::in(['phone', 'email', 'meeting', 'note'])], 'note' => 'required|string|max:2000']);
        $this->repo->db()->table('contacts')->insert($data + ['client_id' => $id, 'contacted_at' => now()]);
        return redirect()->route('installflow.clients.show', $id)->with('success', 'Dodano wpis do historii kontaktów. Żadna wiadomość nie została wysłana.');
    }
    public function deleteClient(int $id)
    {
        $this->repo->find('clients', $id);
        if ($this->repo->db()->table('offers')->where('client_id', $id)->exists() || $this->repo->db()->table('orders')->where('client_id', $id)->exists()) {
            return back()->withErrors(['client' => 'Klient ma oferty lub zlecenia. Zachowaj go, aby nie utracić powiązań.']);
        }
        $this->repo->db()->table('clients')->where('id', $id)->delete();
        return redirect()->route('installflow.clients')->with('success', 'Usunięto klienta z tej sesji demo.');
    }
    public function offers(Request $request)
    {
        $filters = $this->validate($request, ['q' => 'nullable|string|max:100', 'status' => ['nullable', Rule::in(array_keys(Repository::OFFER_STATUSES))]]);
        $offers = $this->repo->offers()->filter(fn ($row) => (! ($filters['q'] ?? '') || mb_stripos($row->title.' '.$row->client_name, $filters['q']) !== false)
            && (! ($filters['status'] ?? '') || $row->status === $filters['status']));
        return $this->page('offers', 'Oferty handlowe', compact('offers'));
    }
    public function offerForm(?int $id = null)
    {
        return $this->page('offer-form', $id ? 'Edytuj ofertę' : 'Nowa oferta', ['offer' => $id ? $this->repo->find('offers', $id) : null,
            'items' => $id ? $this->repo->db()->table('offer_items')->where('offer_id', $id)->get() : collect(), 'clients' => $this->repo->clients()]);
    }
    public function saveOffer(Request $request, ?int $id = null)
    {
        if ($id) {
            $this->repo->find('offers', $id);
            if ($this->repo->db()->table('orders')->where('offer_id', $id)->exists()) return back()->withErrors(['offer' => 'Oferta została przeniesiona do zlecenia. Jej warunki są już zapisane w zleceniu.']);
        } else $this->limit('offers');
        $data = $this->validate($request, ['client_id' => 'required|integer|exists:installflow.clients,id', 'title' => 'required|string|max:180',
            'status' => ['required', Rule::in(array_keys(Repository::OFFER_STATUSES))], 'valid_until' => 'required|date', 'notes' => 'nullable|string|max:3000',
            'items' => 'required|array|min:1|max:20', 'items.*.description' => 'required|string|max:200',
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:999.99', 'regex:/^\d+(\.\d{1,2})?$/D'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999.99', 'regex:/^\d+(\.\d{1,2})?$/D']]);
        $totals = Money::calculate($data['items']); unset($data['items']);
        $lines = $totals['items']; unset($totals['items']);
        $id = $this->repo->db()->transaction(function () use ($data, $totals, $lines, $id) {
            $row = $data + $totals + ['updated_at' => now()];
            if ($id) $this->repo->db()->table('offers')->where('id', $id)->update($row);
            else $id = $this->repo->db()->table('offers')->insertGetId($row + ['created_at' => now()]);
            $this->repo->db()->table('offer_items')->where('offer_id', $id)->delete();
            foreach ($lines as $line) $this->repo->db()->table('offer_items')->insert($line + ['offer_id' => $id]);
            return $id;
        });
        return redirect()->route('installflow.offers.show', $id)->with('success', 'Zapisano ofertę. Wartość została przeliczona na podstawie pozycji.');
    }
    public function offer(int $id)
    {
        return $this->page('offer', 'Szczegóły oferty', $this->offerData($id));
    }
    private function offerData(int $id): array
    {
        $offer = $this->repo->find('offers', $id);
        return ['offer' => $offer, 'client' => $this->repo->find('clients', $offer->client_id),
            'items' => $this->repo->db()->table('offer_items')->where('offer_id', $id)->get(),
            'order' => $this->repo->db()->table('orders')->where('offer_id', $id)->first()];
    }
    public function printOffer(int $id) { return $this->page('print', 'Oferta demonstracyjna', $this->offerData($id)); }
    public function convert(int $id)
    {
        $offer = $this->repo->find('offers', $id);
        abort_unless($offer->status === 'accepted', 422, 'Najpierw zaakceptuj ofertę.');
        $this->limit('orders');
        $orderId = $this->repo->db()->transaction(function () use ($offer) {
            $existing = $this->repo->db()->table('orders')->where('offer_id', $offer->id)->first();
            if ($existing) return $existing->id;
            $id = $this->repo->db()->table('orders')->insertGetId(['client_id' => $offer->client_id, 'offer_id' => $offer->id,
                'title' => $offer->title, 'status' => 'new', 'gross_cents' => $offer->gross_cents, 'notes' => $offer->notes,
                'created_at' => now(), 'updated_at' => now()]);
            $this->repo->event($id, 'Utworzono z zaakceptowanej oferty OF/'.str_pad((string) $offer->id, 4, '0', STR_PAD_LEFT).'.');
            return $id;
        });
        return redirect()->route('installflow.orders.show', $orderId)->with('success', 'Oferta jest powiązana ze zleceniem. Zaplanuj termin i przypisz pracownika.');
    }
    public function orders(Request $request)
    {
        $filters = $this->validate($request, ['q' => 'nullable|string|max:100', 'status' => ['nullable', Rule::in(array_keys(Repository::ORDER_STATUSES))], 'employee' => 'nullable|integer|exists:installflow.employees,id']);
        $orders = $this->repo->orders()->filter(fn ($row) => (! ($filters['q'] ?? '') || mb_stripos($row->title.' '.$row->client_name.' '.$row->city, $filters['q']) !== false)
            && (! ($filters['status'] ?? '') || $row->status === $filters['status']) && (! ($filters['employee'] ?? '') || $row->employee_id == $filters['employee']));
        return $this->page('orders', 'Zlecenia', ['orders' => $orders, 'employees' => $this->repo->employees()]);
    }
    public function orderForm(?int $id = null)
    {
        return $this->page('order-form', $id ? 'Edytuj zlecenie' : 'Nowe zlecenie', ['order' => $id ? $this->repo->find('orders', $id) : null, 'clients' => $this->repo->clients(), 'employees' => $this->repo->employees()]);
    }
    public function saveOrder(Request $request, ?int $id = null)
    {
        $previous = $id ? $this->repo->find('orders', $id) : null;
        if (! $id) $this->limit('orders');
        $data = $this->validate($request, ['client_id' => 'required|integer|exists:installflow.clients,id', 'employee_id' => 'nullable|integer|exists:installflow.employees,id',
            'title' => 'required|string|max:180', 'status' => ['required', Rule::in(array_keys(Repository::ORDER_STATUSES))],
            'scheduled_at' => 'nullable|date|required_if:status,scheduled', 'gross' => 'required|numeric|min:0|max:100000000|regex:/^\d+(\.\d{1,2})?$/D', 'notes' => 'nullable|string|max:3000']);
        $data['gross_cents'] = Money::hundredths($data['gross']); unset($data['gross']);
        $data['employee_id'] = $data['employee_id'] ?? null;
        $data['scheduled_at'] = ! empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at'])->toDateTimeString() : null;
        if ($previous?->offer_id) { $data['client_id'] = $previous->client_id; $data['gross_cents'] = $previous->gross_cents; }
        $data['completed_at'] = $data['status'] === 'completed' ? ($previous?->completed_at ?? now()) : null;
        $id = $this->repo->db()->transaction(function () use ($id, $data, $previous) {
            if ($id) $this->repo->db()->table('orders')->where('id', $id)->update($data + ['updated_at' => now()]);
            else $id = $this->repo->db()->table('orders')->insertGetId($data + ['created_at' => now(), 'updated_at' => now()]);
            $changes = [];
            if (! $previous) $changes[] = 'Utworzono zlecenie';
            if ($previous && $previous->status !== $data['status']) $changes[] = 'Status: '.Repository::ORDER_STATUSES[$data['status']];
            if ($previous && $previous->scheduled_at !== $data['scheduled_at']) $changes[] = 'Zmieniono termin montażu';
            if ($previous && $previous->employee_id != $data['employee_id']) $changes[] = 'Zmieniono przypisanie pracownika';
            $this->repo->event($id, $changes ? implode('. ', $changes).'.' : 'Zaktualizowano dane zlecenia.');
            return $id;
        });
        return redirect()->route('installflow.orders.show', $id)->with('success', 'Zapisano zlecenie i historię zmian.');
    }
    public function order(int $id)
    {
        $order = $this->repo->find('orders', $id);
        return $this->page('order', 'Szczegóły zlecenia', ['order' => $order, 'client' => $this->repo->find('clients', $order->client_id),
            'employee' => $order->employee_id ? $this->repo->find('employees', $order->employee_id) : null,
            'events' => $this->repo->db()->table('order_events')->where('order_id', $id)->orderByDesc('id')->get()]);
    }
    public function employees() { return $this->page('employees', 'Zespół', ['employees' => $this->repo->employees(), 'orders' => $this->repo->orders()]); }
    public function employeeForm(?int $id = null)
    {
        return $this->page('employee-form', $id ? 'Edytuj pracownika' : 'Nowy pracownik', ['employee' => $id ? $this->repo->find('employees', $id) : null]);
    }
    public function saveEmployee(Request $request, ?int $id = null)
    {
        if ($id) $this->repo->find('employees', $id); else $this->limit('employees', 40);
        $data = $this->validate($request, ['name' => 'required|string|max:150', 'role' => 'required|string|max:100', 'email' => 'required|email|max:150', 'phone' => 'required|string|max:30', 'availability' => ['required', Rule::in(array_keys(Repository::AVAILABILITY))]]);
        if ($id) $this->repo->db()->table('employees')->where('id', $id)->update($data + ['updated_at' => now()]);
        else $id = $this->repo->db()->table('employees')->insertGetId($data + ['created_at' => now(), 'updated_at' => now()]);
        return redirect()->route('installflow.employees.show', $id)->with('success', 'Zapisano dane pracownika.');
    }
    public function employee(int $id)
    {
        return $this->page('employee', 'Profil pracownika', ['employee' => $this->repo->find('employees', $id), 'orders' => $this->repo->orders()->where('employee_id', $id)->sortBy('scheduled_at')]);
    }
    public function calendar(Request $request)
    {
        $filters = $this->validate($request, ['month' => 'nullable|date_format:Y-m', 'employee' => 'nullable|integer|exists:installflow.employees,id']);
        $month = isset($filters['month']) ? Carbon::createFromFormat('Y-m-d', $filters['month'].'-01')->startOfDay() : now()->startOfMonth();
        $start = $month->copy()->startOfWeek(); $days = collect();
        for ($i = 0; $i < 42; $i++) $days->push($start->copy()->addDays($i));
        $visibleOrders = $this->repo->orders()->whereNotIn('status', ['cancelled'])->filter(fn ($order) => ! ($filters['employee'] ?? '') || $order->employee_id == $filters['employee']);
        $orders = $visibleOrders->filter(fn ($order) => $order->scheduled_at);
        $plannableOrders = $visibleOrders->whereNotIn('status', ['completed']);
        return $this->page('calendar', 'Kalendarz montaży', ['month' => $month, 'days' => $days, 'orders' => $orders, 'plannableOrders' => $plannableOrders, 'employees' => $this->repo->employees()]);
    }
    public function reschedule(Request $request)
    {
        $data = $this->validate($request, ['order_id' => 'required|integer|exists:installflow.orders,id']);
        return $this->schedule($request, (int) $data['order_id']);
    }
    public function schedule(Request $request, int $id)
    {
        $order = $this->repo->find('orders', $id);
        $data = $this->validate($request, ['scheduled_at' => 'required|date', 'employee_id' => 'required|integer|exists:installflow.employees,id']);
        if (in_array($order->status, ['completed', 'cancelled'])) return back()->withErrors(['schedule' => 'Zakończone lub anulowane zlecenie nie może być przeplanowane.']);
        $data['scheduled_at'] = Carbon::parse($data['scheduled_at'])->toDateTimeString();
        $this->repo->db()->transaction(function () use ($order, $data) {
            $this->repo->db()->table('orders')->where('id', $order->id)->update($data + ['status' => $order->status === 'new' ? 'scheduled' : $order->status, 'updated_at' => now()]);
            $this->repo->event($order->id, 'Zmieniono termin na '.Carbon::parse($data['scheduled_at'])->format('d.m.Y H:i').' i przypisanie ekipy.');
        });
        return redirect()->route('installflow.calendar', ['month' => Carbon::parse($data['scheduled_at'])->format('Y-m')])->with('success', 'Termin montażu został zapisany w kalendarzu.');
    }
}
