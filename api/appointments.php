<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');
$pdo = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $month = $_GET['month'] ?? null; // format YYYY-MM
        $query = "SELECT * FROM appointments";
        $params = [];

        if ($month) {
            $query .= " WHERE appointment_date LIKE ?";
            $params[] = $month . '%';
        }

        $query .= " ORDER BY appointment_date ASC, appointment_time ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Also include service jobs from Dashboard to keep Calendar perfectly synchronized
        $jobQuery = "
            SELECT j.id as job_id, j.job_code, j.service_type, j.status, j.date_received,
                   c.name as customer_name, c.phone as customer_phone,
                   v.brand as vehicle_brand, v.model as vehicle_model, v.plate_number
            FROM jobs j
            LEFT JOIN customers c ON j.customer_id = c.id
            LEFT JOIN vehicles v ON j.vehicle_id = v.id
            WHERE 1=1
        ";
        $jobParams = [];
        if ($month) {
            $jobQuery .= " AND j.date_received LIKE ?";
            $jobParams[] = $month . '%';
        }
        $jobQuery .= " ORDER BY j.date_received ASC, j.id ASC";
        $jStmt = $pdo->prepare($jobQuery);
        $jStmt->execute($jobParams);
        $jobList = $jStmt->fetchAll();

        foreach ($jobList as $j) {
            $rows[] = [
                'id' => 9000 + (int)$j['job_id'],
                'customer_name' => $j['customer_name'] ?: 'Pelanggan',
                'customer_phone' => $j['customer_phone'] ?? '',
                'vehicle_info' => trim(($j['vehicle_brand'] ?? '') . ' ' . ($j['vehicle_model'] ?? '') . ' (' . ($j['plate_number'] ?? '-') . ')'),
                'appointment_date' => $j['date_received'],
                'appointment_time' => $j['job_code'],
                'delivery_type' => 'SPK ' . $j['status'],
                'loaner_needed' => 0,
                'description' => $j['service_type'],
                'status' => $j['status'],
                'is_job' => true,
                'job_id' => $j['job_id']
            ];
        }

        jsonResponse(['success' => true, 'data' => $rows]);
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;
        $action = $input['action'] ?? 'create';

        if ($action === 'create') {
            $name = trim($input['customer_name'] ?? '');
            if (!$name) {
                jsonResponse(['error' => 'Customer name is required'], 400);
            }

            $date = $input['appointment_date'] ?? date('Y-m-d');
            $time = $input['appointment_time'] ?? '10:00 AM';
            $deliveryType = $input['delivery_type'] ?? 'Customer Vehicle Pick-Up';
            $loanerNeeded = !empty($input['loaner_needed']) ? 1 : 0;
            $description = $input['description'] ?? '';
            $phone = $input['customer_phone'] ?? '';
            $vehicle = $input['vehicle_info'] ?? 'Vehicle';

            $stmt = $pdo->prepare("
                INSERT INTO appointments (
                    customer_name, customer_phone, vehicle_info,
                    appointment_date, appointment_time, delivery_type,
                    loaner_needed, description, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Scheduled')
            ");
            $stmt->execute([
                $name, $phone, $vehicle, $date, $time, $deliveryType, $loanerNeeded, $description
            ]);

            jsonResponse([
                'success' => true,
                'message' => 'Appointment added successfully',
                'id' => (int)$pdo->lastInsertId()
            ]);
        }

        if ($action === 'convert_to_job') {
            $aptId = (int)($input['appointment_id'] ?? 0);
            $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
            $stmt->execute([$aptId]);
            $apt = $stmt->fetch();

            if (!$apt) {
                jsonResponse(['error' => 'Appointment not found'], 404);
            }

            // Check if customer exists or create
            $cStmt = $pdo->prepare("SELECT id FROM customers WHERE name = ? LIMIT 1");
            $cStmt->execute([$apt['customer_name']]);
            $custId = $cStmt->fetchColumn();

            if (!$custId) {
                $insCust = $pdo->prepare("INSERT INTO customers (name, phone) VALUES (?, ?)");
                $insCust->execute([$apt['customer_name'], $apt['customer_phone']]);
                $custId = (int)$pdo->lastInsertId();

                $insVeh = $pdo->prepare("INSERT INTO vehicles (customer_id, model, plate_number) VALUES (?, ?, ?)");
                $insVeh->execute([$custId, $apt['vehicle_info'] ?: 'Vehicle', 'B-' . rand(1000, 9999)]);
                $vehId = (int)$pdo->lastInsertId();
            } else {
                $vStmt = $pdo->prepare("SELECT id FROM vehicles WHERE customer_id = ? LIMIT 1");
                $vStmt->execute([$custId]);
                $vehId = $vStmt->fetchColumn() ?: 1;
            }

            // Generate Job Code
            $maxId = (int)$pdo->query("SELECT MAX(id) FROM jobs")->fetchColumn();
            $jobCode = 'J-' . ($maxId + 101);

            $insJob = $pdo->prepare("
                INSERT INTO jobs (
                    job_code, customer_id, vehicle_id, employee_id, service_type,
                    description, priority, status, date_received, date_promised,
                    labor_cost, parts_cost, tax_amount, total_cost, notes
                ) VALUES (?, ?, ?, 1, 'Service from Appointment', ?, 'Normal', 'Pending', ?, ?, 150000, 0, 16500, 166500, ?)
            ");
            $notes = "Booked via Bayou Calendar. Delivery: " . $apt['delivery_type'] . ($apt['loaner_needed'] ? " (Loaner requested)" : "");
            $insJob->execute([
                $jobCode, $custId, $vehId, $apt['description'],
                $apt['appointment_date'], $apt['appointment_date'], $notes
            ]);
            $newJobId = (int)$pdo->lastInsertId();

            // Link appointment
            $pdo->prepare("UPDATE appointments SET status = 'Confirmed', job_id = ? WHERE id = ?")->execute([$newJobId, $aptId]);

            jsonResponse([
                'success' => true,
                'message' => 'Appointment converted to Job Order ' . $jobCode,
                'job_code' => $jobCode,
                'job_id' => $newJobId
            ]);
        }

        if ($action === 'update_status') {
            $aptId = (int)($input['id'] ?? 0);
            $newStatus = $input['status'] ?? 'Scheduled';
            $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?")->execute([$newStatus, $aptId]);
            jsonResponse(['success' => true, 'message' => 'Status updated']);
        }

        if ($action === 'delete') {
            $aptId = (int)($input['id'] ?? 0);
            $pdo->prepare("DELETE FROM appointments WHERE id = ?")->execute([$aptId]);
            jsonResponse(['success' => true, 'message' => 'Appointment deleted']);
        }

        jsonResponse(['error' => 'Unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

