<?php

namespace Database\Seeders;

use App\Demo\InstallFlow\Money;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstallFlowSeeder extends Seeder
{
    public function run(): void
    {
        $db = DB::connection('installflow');
        $now = now();
        $db->transaction(function () use ($db, $now) {
            $staff = ['Adam Zieliński', 'Marta Wolska', 'Piotr Maj', 'Julia Gaj', 'Tomasz Lis', 'Oskar Polny'];
            foreach ($staff as $i => $name) {
                $db->table('employees')->insert(['name' => $name, 'role' => ['Kierownik montaży', 'Specjalistka HVAC', 'Monter klimatyzacji', 'Doradczyni techniczna', 'Monter pomp ciepła', 'Serwisant'][$i],
                    'email' => 'pracownik'.($i + 1).'@installflow.test', 'phone' => '+48 000 100 00'.$i,
                    'availability' => $i === 5 ? 'leave' : 'available', 'created_at' => $now, 'updated_at' => $now]);
            }
            $first = ['Anna', 'Jan', 'Ewa', 'Michał', 'Alicja', 'Paweł', 'Natalia', 'Karol', 'Zofia', 'Filip'];
            $last = ['Testowa', 'Przykładowy', 'Demonstracyjna', 'Modelowy', 'Pokazowa', 'Wzorowy'];
            for ($i = 1; $i <= 30; $i++) {
                $name = $i % 4 === 0 ? 'Firma Demo '.str_pad((string) $i, 2, '0', STR_PAD_LEFT) : $first[($i - 1) % 10].' '.$last[($i - 1) % 6];
                $db->table('clients')->insert(['name' => $name, 'type' => $i % 4 === 0 ? 'company' : 'person',
                    'email' => 'klient'.$i.'@installflow.test', 'phone' => '+48 000 200 '.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'city' => ['Radom', 'Warszawa', 'Kielce', 'Lublin', 'Łódź'][($i - 1) % 5],
                    'address' => 'ul. Demonstracyjna '.$i, 'notes' => 'Fikcyjny klient przygotowany wyłącznie do demonstracji.',
                    'created_at' => $now->copy()->subDays(45 - $i), 'updated_at' => $now]);
                if ($i <= 12) $db->table('contacts')->insert(['client_id' => $i, 'channel' => $i % 2 ? 'phone' : 'meeting',
                    'note' => 'Rozmowa demonstracyjna: ustalono zakres instalacji i preferowany termin wizji lokalnej.', 'contacted_at' => $now->copy()->subDays($i % 7)]);
            }
            $titles = ['Montaż klimatyzacji split', 'Instalacja pompy ciepła', 'Przegląd instalacji HVAC', 'Montaż klimatyzacji multisplit', 'Serwis i czyszczenie klimatyzacji'];
            for ($i = 1; $i <= 18; $i++) {
                $type = ($i - 1) % 5;
                $totals = Money::calculate([
                    ['description' => ['Jednostka klimatyzacyjna 3,5 kW', 'Pompa ciepła powietrze–woda', 'Przegląd i pomiary instalacji', 'Zestaw klimatyzacji multisplit', 'Czyszczenie i dezynfekcja'][$type], 'quantity' => '1', 'unit_price' => (string) [3200, 24500, 450, 7200, 280][$type]],
                    ['description' => 'Materiały instalacyjne i robocizna', 'quantity' => '1', 'unit_price' => (string) [1500, 6500, 150, 2300, 120][$type]],
                ]);
                $created = $now->copy()->subDays(2 * $i);
                $status = $i <= 10 ? 'accepted' : ['draft', 'sent', 'rejected', 'sent'][($i - 11) % 4];
                $db->table('offers')->insert(['id' => $i, 'client_id' => $i, 'title' => $titles[$type], 'status' => $status,
                    'valid_until' => $now->copy()->addDays(14)->toDateString(), 'notes' => 'Przykładowa oferta. Dane i ceny demonstracyjne.',
                    'net_cents' => $totals['net_cents'], 'vat_cents' => $totals['vat_cents'], 'gross_cents' => $totals['gross_cents'], 'created_at' => $created, 'updated_at' => $created]);
                foreach ($totals['items'] as $line) $db->table('offer_items')->insert($line + ['offer_id' => $i]);
            }
            for ($i = 1; $i <= 24; $i++) {
                $status = ['new', 'scheduled', 'in_progress', 'completed', 'scheduled', 'completed', 'cancelled', 'completed'][($i - 1) % 8];
                $date = in_array($status, ['completed', 'cancelled']) ? $now->copy()->subDays($i * 3) : $now->copy()->addDays(($i - 1) % 12);
                $date->setTime(8 + ($i % 4) * 2, 0);
                $offer = $i <= 8 ? $db->table('offers')->find($i) : null;
                $value = $offer?->gross_cents ?? Money::calculate([['description' => 'Usługa', 'quantity' => '1', 'unit_price' => (string) [4700, 31000, 600, 9500, 400][($i - 1) % 5]]])['gross_cents'];
                $db->table('orders')->insert(['id' => $i, 'client_id' => $offer?->client_id ?? $i,
                    'employee_id' => ($i - 1) % 5 + 1, 'offer_id' => $offer?->id, 'title' => $offer?->title ?? $titles[($i - 1) % 5],
                    'status' => $status, 'scheduled_at' => $status === 'new' ? null : $date,
                    'completed_at' => $status === 'completed' ? $date : null, 'gross_cents' => $value,
                    'notes' => 'Przykładowe zlecenie. Przed wizytą potwierdź dostęp do miejsca montażu.',
                    'created_at' => $now->copy()->subDays(40), 'updated_at' => $now->copy()->subHours($i)]);
                $db->table('order_events')->insert(['order_id' => $i, 'message' => 'Przygotowano zlecenie demonstracyjne.', 'created_at' => $now->copy()->subHours($i)]);
            }
        });
    }
}
