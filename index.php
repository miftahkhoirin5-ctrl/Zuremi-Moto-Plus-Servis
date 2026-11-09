<?php
/**
 * MotoTrust & Bayou Automotive - Workshop & Dealer Service System
 * Main Controller / Router
 */

require_once __DIR__ . '/config/app.php';

$allowedPages = [
    'dashboard' => 'Dashboard Bengkel',
    'jobs' => 'Daftar Perintah Kerja (SPK)',
    'calendar' => 'Jadwal Servis (Bayou Calendar)',
    'customers' => 'Data Pelanggan & Kendaraan',
    'inventory' => 'Gudang & Suku Cadang',
    'employees' => 'Mekanik & Karyawan',
    'reports' => 'Laporan & Analitik',
    'invoice' => 'Faktur & Nota Servis',
    'settings' => 'Pengaturan Bengkel'
];

$page = $_GET['page'] ?? 'dashboard';
if (!array_key_exists($page, $allowedPages)) {
    $page = 'dashboard';
}

// Render Page Layout
include __DIR__ . '/views/layout/header.php';
include __DIR__ . '/views/' . $page . '.php';
include __DIR__ . '/views/layout/footer.php';

