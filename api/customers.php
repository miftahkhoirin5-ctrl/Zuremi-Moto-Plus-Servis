<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');
$pdo = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$id]);
            $cust = $stmt->fetch();
            if (!$cust) {
                jsonResponse(['error' => 'Customer not found'], 404);
            }

            // Vehicles
            $vStmt = $pdo->prepare("SELECT * FROM vehicles WHERE customer_id = ?");
            $vStmt->execute([$id]);
            $cust['vehicles'] = $vStmt->fetchAll();

            // Jobs history
            $jStmt = $pdo->prepare("
                SELECT j.*, v.model as vehicle_model, v.plate_number
                FROM jobs j
                JOIN vehicles v ON j.vehicle_id = v.id
                WHERE j.customer_id = ?
                ORDER BY j.date_received DESC
            ");
            $jStmt->execute([$id]);
            $cust['jobs'] = $jStmt->fetchAll();

            jsonResponse(['success' => true, 'data' => $cust]);
        }

        $search = $_GET['search'] ?? null;
        $query = "
            SELECT c.*, 
                   COUNT(DISTINCT v.id) as vehicle_count,
                   COUNT(DISTINCT j.id) as job_count,
                   COALESCE(SUM(j.total_cost), 0) as total_spent
            FROM customers c
            LEFT JOIN vehicles v ON c.id = v.customer_id
            LEFT JOIN jobs j ON c.id = j.customer_id
            WHERE 1=1
        ";
        $params = [];

        if ($search) {
            $query .= " AND (c.name LIKE ? OR c.phone LIKE ? OR c.email LIKE ?)";
            $wildcard = "%$search%";
            $params = [$wildcard, $wildcard, $wildcard];
        }

        $query .= " GROUP BY c.id ORDER BY c.name ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $customers = $stmt->fetchAll();

        jsonResponse(['success' => true, 'data' => $customers]);
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;
        $action = $input['action'] ?? 'create';

        if ($action === 'create') {
            $name = trim($input['name'] ?? '');
            if (!$name) {
                jsonResponse(['error' => 'Name required'], 400);
            }

            $stmt = $pdo->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $input['phone'] ?? '', $input['email'] ?? '', $input['address'] ?? '']);
            $custId = (int)$pdo->lastInsertId();

            if (!empty($input['vehicle_model'])) {
                $vStmt = $pdo->prepare("INSERT INTO vehicles (customer_id, brand, model, plate_number, year, color, odometer) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $vStmt->execute([
                    $custId,
                    $input['vehicle_brand'] ?? 'Toyota',
                    $input['vehicle_model'],
                    $input['vehicle_plate'] ?? '-',
                    (int)($input['vehicle_year'] ?? date('Y')),
                    $input['vehicle_color'] ?? '',
                    (int)($input['odometer'] ?? 0)
                ]);
            }

            jsonResponse(['success' => true, 'message' => 'Customer added', 'id' => $custId]);
        }

        if ($action === 'add_vehicle') {
            $custId = (int)($input['customer_id'] ?? 0);
            if (!$custId) {
                jsonResponse(['error' => 'Customer ID required'], 400);
            }

            $vStmt = $pdo->prepare("INSERT INTO vehicles (customer_id, brand, model, plate_number, year, color, odometer) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $vStmt->execute([
                $custId,
                $input['brand'] ?? 'Toyota',
                $input['model'] ?? 'Vehicle',
                $input['plate_number'] ?? '-',
                (int)($input['year'] ?? date('Y')),
                $input['color'] ?? '',
                (int)($input['odometer'] ?? 0)
            ]);

            jsonResponse(['success' => true, 'message' => 'Vehicle registered', 'id' => (int)$pdo->lastInsertId()]);
        }

        if ($action === 'update') {
            $id = (int)($input['id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE customers SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?");
            $stmt->execute([$input['name'], $input['phone'] ?? '', $input['email'] ?? '', $input['address'] ?? '', $id]);
            jsonResponse(['success' => true, 'message' => 'Customer updated']);
        }

        if ($action === 'delete') {
            $id = (int)($input['id'] ?? 0);
            $pdo->prepare("DELETE FROM vehicles WHERE customer_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM customers WHERE id = ?")->execute([$id]);
            jsonResponse(['success' => true, 'message' => 'Customer deleted']);
        }

        jsonResponse(['error' => 'Unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

