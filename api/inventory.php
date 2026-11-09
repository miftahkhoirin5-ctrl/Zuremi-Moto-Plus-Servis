<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');
$pdo = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        $lowStock = isset($_GET['low_stock']) ? true : false;

        $query = "SELECT * FROM inventory WHERE 1=1";
        $params = [];

        if ($category && $category !== 'all') {
            $query .= " AND category = ?";
            $params[] = $category;
        }

        if ($lowStock) {
            $query .= " AND stock <= min_stock";
        }

        if ($search) {
            $query .= " AND (sku LIKE ? OR name LIKE ? OR category LIKE ?)";
            $wildcard = "%$search%";
            $params = array_merge($params, [$wildcard, $wildcard, $wildcard]);
        }

        $query .= " ORDER BY name ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        // Get categories
        $cats = $pdo->query("SELECT DISTINCT category FROM inventory ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);

        jsonResponse([
            'success' => true,
            'data' => $items,
            'categories' => $cats
        ]);
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;
        $action = $input['action'] ?? 'create';

        if ($action === 'create') {
            $sku = trim($input['sku'] ?? '');
            if (!$sku) {
                $sku = 'PRT-' . strtoupper(substr(uniqid(), -6));
            }

            $stmt = $pdo->prepare("
                INSERT INTO inventory (sku, name, category, stock, min_stock, buy_price, sell_price, unit)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $sku,
                $input['name'] ?? 'Spare Part',
                $input['category'] ?? 'General',
                (int)($input['stock'] ?? 0),
                (int)($input['min_stock'] ?? 5),
                (float)($input['buy_price'] ?? 0),
                (float)($input['sell_price'] ?? 0),
                $input['unit'] ?? 'pcs'
            ]);

            jsonResponse(['success' => true, 'message' => 'Item added to inventory', 'id' => (int)$pdo->lastInsertId()]);
        }

        if ($action === 'adjust_stock') {
            $id = (int)($input['id'] ?? 0);
            $amount = (int)($input['amount'] ?? 0); // can be positive or negative
            if (!$id || $amount === 0) {
                jsonResponse(['error' => 'Invalid parameters'], 400);
            }

            $pdo->prepare("UPDATE inventory SET stock = MAX(0, stock + ?) WHERE id = ?")->execute([$amount, $id]);
            $newStock = $pdo->query("SELECT stock FROM inventory WHERE id = $id")->fetchColumn();

            jsonResponse(['success' => true, 'message' => 'Stock updated', 'new_stock' => $newStock]);
        }

        if ($action === 'update') {
            $id = (int)($input['id'] ?? 0);
            if (!$id) {
                jsonResponse(['error' => 'Item ID required'], 400);
            }

            $stmt = $pdo->prepare("
                UPDATE inventory SET
                    sku = ?, name = ?, category = ?, stock = ?, min_stock = ?,
                    buy_price = ?, sell_price = ?, unit = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $input['sku'],
                $input['name'],
                $input['category'],
                (int)$input['stock'],
                (int)$input['min_stock'],
                (float)$input['buy_price'],
                (float)$input['sell_price'],
                $input['unit'] ?? 'pcs',
                $id
            ]);

            jsonResponse(['success' => true, 'message' => 'Item updated']);
        }

        if ($action === 'delete') {
            $id = (int)($input['id'] ?? 0);
            $pdo->prepare("DELETE FROM inventory WHERE id = ?")->execute([$id]);
            jsonResponse(['success' => true, 'message' => 'Item deleted']);
        }

        jsonResponse(['error' => 'Unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

