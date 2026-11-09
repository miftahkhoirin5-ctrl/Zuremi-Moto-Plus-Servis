<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');
$pdo = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        jsonResponse(['success' => true, 'data' => $settings]);
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;
        $action = $input['action'] ?? 'update';

        if ($action === 'set_theme') {
            $theme = in_array($input['theme'] ?? '', ['dark', 'light']) ? $input['theme'] : 'dark';
            updateSetting('theme', $theme);
            jsonResponse(['success' => true, 'theme' => $theme]);
        }

        if ($action === 'update') {
            foreach ($input as $k => $v) {
                if ($k !== 'action') {
                    updateSetting($k, (string)$v);
                }
            }
            jsonResponse(['success' => true, 'message' => 'Settings updated successfully']);
        }

        if ($action === 'localize_indonesia') {
            $nameMap = [
                'Rajesh Sharma' => ['Budi Santoso', 'Toyota Avanza 1.5 Veloz', 'B 1011 BS'],
                'Priya Patel' => ['Siti Rahmawati', 'Honda Brio RS', 'B 2102 SR'],
                'Amit Verma' => ['Agus Pratama', 'Honda City RS Sedan', 'B 3103 AP'],
                'Suresh Gupta' => ['Hendra Setiawan', 'Toyota Kijang Innova Reborn', 'B 4104 HS'],
                'Deepika Rao' => ['Dewi Lestari', 'Ford EcoSport Titanium', 'B 5105 DL'],
                'Rajesh S.' => ['Rizky Saputra', 'Suzuki Baleno Hatchback', 'B 6106 RS'],
                'Anita D.' => ['Anita Damayanti', 'Wuling Alvez / EV', 'B 7107 AD'],
                'Vikram K.' => ['Vicky Kurniawan', 'Mitsubishi Pajero Sport', 'B 8108 VK'],
                'Neha P.' => ['Nurul Putri', 'Toyota Yaris GR Sport', 'B 9109 NP'],
                'Rahul M.' => ['Raditya Mahendra', 'Toyota Raize 1.0 Turbo', 'B 1110 RM'],
                'John Karsley' => ['Joko Susanto', 'Toyota Fortuner 2.8 GR', 'B 1234 JS'],
                'Amelia Cabal' => ['Amalia Cahyani', 'Honda CR-V 1.5 Turbo', 'B 5678 AC'],
                'Leo Knight' => ['Leo Nugroho', 'Mitsubishi Xpander Ultimate', 'B 9012 LN'],
                'Tania Perfilveya' => ['Tania Permata', 'BMW 320i Sport', 'B 3456 TP']
            ];

            foreach ($nameMap as $old => $new) {
                $cId = $pdo->query("SELECT id FROM customers WHERE name = " . $pdo->quote($old))->fetchColumn();
                if ($cId) {
                    $pdo->prepare("UPDATE customers SET name = ? WHERE id = ?")->execute([$new[0], $cId]);
                    $pdo->prepare("UPDATE vehicles SET model = ?, plate_number = ? WHERE customer_id = ?")->execute([$new[1], $new[2], $cId]);
                }
                $pdo->prepare("UPDATE appointments SET customer_name = ? WHERE customer_name = ?")->execute([$new[0], $old]);
            }

            jsonResponse(['success' => true, 'message' => 'Semua nama demo berhasil diubah menjadi nama Indonesia']);
        }

        if ($action === 'reset_database') {
            // Drop and reseed
            $pdo->exec("
                DROP TABLE IF EXISTS job_parts;
                DROP TABLE IF EXISTS jobs;
                DROP TABLE IF EXISTS appointments;
                DROP TABLE IF EXISTS inventory;
                DROP TABLE IF EXISTS vehicles;
                DROP TABLE IF EXISTS customers;
                DROP TABLE IF EXISTS employees;
                DROP TABLE IF EXISTS settings;
            ");
            // Re-run database schema & seed
            require_once __DIR__ . '/../database/seed.php';
            // Schema re-run
            $dbClass = new ReflectionClass('Database');
            $method = $dbClass->getMethod('initSchema');
            $method->setAccessible(true);
            $method->invoke(null, $pdo);

            jsonResponse(['success' => true, 'message' => 'Database successfully reset and reseeded with demo data']);
        }

        jsonResponse(['error' => 'Unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

