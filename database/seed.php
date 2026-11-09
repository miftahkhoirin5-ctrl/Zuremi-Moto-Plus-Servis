<?php
/**
 * Seeder for Bengkel Resmi & Dealer Servis Kendaraan (MotoTrust & Bayou Automotive)
 */

function seedDatabase(PDO $pdo): void {
    // 1. Settings
    $settings = [
        'workshop_name' => 'MotoTrust & Bayou Service Dealer',
        'workshop_tagline' => 'Bengkel Resmi & Dealer Servis Kendaraan',
        'address' => 'Jl. Otomotif Raya No. 88, Kawasan Bengkel Resmi',
        'phone' => '+62 812-3456-7890',
        'email' => 'service@mototrust.id',
        'currency_symbol' => 'Rp',
        'currency_code' => 'IDR',
        'currency_multiplier' => '1000', // for formatting
        'tax_rate' => '11', // PPN 11%
        'theme' => 'dark', // 'dark' (MotoTrust) or 'light' (Bayou)
        'active_jobs_stat' => '24',
        'revenue_stat' => '124500000',
        'customers_stat' => '156',
        'pending_stat' => '8'
    ];

    $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (:k, :v)");
    foreach ($settings as $k => $v) {
        $stmt->execute([':k' => $k, ':v' => $v]);
    }

    // 2. Employees / Mechanics
    $employees = [
        ['Bambang Prasetyo', 'Kepala Bengkel (Workshop Manager)', '+62 811-1001-001', 'bambang@mototrust.id', 'Diagnosa Mesin & Manajemen', 'Active'],
        ['Angelina Devina', 'Service Advisor', '+62 811-1001-002', 'angelina@mototrust.id', 'Customer Service & Estimasi', 'Active'],
        ['Joko Susanto', 'Senior Mechanic', '+62 811-1001-003', 'joko@mototrust.id', 'Mesin, Transmisi & Kopling', 'Active'],
        ['Aris Munandar', 'Technician', '+62 811-1001-004', 'aris@mototrust.id', 'Sistem Rem & Suspensi', 'Active'],
        ['Hendra Wijaya', 'AC & Electrical Specialist', '+62 811-1001-005', 'hendra@mototrust.id', 'AC, Kelistrikan & ECU', 'Active'],
        ['Dedi Kurniawan', 'Body & Tyre Specialist', '+62 811-1001-006', 'dedi@mototrust.id', 'Spooring, Balancing & Body Repair', 'Active']
    ];

    $empStmt = $pdo->prepare("INSERT INTO employees (name, role, phone, email, specialization, status) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($employees as $e) {
        $empStmt->execute($e);
    }

    // 3. Customers & Vehicles
    $custVehicles = [
        ['Rajesh Sharma', '+62 812-9871-001', 'rajesh.sharma@example.com', 'Komp. Graha Indah Blk A/12', 'Hyundai', 'Creta 1.5 Prime', 'B 1011 RS', 2022, 'Putih Mutiara', 28500],
        ['Priya Patel', '+62 812-9871-002', 'priya.patel@example.com', 'Jl. Melati Selatan No. 45', 'Maruti Suzuki', 'Swift 1.2 GL', 'B 2102 PP', 2021, 'Merah Metalik', 34100],
        ['Amit Verma', '+62 812-9871-003', 'amit.verma@example.com', 'Apartemen Sudirman Tower B', 'Honda', 'City RS Sedan', 'B 3103 AV', 2020, 'Hitam Metalik', 49200],
        ['Suresh Gupta', '+62 812-9871-004', 'suresh.gupta@example.com', 'Jl. Flamboyan Timur No. 19', 'Toyota', 'Innova Reborn 2.4 V', 'B 4104 SG', 2021, 'Abu-abu Metalik', 62400],
        ['Deepika Rao', '+62 812-9871-005', 'deepika.rao@example.com', 'Cluster Harmoni No. 8', 'Ford', 'EcoSport Titanium', 'B 5105 DR', 2019, 'Biru Laut', 58700],
        ['Rajesh S.', '+62 812-9871-006', 'rajesh.s@example.com', 'Jl. Kenanga Baru No. 3', 'Maruti Suzuki', 'Baleno Hatchback', 'B 6106 RS', 2022, 'Silver', 21300],
        ['Anita D.', '+62 812-9871-007', 'anita.d@example.com', 'Taman Kencana Regency 14', 'Tata', 'Nexon EV / Bensin', 'B 7107 AD', 2023, 'Biru Kehijauan', 15800],
        ['Vikram K.', '+62 812-9871-008', 'vikram.k@example.com', 'Jl. Pangrango No. 71', 'Mahindra', 'Thar 4x4 Hardtop', 'B 8108 VK', 2022, 'Hitam Doff', 38900],
        ['Neha P.', '+62 812-9871-009', 'neha.p@example.com', 'Villa Indah Residence 5', 'Volkswagen', 'Polo 1.2 TSI', 'B 9109 NP', 2020, 'Putih Polar', 42100],
        ['Rahul M.', '+62 812-9871-010', 'rahul.m@example.com', 'Jl. Dahlia Barat No. 22', 'Skoda', 'Rapid 1.0 TSI', 'B 1110 RM', 2021, 'Coklat Metalik', 31500],
        ['John Karsley', '+62 813-1122-3344', 'john.karsley@example.com', 'Pondok Indah Asri B/9', 'Toyota', 'Fortuner 2.8 GR', 'B 1234 JK', 2023, 'Hitam Metalik', 18200],
        ['Amelia Cabal', '+62 813-2233-4455', 'amelia.cabal@example.com', 'Permata Hijau Residence 2', 'Honda', 'CR-V 1.5 Turbo', 'B 5678 AC', 2022, 'Silver Lunar', 24100],
        ['Leo Knight', '+62 813-3344-5566', 'leo.knight@example.com', 'Menteng Terrace 10', 'Mitsubishi', 'Pajero Sport Dakar', 'B 9012 LK', 2021, 'Putih Mutiara', 45000],
        ['Tania Perfilveya', '+62 813-4455-6677', 'tania.p@example.com', 'Kemang Village Penthouse 3', 'BMW', '320i Sport', 'B 3456 TP', 2021, 'Biru Phonic', 33400]
    ];

    $custStmt = $pdo->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)");
    $vehStmt = $pdo->prepare("INSERT INTO vehicles (customer_id, brand, model, plate_number, year, color, odometer) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $customerMap = [];
    $vehicleMap = [];

    foreach ($custVehicles as $index => $cv) {
        $custStmt->execute([$cv[0], $cv[1], $cv[2], $cv[3]]);
        $custId = $pdo->lastInsertId();
        $customerMap[$cv[0]] = $custId;

        $vehStmt->execute([$custId, $cv[4], $cv[5], $cv[6], $cv[7], $cv[8], $cv[9]]);
        $vehicleMap[$cv[0]] = $pdo->lastInsertId();
    }

    // 4. Inventory / Spare Parts (Image 3)
    $inventoryItems = [
        ['OIL-SYN-001', 'Oli Mesin Shell Helix Ultra 5W-30 (4L)', 'Oli & Pelumas', 35, 10, 380000, 520000, 'galon'],
        ['OIL-SYN-002', 'Oli Mesin Castrol Magnatec 10W-40 (4L)', 'Oli & Pelumas', 28, 8, 290000, 395000, 'galon'],
        ['OIL-TRN-003', 'Oli Transmisi Otomatis ATF T-IV (1L)', 'Oli & Pelumas', 42, 12, 75000, 115000, 'botol'],
        ['FLT-OIL-001', 'Filter Oli Mesin Original OEM', 'Filter', 65, 15, 35000, 65000, 'pcs'],
        ['FLT-AIR-002', 'Filter Udara Mesin Engine Air Filter', 'Filter', 30, 10, 85000, 135000, 'pcs'],
        ['FLT-CAB-003', 'Filter Kabin AC Karbon Aktif', 'Filter', 25, 8, 90000, 150000, 'pcs'],
        ['BRK-PAD-001', 'Brake Pad Kampas Rem Depan Ceramic', 'Sistem Rem', 18, 5, 240000, 380000, 'set'],
        ['BRK-SHU-002', 'Kampas Rem Belakang Tromol / Shoe', 'Sistem Rem', 14, 5, 175000, 290000, 'set'],
        ['BRK-DSC-003', 'Piringan Cakram Rem Depan (Brake Rotor)', 'Sistem Rem', 8, 3, 420000, 650000, 'pcs'],
        ['BRK-FLD-004', 'Minyak Rem DOT 4 Synthetic (1L)', 'Sistem Rem', 22, 6, 60000, 95000, 'botol'],
        ['SUS-SHK-001', 'Shock Absorber Gas Depan Original', 'Suspensi', 6, 2, 850000, 1250000, 'set'],
        ['SUS-SHK-002', 'Shock Absorber Belakang Standard', 'Suspensi', 8, 2, 650000, 950000, 'set'],
        ['SUS-BLL-003', 'Ball Joint & Tie Rod End Kit', 'Suspensi', 12, 4, 310000, 480000, 'set'],
        ['BAT-MF-001', 'Aki Kering Maintenance Free 12V 45Ah', 'Aki & Elektrikal', 11, 4, 720000, 980000, 'unit'],
        ['BAT-MF-002', 'Aki Kering Heavy Duty 12V 60Ah', 'Aki & Elektrikal', 7, 3, 910000, 1250000, 'unit'],
        ['SPK-PLG-001', 'Busi Iridium Tough Long Life (4 pcs)', 'Pengapian', 24, 6, 280000, 440000, 'set'],
        ['CLT-KIT-001', 'Clutch Set (Plat, Matahari & Bearing)', 'Transmisi', 5, 2, 1100000, 1650000, 'set'],
        ['TYR-RAD-001', 'Ban Radial 185/65 R15 Comfort', 'Ban & Roda', 16, 4, 620000, 850000, 'pcs'],
        ['TYR-RAD-002', 'Ban Radial 205/55 R16 Premium', 'Ban & Roda', 10, 4, 820000, 1150000, 'pcs'],
        ['AC-GAS-001', 'Freon R134a AC Mobil + Oli Kompresor', 'AC Mobil', 20, 5, 120000, 220000, 'paket'],
        ['CLT-RAD-001', 'Radiator Coolant Anti-Freeze 4L', 'Pendingin Mesin', 30, 8, 95000, 150000, 'galon'],
        ['WPR-BLD-001', 'Wiper Blade Frameless Silicone (Sepasang)', 'Aksesoris', 26, 8, 85000, 140000, 'set']
    ];

    $invStmt = $pdo->prepare("INSERT INTO inventory (sku, name, category, stock, min_stock, buy_price, sell_price, unit) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($inventoryItems as $item) {
        $invStmt->execute($item);
    }

    // 5. Jobs from Image 1:
    // J-101 | Rajesh Sharma | Hyundai Creta | In Progress | 2023-10-26 | Suspension
    // J-102 | Priya Patel | Maruti Swift | Completed | 2023-10-25 | General Service
    // J-103 | Amit Verma | Honda City | Pending Parts | 2023-10-25 | Engine Check / Brake
    // J-104 | Suresh Gupta | Toyota Innova | In Progress | 2023-10-24 | Clutch Replacement
    // J-105 | Deepika Rao | Ford EcoSport | Completed | 2023-10-23 | Tyre Replacement
    // J-106 | Rajesh S. | Maruti Baleno | Pending | 2023-10-27 | Brake Issue
    // J-107 | Anita D. | Tata Nexon | Pending | 2023-10-27 | Oil Change
    // J-108 | Vikram K. | Mahindra Thar | Pending | 2023-10-28 | Engine Noise
    // J-109 | Neha P. | Volkswagen Polo | In Progress | 2023-10-28 | AC Service
    // J-110 | Rahul M. | Skoda Rapid | In Progress | 2023-10-28 | Dent Repair

    $jobsData = [
        [
            'code' => 'J-101', 'cust' => 'Rajesh Sharma', 'service' => 'Suspension Overhaul',
            'desc' => 'Suspensi berbunyi saat melewati jalan tidak rata. Penggantian Shock Absorber Depan & Ball Joint.',
            'prio' => 'High', 'status' => 'In Progress', 'date' => '2023-10-26', 'pdate' => '2023-10-28',
            'emp' => 4, 'labor' => 350000, 'parts' => 1730000, 'paid' => 'Unpaid', 'pmeth' => null
        ],
        [
            'code' => 'J-102', 'cust' => 'Priya Patel', 'service' => 'General Service 30.000 KM',
            'desc' => 'Tune up berkala, ganti oli mesin Shell Helix Ultra, filter oli, filter udara, kuras minyak rem.',
            'prio' => 'Normal', 'status' => 'Completed', 'date' => '2023-10-25', 'pdate' => '2023-10-25',
            'emp' => 3, 'labor' => 250000, 'parts' => 815000, 'paid' => 'Paid', 'pmeth' => 'Transfer BCA'
        ],
        [
            'code' => 'J-103', 'cust' => 'Amit Verma', 'service' => 'Brake Overhaul & Sensor Check',
            'desc' => 'Indikator rem menyala dan getaran pada pedal rem. Menunggu kiriman sensor ABS dari pabrikan.',
            'prio' => 'Urgent', 'status' => 'Pending Parts', 'date' => '2023-10-25', 'pdate' => '2023-10-29',
            'emp' => 4, 'labor' => 300000, 'parts' => 1030000, 'paid' => 'Unpaid', 'pmeth' => null
        ],
        [
            'code' => 'J-104', 'cust' => 'Suresh Gupta', 'service' => 'Clutch Replacement',
            'desc' => 'Kopling selip saat tanjakan. Penggantian full clutch set (plat, dekrup, bearing) & oli transmisi.',
            'prio' => 'Normal', 'status' => 'In Progress', 'date' => '2023-10-24', 'pdate' => '2023-10-27',
            'emp' => 3, 'labor' => 500000, 'parts' => 1765000, 'paid' => 'Unpaid', 'pmeth' => null
        ],
        [
            'code' => 'J-105', 'cust' => 'Deepika Rao', 'service' => 'Tyre Replacement & Spooring',
            'desc' => 'Penggantian 2 ban depan Bridgestone Turanza 185/65 R15 + balancing 4 roda & spooring 3D.',
            'prio' => 'Normal', 'status' => 'Ready for Pickup', 'date' => '2023-10-23', 'pdate' => '2023-10-24',
            'emp' => 6, 'labor' => 150000, 'parts' => 1700000, 'paid' => 'Paid', 'pmeth' => 'QRIS'
        ],
        [
            'code' => 'J-106', 'cust' => 'Rajesh S.', 'service' => 'Brake Issue',
            'desc' => 'Rem bunyi mendecit keras saat pengereman kecepatan rendah. Pengecekan ketebalan brake pads.',
            'prio' => 'High', 'status' => 'Pending', 'date' => '2023-10-27', 'pdate' => '2023-10-29',
            'emp' => 4, 'labor' => 150000, 'parts' => 380000, 'paid' => 'Unpaid', 'pmeth' => null
        ],
        [
            'code' => 'J-107', 'cust' => 'Anita D.', 'service' => 'Oil Change & Filter',
            'desc' => 'Ganti oli rutin Castrol Magnatec 10W-40 + filter oli & general inspection 21 titik.',
            'prio' => 'Normal', 'status' => 'Pending', 'date' => '2023-10-27', 'pdate' => '2023-10-28',
            'emp' => 3, 'labor' => 100000, 'parts' => 460000, 'paid' => 'Unpaid', 'pmeth' => null
        ],
        [
            'code' => 'J-108', 'cust' => 'Vikram K.', 'service' => 'Engine Noise Inspection',
            'desc' => 'Suara knocking pada putaran mesin 2500 RPM. Diduga belt tensioner atau busi aus.',
            'prio' => 'Urgent', 'status' => 'Pending', 'date' => '2023-10-28', 'pdate' => '2023-10-30',
            'emp' => 1, 'labor' => 300000, 'parts' => 440000, 'paid' => 'Unpaid', 'pmeth' => null
        ],
        [
            'code' => 'J-109', 'cust' => 'Neha P.', 'service' => 'AC Service & Evaporator Clean',
            'desc' => 'Hembusan AC kurang dingin dan berbau apek. Flushing freon R134a, ganti filter kabin karbon.',
            'prio' => 'Normal', 'status' => 'In Progress', 'date' => '2023-10-28', 'pdate' => '2023-10-29',
            'emp' => 5, 'labor' => 250000, 'parts' => 370000, 'paid' => 'Unpaid', 'pmeth' => null
        ],
        [
            'code' => 'J-110', 'cust' => 'Rahul M.', 'service' => 'Dent Repair & Bumper Alignment',
            'desc' => 'Penyok pada fender kiri depan dan bumper renggang akibat senggolan di tempat parkir.',
            'prio' => 'Normal', 'status' => 'In Progress', 'date' => '2023-10-28', 'pdate' => '2023-10-31',
            'emp' => 6, 'labor' => 650000, 'parts' => 0, 'paid' => 'Unpaid', 'pmeth' => null
        ]
    ];

    $jobStmt = $pdo->prepare("INSERT INTO jobs (job_code, customer_id, vehicle_id, employee_id, service_type, description, priority, status, date_received, date_promised, labor_cost, parts_cost, tax_amount, total_cost, payment_status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($jobsData as $jd) {
        $cId = $customerMap[$jd['cust']] ?? 1;
        $vId = $vehicleMap[$jd['cust']] ?? 1;
        $tax = round(($jd['labor'] + $jd['parts']) * 0.11);
        $total = $jd['labor'] + $jd['parts'] + $tax;

        $jobStmt->execute([
            $jd['code'], $cId, $vId, $jd['emp'], $jd['service'], $jd['desc'],
            $jd['prio'], $jd['status'], $jd['date'], $jd['pdate'],
            $jd['labor'], $jd['parts'], $tax, $total, $jd['paid'], $jd['pmeth']
        ]);
    }

    // 6. Appointments from Image 2 (Bayou Automotive Calendar):
    // John Karsley 10:30 AM
    // Farhad Tarokh 11:15 AM
    // Sijmen Immens 10:30 AM
    // Heather Clark 11:15 AM
    // Lidmila Vilensky 10:30 AM
    // Amelia Cabal 08:00 AM
    // Tania Perfilveya 09:00 AM
    // Leo Knight 10:30 AM
    // Laquita Elliott 10:30 AM
    // Raaid El Syed 11:15 AM
    // Sung Jin-Shil 10:30 AM
    // Oluchi Mazi 10:30 AM
    // Chikelu Obasea 09:00 AM
    // Yvonne Thompson 10:00 AM

    $currentYear = date('Y');
    $currentMonth = date('m');

    $appointments = [
        ['John Karsley', '+62 813-1122-3344', 'Toyota Fortuner (B 1234 JK)', "$currentYear-$currentMonth-06", '10:30 AM', 'Customer Vehicle Pick-Up', 0, 'General Service 20.000 KM & Tire Rotation', 'Scheduled'],
        ['Farhad Tarokh', '+62 812-4455-6677', 'Honda Civic Turbo (B 9876 FT)', "$currentYear-$currentMonth-06", '11:15 AM', 'Drop Off', 1, 'Brake pad inspection and synthetic oil change', 'Confirmed'],
        ['Sijmen Immens', '+62 812-5566-7788', 'Mitsubishi Xpander (B 3344 SI)', "$currentYear-$currentMonth-06", '10:30 AM', 'Customer Vehicle Pick-Up', 0, 'AC cooling check and cabin filter change', 'Scheduled'],
        ['Heather Clark', '+62 812-6677-8899', 'Mazda CX-5 (B 7788 HC)', "$currentYear-$currentMonth-06", '11:15 AM', 'Express Service', 0, 'Battery health check and engine scan diagnostics', 'Scheduled'],
        ['Lidmila Vilensky', '+62 812-7788-9900', 'Subaru Forester (B 5566 LV)', "$currentYear-$currentMonth-06", '10:30 AM', 'Customer Vehicle Pick-Up', 1, 'All-Wheel Drive system check and oil change', 'Confirmed'],
        ['Amelia Cabal', '+62 813-2233-4455', 'Honda CR-V (B 5678 AC)', "$currentYear-$currentMonth-08", '08:00 AM', 'Customer Vehicle Pick-Up', 1, 'Regular periodic maintenance and spark plugs replacement', 'Confirmed'],
        ['Tania Perfilveya', '+62 813-4455-6677', 'BMW 320i (B 3456 TP)', "$currentYear-$currentMonth-08", '09:00 AM', 'Drop Off', 0, 'Brake disc rotor check and sensor replacement', 'Scheduled'],
        ['Leo Knight', '+62 813-3344-5566', 'Pajero Sport (B 9012 LK)', "$currentYear-$currentMonth-09", '10:30 AM', 'Customer Vehicle Pick-Up', 0, 'Suspension noise diagnostic & front stabilizer link check', 'Confirmed'],
        ['Laquita Elliott', '+62 813-8899-0011', 'Hyundai Santa Fe (B 2345 LE)', "$currentYear-$currentMonth-14", '10:30 AM', 'Drop Off', 1, 'Transmission fluid flush and filter replacement', 'Scheduled'],
        ['Raaid El Syed', '+62 813-9900-1122', 'Nissan Terra (B 6789 RE)', "$currentYear-$currentMonth-14", '11:15 AM', 'Customer Vehicle Pick-Up', 0, 'Brake overhaul and steering rack calibration', 'Scheduled'],
        ['Amelia Cabal', '+62 813-2233-4455', 'Honda CR-V (B 5678 AC)', "$currentYear-$currentMonth-16", '08:00 AM', 'Express Service', 0, 'Follow up inspection on AC cooling performance', 'Scheduled'],
        ['Tania Perfilveya', '+62 813-4455-6677', 'BMW 320i (B 3456 TP)', "$currentYear-$currentMonth-16", '09:00 AM', 'Drop Off', 0, 'Software ECU update and detailing', 'Scheduled'],
        ['Sung Jin-Shil', '+62 813-1133-5577', 'Kia Seltos (B 4455 SJ)', "$currentYear-$currentMonth-23", '10:30 AM', 'Customer Vehicle Pick-Up', 0, 'General periodic service 15.000 KM', 'Scheduled'],
        ['Oluchi Mazi', '+62 813-2244-6688', 'Suzuki Grand Vitara (B 8899 OM)', "$currentYear-$currentMonth-30", '10:30 AM', 'Customer Vehicle Pick-Up', 1, 'Complete engine tune-up and brake overhaul', 'Scheduled'],
        ['Chikelu Obasea', '+62 813-3355-7799', 'Toyota Corolla Cross (B 1122 CO)', "$currentYear-$currentMonth-03", '09:00 AM', 'Drop Off', 0, 'Hybrid battery cooling inspection & routine service', 'Completed'],
        ['Yvonne Thompson', '+62 813-4466-8800', 'Hyundai Ioniq 5 (B 9911 YT)', "$currentYear-$currentMonth-03", '10:00 AM', 'Customer Vehicle Pick-Up', 0, 'Electric powertrain inspection and tire rotation', 'Completed']
    ];

    $aptStmt = $pdo->prepare("INSERT INTO appointments (customer_name, customer_phone, vehicle_info, appointment_date, appointment_time, delivery_type, loaner_needed, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($appointments as $apt) {
        $aptStmt->execute($apt);
    }
}

