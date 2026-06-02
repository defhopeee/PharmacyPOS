<?php

use Illuminate\Support\Carbon;

if (! function_exists('money')) {
    /**
     * Format an amount as Kenyan Shillings.
     */
    function money($amount, bool $withSymbol = true): string
    {
        $value = number_format((float) $amount, 2);

        return $withSymbol ? 'KSh '.$value : $value;
    }
}

if (! function_exists('trendData')) {
    /**
     * Build a percentage trend between a current and previous value.
     *
     * @return array{percent: float, direction: string}
     */
    function trendData(float $current, float $previous): array
    {
        if ($previous == 0.0) {
            $percent = $current > 0 ? 100.0 : 0.0;
        } else {
            $percent = (($current - $previous) / $previous) * 100;
        }

        return [
            'percent' => round(abs($percent), 1),
            'direction' => $current >= $previous ? 'up' : 'down',
        ];
    }
}
