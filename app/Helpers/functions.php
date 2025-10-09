<?php

if (!function_exists('tanggal_indo')) {
    /**
     * Convert date to Indonesian format
     */
    function tanggal_indo($date)
    {
        if (!$date) return '';
        
        $carbon = \Carbon\Carbon::parse($date);
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $carbon->format('d') . ' ' . $bulan[$carbon->format('n')] . ' ' . $carbon->format('Y');
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format number to Indonesian currency
     */
    function format_currency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
