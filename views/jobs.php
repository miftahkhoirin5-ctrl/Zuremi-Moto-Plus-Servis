<?php
$pdo = Database::getConnection();
$statusFilter = $_GET['status'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

$sql = "
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

if ($statusFilter !== 'all') {
    $sql .= " AND j.status = ?";
    $params[] = $statusFilter;
}

if ($searchQuery) {
    $sql .= " AND (j.job_code LIKE ? OR c.name LIKE ? OR v.model LIKE ? OR v.plate_number LIKE ?)";
    $w = "%$searchQuery%";
    $params = array_merge($params, [$w, $w, $w, $w]);
}

$sql .= " ORDER BY j.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 style="font-size:24px; font-weight:800; color:var(--text-main);">Daftar Perintah Kerja Servis (SPK)</h1>
    <p style="color:var(--text-muted); font-size:13.5px;">Kelola pengerjaan servis mobil, suku cadang terpakai, dan status mekanik.</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('modalAddJob')">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    + Buat SPK Baru
  </button>
</div>

<!-- Filter Pills Row -->
<div style="display:flex; gap:10px; margin-bottom:18px; overflow-x:auto; padding-bottom:6px;">
  <a href="index.php?page=jobs&status=all" class="btn <?= $statusFilter === 'all' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Semua Status</a>
  <a href="index.php?page=jobs&status=Pending" class="btn <?= $statusFilter === 'Pending' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Menunggu Antrean</a>
  <a href="index.php?page=jobs&status=In Progress" class="btn <?= $statusFilter === 'In Progress' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Sedang Dikerjakan</a>
  <a href="index.php?page=jobs&status=Pending Parts" class="btn <?= $statusFilter === 'Pending Parts' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Tunggu Suku Cadang</a>
  <a href="index.php?page=jobs&status=Ready for Pickup" class="btn <?= $statusFilter === 'Ready for Pickup' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Siap Diambil</a>
  <a href="index.php?page=jobs&status=Completed" class="btn <?= $statusFilter === 'Completed' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Selesai</a>
</div>

<!-- Jobs List Table -->
<div class="card-panel">
  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Kode SPK</th>
          <th>Pelanggan</th>
          <th>Kendaraan & Plat</th>
          <th>Keluhan / Servis</th>
          <th>Mekanik</th>
          <th>Status</th>
          <th>Total Biaya</th>
          <th>Pembayaran</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($jobs)): ?>
          <tr>
            <td colspan="9" style="text-align:center; padding:30px; color:var(--text-muted);">Tidak ada data SPK sesuai filter.</td>
          </tr>
        <?php endif; ?>
        <?php foreach ($jobs as $j): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($j['job_code']) ?></strong>
              <div style="font-size:11px; color:var(--text-muted);"><?= htmlspecialchars($j['date_received']) ?></div>
            </td>
            <td>
              <div style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;" onclick="editCustomerName(<?= $j['id'] ?>, '<?= htmlspecialchars(addslashes($j['customer_name'] ?? 'Pelanggan')) ?>')" title="Klik untuk ganti nama pelanggan">
                <span style="font-weight:600; color:var(--text-main); border-bottom:1px dashed rgba(255,255,255,0.3);"><?= htmlspecialchars($j['customer_name'] ?? 'Pelanggan') ?></span>
                <span style="font-size:11px; opacity:0.7;" title="Klik untuk ubah nama">✏️</span>
              </div>
              <div style="font-size:11.5px; color:var(--text-muted);"><?= htmlspecialchars($j['customer_phone'] ?? '') ?></div>
            </td>
            <td>
              <div><?= htmlspecialchars(($j['vehicle_brand'] ?? '') . ' ' . ($j['vehicle_model'] ?? '')) ?></div>
              <div style="font-size:11.5px; color:var(--primary); font-weight:600;"><?= htmlspecialchars($j['plate_number'] ?? '-') ?></div>
            </td>
            <td>
              <div><?= htmlspecialchars($j['service_type']) ?></div>
              <div style="font-size:11.5px; color:var(--text-muted); max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                <?= htmlspecialchars($j['description'] ?? '') ?>
              </div>
            </td>
            <td><?= htmlspecialchars($j['mechanic_name'] ?? 'Mekanik') ?></td>
            <td>
              <select onchange="updateJobStatus(<?= $j['id'] ?>, this.value)" style="background:var(--bg-card-alt); color:var(--text-main); border:1px solid var(--border-card); border-radius:14px; padding:3px 8px; font-size:11.5px; font-weight:600; cursor:pointer;">
                <option value="Pending" <?= $j['status'] === 'Pending' ? 'selected' : '' ?>>Menunggu Antrean</option>
                <option value="In Progress" <?= $j['status'] === 'In Progress' ? 'selected' : '' ?>>Sedang Dikerjakan</option>
                <option value="Pending Parts" <?= $j['status'] === 'Pending Parts' ? 'selected' : '' ?>>Tunggu Suku Cadang</option>
                <option value="Ready for Pickup" <?= $j['status'] === 'Ready for Pickup' ? 'selected' : '' ?>>Siap Diambil</option>
                <option value="Completed" <?= $j['status'] === 'Completed' ? 'selected' : '' ?>>Selesai</option>
                <option value="Cancelled" <?= $j['status'] === 'Cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
              </select>
            </td>
            <td style="font-weight:700; color:var(--text-main);"><?= formatCurrency($j['total_cost']) ?></td>
            <td>
              <span class="status-badge <?= $j['payment_status'] === 'Paid' ? 'badge-completed' : 'badge-pending' ?>">
                <?= ($j['payment_status'] === 'Paid') ? 'Lunas' : 'Belum Lunas' ?>
              </span>
            </td>
            <td style="text-align:right;">
              <div style="display:flex; justify-content:flex-end; gap:6px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="openEditJobModal(<?= $j['id'] ?>)" title="Ubah Nama & Data SPK">
                  Ubah
                </button>
                <a href="index.php?page=invoice&id=<?= $j['id'] ?>" class="btn btn-primary btn-sm" title="Buka Faktur & Nota Servis">
                  Faktur
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

