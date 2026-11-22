<?php

namespace App\Demo\InstallFlow;

final class Money
{
    public static function hundredths(string|int|float $value): int
    {
        $parts = explode('.', str_replace(',', '.', (string) $value), 2);
        return ((int) $parts[0] * 100) + (int) str_pad($parts[1] ?? '', 2, '0');
    }

    public static function calculate(array $items): array
    {
        $lines = [];
        foreach ($items as $item) {
            $quantity = self::hundredths($item['quantity']);
            $unit = self::hundredths($item['unit_price']);
            $lines[] = ['description' => $item['description'], 'quantity_hundredths' => $quantity,
                'unit_cents' => $unit, 'net_cents' => intdiv($unit * $quantity + 50, 100)];
        }
        $net = array_sum(array_column($lines, 'net_cents'));
        $vat = intdiv($net * 23 + 50, 100);
        return ['items' => $lines, 'net_cents' => $net, 'vat_cents' => $vat, 'gross_cents' => $net + $vat];
    }

    public static function format(int $cents): string
    {
        return number_format($cents / 100, 2, ',', ' ').' zł';
    }
}
