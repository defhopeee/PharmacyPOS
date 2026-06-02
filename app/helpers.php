<?php

use Illuminate\Support\Carbon;

if (! function_exists('asset_v')) {
    /**
     * Versioned asset URL — appends the file's last-modified time so browsers
     * always pick up CSS/JS changes instead of serving a stale cached copy.
     */
    function asset_v(string $path): string
    {
        $full = public_path($path);
        $version = is_file($full) ? filemtime($full) : '1';

        return asset($path).'?v='.$version;
    }
}

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
