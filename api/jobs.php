<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');
$pdo = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $status = $_GET['status'] ?? null;
        $search = $_GET['search'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("
                SELECT j.*, 
                       c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
                       v.brand as vehicle_brand, v.model as vehicle_model, v.plate_number, v.year as vehicle_year, v.odometer,
                       e.name as mechanic_name, e.role as mechanic_role
                FROM jobs j
                LEFT JOIN customers c ON j.customer_id = c.id
                LEFT JOIN vehicles v ON j.vehicle_id = v.id
                LEFT JOIN employees e ON j.employee_id = e.id
                WHERE j.id = ?
            ");
            $stmt->execute([$id]);
            $job = $stmt->fetch();
            if (!$job) {
                jsonResponse(['error' => 'Job not found'], 404);
            }

            // Get job parts
            $partStmt = $pdo->prepare("
                SELECT jp.*, i.name as part_name, i.sku, i.unit
                FROM job_parts jp
                JOIN inventory i ON jp.inventory_id = i.id
                WHERE jp.job_id = ?
            ");
            $partStmt->execute([$id]);
            $job['parts_list'] = $partStmt->fetchAll();

            jsonResponse(['success' => true, 'data' => $job]);
        }

        $query = "
            SELECT j.*, 
                   c.name as customer_name, c.phone as customer_phone,
                   v.brand as vehicle_brand, v.model as vehicle_model, v.plate_number,
                   e.name as mechanic_name
            FROM jobs j
            LEFT JOIN customers c ON j.customer_id = c.id
            LEFT JOIN vehicles v ON j.vehicle_id = v.id
            LEFT JOIN employees e ON j.employee_id = e.id
            WHERE 1=1
        ";
        $params = [];

        if ($status && $status !== 'all') {
            $query .= " AND j.status = ?";
            $params[] = $status;
        }

        if ($search) {
            $query .= " AND (j.job_code LIKE ? OR c.name LIKE ? OR v.model LIKE ? OR v.plate_number LIKE ?)";
            $wildcard = "%$search%";
            $params = array_merge($params, [$wildcard, $wildcard, $wildcard, $wildcard]);
        }

        $query .= " ORDER BY j.id DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $jobs = $stmt->fetchAll();

        jsonResponse(['success' => true, 'data' => $jobs]);
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;
        $action = $input['action'] ?? 'create';

        if ($action === 'create') {
            // Generate Job Code
            $lastIdStmt = $pdo->query("SELECT MAX(id) as max_id FROM jobs");
            $nextNum = ((int)$lastIdStmt->fetch()['max_id']) + 101;
            $jobCode = $input['job_code'] ?? ('J-' . $nextNum);

            $customerId = (int)($input['customer_id'] ?? 0);
            $vehicleId = (int)($input['vehicle_id'] ?? 0);

            // Quick create customer & vehicle if needed
            if (!$customerId && !empty($input['new_customer_name'])) {
                $cStmt = $pdo->prepare("INSERT INTO customers (name, phone) VALUES (?, ?)");
                $cStmt->execute([$input['new_customer_name'], $input['new_customer_phone'] ?? '']);
                $customerId = (int)$pdo->lastInsertId();

                $vStmt = $pdo->prepare("INSERT INTO vehicles (customer_id, brand, model, plate_number) VALUES (?, ?, ?, ?)");
                $vStmt->execute([$customerId, $input['new_vehicle_brand'] ?? 'Vehicle', $input['new_vehicle_model'] ?? 'Car', $input['new_vehicle_plate'] ?? '-']);
                $vehicleId = (int)$pdo->lastInsertId();
            }

            $labor = (float)($input['labor_cost'] ?? 0);
            $parts = (float)($input['parts_cost'] ?? 0);
            $taxRate = (float)getSetting('tax_rate', '11');
            $tax = round(($labor + $parts) * ($taxRate / 100));
            $total = $labor + $parts + $tax;

            $stmt = $pdo->prepare("
                INSERT INTO jobs (
                    job_code, customer_id, vehicle_id, employee_id, service_type,
                    description, priority, status, date_received, date_promised,
                    labor_cost, parts_cost, tax_amount, total_cost, payment_status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $jobCode,
                $customerId ?: 1,
                $vehicleId ?: 1,
                (int)($input['employee_id'] ?? 1),
                $input['service_type'] ?? 'General Service',
                $input['description'] ?? '',
                $input['priority'] ?? 'Normal',
                $input['status'] ?? 'Pending',
                $input['date_received'] ?? date('Y-m-d'),
                $input['date_promised'] ?? date('Y-m-d', strtotime('+2 days')),
                $labor,
                $parts,
                $tax,
                $total,
                $input['payment_status'] ?? 'Unpaid',
                $input['notes'] ?? ''
            ]);

            $jobId = (int)$pdo->lastInsertId();

            // Insert parts if provided
            if (!empty($input['parts']) && is_array($input['parts'])) {
                $itemStmt = $pdo->prepare("INSERT INTO job_parts (job_id, inventory_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $deductStmt = $pdo->prepare("UPDATE inventory SET stock = MAX(0, stock - ?) WHERE id = ?");

                foreach ($input['parts'] as $p) {
                    $invId = (int)($p['id'] ?? $p['inventory_id']);
                    $qty = max(1, (int)($p['quantity'] ?? 1));
                    $price = (float)($p['price'] ?? 0);
                    $sub = $qty * $price;

                    $itemStmt->execute([$jobId, $invId, $qty, $price, $sub]);
                    $deductStmt->execute([$qty, $invId]);
                }
            }

            jsonResponse(['success' => true, 'message' => 'Job Order created successfully', 'job_id' => $jobId, 'job_code' => $jobCode]);
        }

        if ($action === 'update_status') {
            $jobId = (int)($input['job_id'] ?? 0);
            $newStatus = $input['status'] ?? '';
            $valid = ['Pending', 'In Progress', 'Pending Parts', 'Ready for Pickup', 'Completed', 'Cancelled'];

            if (!$jobId || !in_array($newStatus, $valid)) {
                jsonResponse(['error' => 'Invalid parameters'], 400);
            }

            $dateCompleted = ($newStatus === 'Completed') ? date('Y-m-d') : null;
            $stmt = $pdo->prepare("UPDATE jobs SET status = ?, date_completed = COALESCE(?, date_completed) WHERE id = ?");
            $stmt->execute([$newStatus, $dateCompleted, $jobId]);

            jsonResponse(['success' => true, 'message' => "Job status updated to $newStatus"]);
        }

        if ($action === 'quick_rename') {
            $jobId = (int)($input['job_id'] ?? 0);
            $newName = trim($input['name'] ?? '');
            if (!$jobId || !$newName) {
                jsonResponse(['error' => 'Nama tidak boleh kosong'], 400);
            }
            $cId = (int)$pdo->query("SELECT customer_id FROM jobs WHERE id = $jobId")->fetchColumn();
            if ($cId) {
                $pdo->prepare("UPDATE customers SET name = ? WHERE id = ?")->execute([$newName, $cId]);
                jsonResponse(['success' => true, 'message' => 'Nama pelanggan berhasil diubah menjadi ' . $newName, 'name' => $newName]);
            }
            jsonResponse(['error' => 'Pelanggan tidak ditemukan'], 404);
        }

        if ($action === 'update') {
            $jobId = (int)($input['id'] ?? 0);
            if (!$jobId) {
                jsonResponse(['error' => 'Missing job ID'], 400);
            }

            // Also update customer name / vehicle if passed
            $jobInfo = $pdo->query("SELECT customer_id, vehicle_id FROM jobs WHERE id = $jobId")->fetch();
            if ($jobInfo) {
                if (!empty($input['customer_name'])) {
                    $pdo->prepare("UPDATE customers SET name = ?, phone = COALESCE(?, phone) WHERE id = ?")
                        ->execute([$input['customer_name'], $input['customer_phone'] ?? null, $jobInfo['customer_id']]);
                }
                if (!empty($input['vehicle_model']) || !empty($input['plate_number'])) {
                    $pdo->prepare("UPDATE vehicles SET model = COALESCE(?, model), brand = COALESCE(?, brand), plate_number = COALESCE(?, plate_number) WHERE id = ?")
                        ->execute([
                            $input['vehicle_model'] ?? null,
                            $input['vehicle_brand'] ?? null,
                            $input['plate_number'] ?? null,
                            $jobInfo['vehicle_id']
                        ]);
                }
            }

            $labor = (float)($input['labor_cost'] ?? 0);
            $parts = (float)($input['parts_cost'] ?? 0);
            $taxRate = (float)getSetting('tax_rate', '11');
            $tax = round(($labor + $parts) * ($taxRate / 100));
            $total = $labor + $parts + $tax;

            $stmt = $pdo->prepare("
                UPDATE jobs SET
                    service_type = COALESCE(?, service_type),
                    description = COALESCE(?, description),
                    priority = COALESCE(?, priority),
                    status = COALESCE(?, status),
                    employee_id = COALESCE(?, employee_id),
                    date_promised = COALESCE(?, date_promised),
                    labor_cost = ?,
                    parts_cost = ?,
                    tax_amount = ?,
                    total_cost = ?,
                    payment_status = COALESCE(?, payment_status),
                    payment_method = COALESCE(?, payment_method),
                    notes = COALESCE(?, notes)
                WHERE id = ?
            ");
            $stmt->execute([
                $input['service_type'] ?? null,
                $input['description'] ?? null,
                $input['priority'] ?? null,
                $input['status'] ?? null,
                !empty($input['employee_id']) ? (int)$input['employee_id'] : null,
                $input['date_promised'] ?? null,
                $labor,
                $parts,
                $tax,
                $total,
                $input['payment_status'] ?? null,
                $input['payment_method'] ?? null,
                $input['notes'] ?? null,
                $jobId
            ]);

            jsonResponse(['success' => true, 'message' => 'Data berhasil diperbarui']);
        }

        if ($action === 'delete') {
            $jobId = (int)($input['id'] ?? 0);
            if ($jobId) {
                $pdo->prepare("DELETE FROM job_parts WHERE job_id = ?")->execute([$jobId]);
                $pdo->prepare("DELETE FROM jobs WHERE id = ?")->execute([$jobId]);
                jsonResponse(['success' => true, 'message' => 'Job deleted']);
            }
            jsonResponse(['error' => 'Missing ID'], 400);
        }

        jsonResponse(['error' => 'Unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

