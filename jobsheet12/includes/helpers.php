<?php
// jobsheet11/includes/helpers.php - Fungsi Pembantu Sanitasi dan Keamanan Output
if (!function_exists('e')) {
    /**
     * Membungkus output data yang berasal dari input pengguna sebelum
     * dicetak ke HTML, untuk mencegah kerentanan XSS (Cross-Site Scripting).
     */
    function e($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
