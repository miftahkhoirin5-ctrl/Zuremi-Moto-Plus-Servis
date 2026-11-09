<?php
/**
 * Application Helper Functions & Configuration
 */

if (session_status() === PHP_SESSION_NONE) {
    $sessDir = __DIR__ . '/../database/sessions';
    if (!is_dir($sessDir)) {
        @mkdir($sessDir, 0777, true);
    }
    if (is_dir($sessDir) && is_writable($sessDir)) {
        @session_save_path($sessDir);
    }
    @session_start();
}

require_once __DIR__ . '/database.php';

function getSetting(string $key, $default = ''): string {
    static $settingsCache = null;
    if ($settingsCache === null) {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settingsCache = [];
        while ($row = $stmt->fetch()) {
            $settingsCache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settingsCache[$key] ?? $default;
}

function updateSetting(string $key, string $value): void {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (:k, :v)");
    $stmt->execute([':k' => $key, ':v' => $value]);
}

function formatCurrency(float $amount): string {
    $symbol = getSetting('currency_symbol', 'Rp');
    if ($symbol === '₹') {
        // Indian Lakh / Rupee notation
        if ($amount >= 100000) {
            return '₹' . round($amount / 100000, 1) . 'L';
        }
        return '₹' . number_format($amount, 0, '.', ',');
    } elseif ($symbol === '$') {
        return '$' . number_format($amount, 2, '.', ',');
    } else {
        // Indonesian Rupiah (Rp)
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

function formatStatusBadge(string $status): string {
    $status = trim($status);
    $map = [
        'Pending' => ['class' => 'badge-pending', 'label' => 'Menunggu Antrean'],
        'In Progress' => ['class' => 'badge-in-progress', 'label' => 'Sedang Dikerjakan'],
        'Pending Parts' => ['class' => 'badge-pending-parts', 'label' => 'Tunggu Suku Cadang'],
        'Ready for Pickup' => ['class' => 'badge-pickup', 'label' => 'Siap Diambil'],
        'Completed' => ['class' => 'badge-completed', 'label' => 'Selesai'],
        'Cancelled' => ['class' => 'badge-cancelled', 'label' => 'Dibatalkan'],
        'Scheduled' => ['class' => 'badge-scheduled', 'label' => 'Terjadwal'],
        'Confirmed' => ['class' => 'badge-confirmed', 'label' => 'Terkonfirmasi']
    ];
    $cfg = $map[$status] ?? ['class' => 'badge-default', 'label' => $status];
    return '<span class="status-badge ' . $cfg['class'] . '">' . htmlspecialchars($cfg['label']) . '</span>';
}

function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
