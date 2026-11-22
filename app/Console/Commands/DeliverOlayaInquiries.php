<?php

namespace App\Console\Commands;

use App\Models\Inquiry;
use App\Services\OlayaCrmDelivery;
use Illuminate\Console\Command;

class DeliverOlayaInquiries extends Command
{
    protected $signature = 'kobcode:deliver-inquiries {--inquiry= : Wyślij lub ponów jedno wskazane zapytanie}';
    protected $description = 'Przekaż oczekujące zapytania do Sprzedaż → Zapytania w OLAYA';

    public function handle(OlayaCrmDelivery $delivery): int
    {
        if (! config('services.olaya_crm.enabled')) {
            $this->error('Integracja wyłączona. Ustaw OLAYA_CRM_ENABLED i dane połączenia w .env.');
            return self::FAILURE;
        }
        if ($id = $this->option('inquiry')) {
            if (! ctype_digit((string) $id) || ! Inquiry::whereKey($id)->exists()) {
                $this->error('Nie znaleziono zapytania.'); return self::FAILURE;
            }
            $sent = $delivery->send((int) $id, true);
            $this->info($sent ? 'Zapytanie przekazane.' : 'Sprawdź status przekazania w panelu KOBCODE.');
            return Inquiry::find($id)?->olaya_status === 'sent' ? self::SUCCESS : self::FAILURE;
        }
        $sent = 0;
        $ids = Inquiry::where('olaya_status', 'pending')->whereNotNull('olaya_request_id')
            ->where('olaya_next_attempt_at', '<=', now())->orderBy('id')->limit(50)->pluck('id');
        foreach ($ids as $id) if ($delivery->send($id)) $sent++;
        $this->info('Przekazane zapytania: '.$sent.'. Sprawdzone: '.$ids->count().'.');
        return self::SUCCESS;
    }
}
