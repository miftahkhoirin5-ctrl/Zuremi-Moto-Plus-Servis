<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');
$pdo = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("
            SELECT e.*, COUNT(j.id) as active_jobs
            FROM employees e
            LEFT JOIN jobs j ON e.id = j.employee_id AND j.status IN ('In Progress', 'Pending')
            GROUP BY e.id
            ORDER BY e.id ASC
        ");
        $employees = $stmt->fetchAll();
        jsonResponse(['success' => true, 'data' => $employees]);
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;
        $action = $input['action'] ?? 'create';

        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO employees (name, role, phone, email, specialization, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['name'] ?? 'Staff',
                $input['role'] ?? 'Technician',
                $input['phone'] ?? '',
                $input['email'] ?? '',
                $input['specialization'] ?? '',
                $input['status'] ?? 'Active'
            ]);
            jsonResponse(['success' => true, 'message' => 'Employee added', 'id' => (int)$pdo->lastInsertId()]);
        }

        if ($action === 'update') {
            $id = (int)($input['id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE employees SET name = ?, role = ?, phone = ?, email = ?, specialization = ?, status = ? WHERE id = ?");
            $stmt->execute([
                $input['name'], $input['role'], $input['phone'] ?? '',
                $input['email'] ?? '', $input['specialization'] ?? '',
                $input['status'] ?? 'Active', $id
            ]);
            jsonResponse(['success' => true, 'message' => 'Employee updated']);
        }

        if ($action === 'delete') {
            $id = (int)($input['id'] ?? 0);
            $pdo->prepare("DELETE FROM employees WHERE id = ?")->execute([$id]);
            jsonResponse(['success' => true, 'message' => 'Employee deleted']);
        }

        jsonResponse(['error' => 'Unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

