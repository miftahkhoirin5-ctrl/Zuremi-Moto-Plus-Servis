<?php
/**
 * Database Configuration & Connection Manager
 * Supports SQLite (Default, Zero-Config, Guaranteed to work) and MySQL.
 */

class Database {
    private static ?PDO $instance = null;
    private static string $driver = 'sqlite';

    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $dbDir = __DIR__ . '/../database';
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0777, true);
        }
        $sqliteFile = $dbDir . '/bengkel.sqlite';

        try {
            self::$instance = new PDO("sqlite:" . $sqliteFile, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5
            ]);
            self::$driver = 'sqlite';
            self::initSchema(self::$instance);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }

        return self::$instance;
    }

    private static function initSchema(PDO $pdo): void {
        // Create tables if not exist
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT UNIQUE,
                setting_value TEXT
            );

            CREATE TABLE IF NOT EXISTS customers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                phone TEXT,
                email TEXT,
                address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS vehicles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                customer_id INTEGER,
                brand TEXT,
                model TEXT NOT NULL,
                plate_number TEXT NOT NULL,
                year INTEGER,
                color TEXT,
                vin TEXT,
                odometer INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS employees (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                role TEXT NOT NULL,
                phone TEXT,
                email TEXT,
                specialization TEXT,
                status TEXT DEFAULT 'Active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS inventory (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sku TEXT UNIQUE NOT NULL,
                name TEXT NOT NULL,
                category TEXT NOT NULL,
                stock INTEGER DEFAULT 0,
                min_stock INTEGER DEFAULT 5,
                buy_price REAL DEFAULT 0,
                sell_price REAL DEFAULT 0,
                unit TEXT DEFAULT 'pcs',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS jobs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                job_code TEXT UNIQUE NOT NULL,
                customer_id INTEGER,
                vehicle_id INTEGER,
                employee_id INTEGER,
                service_type TEXT NOT NULL,
                description TEXT,
                priority TEXT DEFAULT 'Normal',
                status TEXT DEFAULT 'Pending',
                date_received DATE NOT NULL,
                date_promised DATE,
                date_completed DATE,
                labor_cost REAL DEFAULT 0,
                parts_cost REAL DEFAULT 0,
                tax_amount REAL DEFAULT 0,
                total_cost REAL DEFAULT 0,
                payment_status TEXT DEFAULT 'Unpaid',
                payment_method TEXT,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (customer_id) REFERENCES customers(id),
                FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
                FOREIGN KEY (employee_id) REFERENCES employees(id)
            );

            CREATE TABLE IF NOT EXISTS job_parts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                job_id INTEGER NOT NULL,
                inventory_id INTEGER NOT NULL,
                quantity INTEGER DEFAULT 1,
                unit_price REAL DEFAULT 0,
                subtotal REAL DEFAULT 0,
                FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
                FOREIGN KEY (inventory_id) REFERENCES inventory(id)
            );

            CREATE TABLE IF NOT EXISTS appointments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                customer_name TEXT NOT NULL,
                customer_phone TEXT,
                vehicle_info TEXT,
                appointment_date DATE NOT NULL,
                appointment_time TEXT NOT NULL,
                delivery_type TEXT DEFAULT 'Customer Vehicle Pick-Up',
                loaner_needed INTEGER DEFAULT 0,
                description TEXT,
                status TEXT DEFAULT 'Scheduled',
                job_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (job_id) REFERENCES jobs(id)
            );
        ");

        // Check if settings seeded
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM settings");
        if ($stmt->fetch()['cnt'] == 0) {
            require_once __DIR__ . '/../database/seed.php';
            seedDatabase($pdo);
        }
    }
}

