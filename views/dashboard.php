<?php
$pdo = Database::getConnection();

// Fetch live jobs for the table
$recentJobsStmt = $pdo->query("
    SELECT j.*, c.name as customer_name, v.brand as vehicle_brand, v.model as vehicle_model, v.plate_number
    FROM jobs j
    LEFT JOIN customers c ON j.customer_id = c.id
    LEFT JOIN vehicles v ON j.vehicle_id = v.id
    ORDER BY j.date_received DESC, j.id DESC
    LIMIT 10
");
$recentJobs = $recentJobsStmt->fetchAll();

// Fetch Kanban column items
$pendingJobs = $pdo->query("
    SELECT j.*, c.name as customer_name, v.brand as vehicle_brand, v.model as vehicle_model
    FROM jobs j
    LEFT JOIN customers c ON j.customer_id = c.id
    LEFT JOIN vehicles v ON j.vehicle_id = v.id
    WHERE j.status = 'Pending'
    ORDER BY j.id ASC
")->fetchAll();

$inProgressJobs = $pdo->query("
    SELECT j.*, c.name as customer_name, v.brand as vehicle_brand, v.model as vehicle_model
    FROM jobs j
    LEFT JOIN customers c ON j.customer_id = c.id
    LEFT JOIN vehicles v ON j.vehicle_id = v.id
    WHERE j.status IN ('In Progress', 'Pending Parts')
    ORDER BY j.id ASC
")->fetchAll();

$readyJobs = $pdo->query("
    SELECT j.*, c.name as customer_name, v.brand as vehicle_brand, v.model as vehicle_model
    FROM jobs j
    LEFT JOIN customers c ON j.customer_id = c.id
    LEFT JOIN vehicles v ON j.vehicle_id = v.id
    WHERE j.status IN ('Ready for Pickup', 'Completed')
    ORDER BY j.id ASC
")->fetchAll();

$currencySymbol = getSetting('currency_symbol', 'Rp');
?>

<!-- Quick Header Action Row -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:22px;">
  <div>
    <h1 style="font-size:24px; font-weight:800; letter-spacing:-0.5px; color:var(--text-main);">Dashboard Bengkel</h1>
    <p style="color:var(--text-muted); font-size:13.5px;">Monitoring operasional servis, antrean kendaraan, pendapatan,
      dan inventaris.</p>
  </div>
  <div style="display:flex; gap:10px;">
    <button class="btn btn-secondary" onclick="openModal('modalAddAppointment')">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
      </svg>
      Booking Servis
    </button>
    <button class="btn btn-primary" onclick="openModal('modalAddJob')">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
      </svg>
      Buat SPK Baru
    </button>
  </div>
</div>

<!-- 4 KPI Cards (Image 1 Top Row) -->
<div class="kpi-grid">
  <!-- Card 1: Active Jobs -->
  <div class="kpi-card">
    <div class="kpi-header">
      <span class="kpi-title">Servis Aktif</span>
      <div class="kpi-icon-wrap">
        <!-- Clipboard icon -->
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
          </path>
        </svg>
      </div>
    </div>
    <div class="kpi-value" id="kpiActiveJobs">24</div>
    <div class="kpi-footer">
      <span>30 hari terakhir:</span>
      <span class="kpi-trend-positive">+12%</span>
    </div>
  </div>

  <!-- Card 2: Revenue -->
  <div class="kpi-card">
    <div class="kpi-header">
      <span class="kpi-title">Total Pendapatan</span>
      <div class="kpi-icon-wrap">
        <!-- Currency icon -->
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
          </path>
        </svg>
      </div>
    </div>
    <div class="kpi-value" id="kpiRevenue"><?= ($currencySymbol === '₹') ? '₹1.2L' : 'Rp 124.5M' ?></div>
    <div class="kpi-footer">
      <span>30 hari terakhir:</span>
      <span class="kpi-trend-positive">+6%</span>
    </div>
  </div>

  <!-- Card 3: Customers -->
  <div class="kpi-card">
    <div class="kpi-header">
      <span class="kpi-title">Total Pelanggan</span>
      <div class="kpi-icon-wrap">
        <!-- Customers icon -->
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
          </path>
        </svg>
      </div>
    </div>
    <div class="kpi-value" id="kpiCustomers">156</div>
    <div class="kpi-footer">
      <span>30 hari terakhir:</span>
      <span class="kpi-trend-positive">+4%</span>
    </div>
  </div>

  <!-- Card 4: Pending Approval -->
  <div class="kpi-card">
    <div class="kpi-header">
      <span class="kpi-title">Menunggu Antrean</span>
      <div class="kpi-icon-wrap">
        <!-- Clock icon -->
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
    </div>
    <div class="kpi-value" id="kpiPending">8</div>
    <div class="kpi-footer">
      <span class="kpi-trend-urgent">Perlu Perhatian Segera</span>
    </div>
  </div>
</div>

<!-- Middle Row: Weekly Revenue Chart + Recent Jobs (Image 1 Middle Row) -->
<div class="dashboard-grid-2">
  <!-- Left: Weekly Revenue Chart -->
  <div class="card-panel">
    <div class="panel-header">
      <h3 class="panel-title">Pendapatan Mingguan</h3>
      <div style="font-size:12px; color:var(--text-muted);">Senin - Minggu</div>
    </div>
    <div class="chart-container">
      <canvas id="revenueChart" style="width:100%; height:100%;"></canvas>
    </div>
  </div>

  <!-- Right: Recent Jobs Table -->
  <div class="card-panel">
    <div class="panel-header">
      <h3 class="panel-title">SPK & Servis Terkini</h3>
      <a href="index.php?page=jobs" class="btn btn-outline btn-sm">Lihat Semua &rarr;</a>
    </div>
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>ID SPK</th>
            <th>Pelanggan</th>
            <th>Kendaraan</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentJobs as $job): ?>
            <tr>
              <td><?= htmlspecialchars($job['job_code']) ?></td>
              <td>
                <div style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;"
                  onclick="editCustomerName(<?= $job['id'] ?>, '<?= htmlspecialchars(addslashes($job['customer_name'] ?? 'Pelanggan')) ?>')"
                  title="Klik untuk ganti nama pelanggan">
                  <span
                    style="font-weight:600; color:var(--text-main); border-bottom:1px dashed rgba(255,255,255,0.3);"><?= htmlspecialchars($job['customer_name'] ?? 'Pelanggan') ?></span>
                  <span style="font-size:11px; opacity:0.7;" title="Klik untuk ubah nama">✏️</span>
                </div>
              </td>
              <td><?= htmlspecialchars(($job['vehicle_brand'] ?? '') . ' ' . ($job['vehicle_model'] ?? 'Mobil')) ?></td>
              <td><?= formatStatusBadge($job['status']) ?></td>
              <td style="font-size:12.5px;"><?= htmlspecialchars($job['date_received']) ?></td>
              <td>
                <div style="display:flex; gap:6px;">
                  <button type="button" class="btn btn-secondary btn-sm" style="padding:3px 8px; font-size:11px;"
                    onclick="openEditJobModal(<?= $job['id'] ?>)" title="Ubah Nama Pelanggan & Data Mobil">
                    Ubah
                  </button>
                  <a href="index.php?page=invoice&id=<?= $job['id'] ?>" class="btn btn-outline btn-sm"
                    style="padding:3px 8px; font-size:11px;" title="Cetak Faktur">
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
</div>

<!-- Bottom Row: Kanban Board / Job Stages (Image 1 Bottom Row) -->
<div class="panel-header" style="margin-top:10px; margin-bottom:14px;">
  <h3 class="panel-title" style="display:flex; align-items:center; gap:8px;">
    <span>Alur Kerja & Status Kendaraan</span>
    <span style="font-size:12px; font-weight:normal; color:var(--text-muted);">(Klik kartu untuk memindahkan
      status)</span>
  </h3>
</div>

<div class="kanban-grid">
  <!-- Column 1: Pending -->
  <div class="kanban-column">
    <div class="column-header">
      <div class="column-title">
        <span style="width:8px; height:8px; border-radius:50%; background:#f97316;"></span>
        <span>Menunggu Antrean</span>
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <span class="column-count">(<?= count($pendingJobs) ?>)</span>
        <button type="button" class="btn btn-outline btn-sm" style="padding:2px 7px; font-size:11px;"
          onclick="openModal('modalAddJob')" title="Tambah Antrean Pending Baru">+ Tambah</button>
      </div>
    </div>
    <div class="kanban-cards">
      <?php if (empty($pendingJobs)): ?>
        <div
          style="padding:24px 16px; text-align:center; color:var(--text-muted); font-size:12px; background:var(--bg-card-alt); border-radius:var(--radius-sm); border:1px dashed var(--border-color);">
          Tidak ada antrean pending.<br>
          <button class="btn btn-primary btn-sm" style="margin-top:8px; font-size:11px;"
            onclick="openModal('modalAddJob')">+ Tambah SPK</button>
        </div>
      <?php endif; ?>
      <?php foreach ($pendingJobs as $pj): ?>
        <div class="kanban-card">
          <div class="kanban-card-title">
            <span><strong><?= htmlspecialchars($pj['job_code']) ?>:</strong>
              <?= htmlspecialchars($pj['service_type']) ?></span>
            <button type="button" onclick="openEditJobModal(<?= $pj['id'] ?>)"
              style="background:transparent; border:none; color:var(--text-muted); cursor:pointer; font-size:11px;"
              title="Ubah Data">✏️</button>
          </div>
          <div class="kanban-card-info">
            <div class="kanban-card-info-item">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <span style="cursor:pointer; border-bottom:1px dashed rgba(255,255,255,0.25);"
                onclick="editCustomerName(<?= $pj['id'] ?>, '<?= htmlspecialchars(addslashes($pj['customer_name'] ?? 'Pelanggan')) ?>')"
                title="Klik untuk ganti nama">
                <?= htmlspecialchars($pj['customer_name']) ?>
              </span>
            </div>
            <div class="kanban-card-info-item">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 19H5V8h14v11zM7 4h10v4H7V4z">
                </path>
              </svg>
              <span><?= htmlspecialchars(($pj['vehicle_brand'] ?? '') . ' ' . ($pj['vehicle_model'] ?? '')) ?></span>
            </div>
          </div>
          <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:center; gap:6px;">
            <select onchange="updateJobStatus(<?= $pj['id'] ?>, this.value)"
              style="background:var(--bg-card); color:var(--text-main); border:1px solid var(--border-card); border-radius:10px; padding:3px 6px; font-size:11px; font-weight:600; cursor:pointer;">
              <option value="Pending" selected>Menunggu Antrean</option>
              <option value="In Progress">Sedang Dikerjakan</option>
              <option value="Pending Parts">Tunggu Suku Cadang</option>
              <option value="Ready for Pickup">Siap Diambil</option>
              <option value="Completed">Selesai</option>
            </select>
            <button type="button" class="btn btn-outline btn-sm"
              style="padding:3px 8px; font-size:11px; color:var(--primary); border-color:var(--primary);"
              onclick="updateJobStatus(<?= $pj['id'] ?>, 'In Progress')">
              Mulai Pengerjaan &rarr;
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Column 2: In Progress -->
  <div class="kanban-column">
    <div class="column-header">
      <div class="column-title">
        <span style="width:8px; height:8px; border-radius:50%; background:#38bdf8;"></span>
        <span>Sedang Dikerjakan</span>
      </div>
      <span class="column-count">(<?= count($inProgressJobs) ?>)</span>
    </div>
    <div class="kanban-cards">
      <?php if (empty($inProgressJobs)): ?>
        <div
          style="padding:24px 16px; text-align:center; color:var(--text-muted); font-size:12px; background:var(--bg-card-alt); border-radius:var(--radius-sm); border:1px dashed var(--border-color);">
          Tidak ada mobil yang sedang dikerjakan.
        </div>
      <?php endif; ?>
      <?php foreach ($inProgressJobs as $ipj): ?>
        <div class="kanban-card">
          <div class="kanban-card-title">
            <span><strong><?= htmlspecialchars($ipj['job_code']) ?>:</strong>
              <?= htmlspecialchars($ipj['service_type']) ?></span>
            <button type="button" onclick="openEditJobModal(<?= $ipj['id'] ?>)"
              style="background:transparent; border:none; color:var(--text-muted); cursor:pointer; font-size:11px;"
              title="Ubah Data">✏️</button>
          </div>
          <div class="kanban-card-info">
            <div class="kanban-card-info-item">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <span style="cursor:pointer; border-bottom:1px dashed rgba(255,255,255,0.25);"
                onclick="editCustomerName(<?= $ipj['id'] ?>, '<?= htmlspecialchars(addslashes($ipj['customer_name'] ?? 'Pelanggan')) ?>')"
                title="Klik untuk ganti nama">
                <?= htmlspecialchars($ipj['customer_name']) ?>
              </span>
            </div>
            <div class="kanban-card-info-item">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 19H5V8h14v11zM7 4h10v4H7V4z">
                </path>
              </svg>
              <span><?= htmlspecialchars(($ipj['vehicle_brand'] ?? '') . ' ' . ($ipj['vehicle_model'] ?? '')) ?></span>
            </div>
          </div>
          <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:center; gap:6px;">
            <select onchange="updateJobStatus(<?= $ipj['id'] ?>, this.value)"
              style="background:var(--bg-card); color:var(--text-main); border:1px solid var(--border-card); border-radius:10px; padding:3px 6px; font-size:11px; font-weight:600; cursor:pointer;">
              <option value="Pending" <?= $ipj['status'] === 'Pending' ? 'selected' : '' ?>>Menunggu Antrean</option>
              <option value="In Progress" <?= $ipj['status'] === 'In Progress' ? 'selected' : '' ?>>Sedang Dikerjakan
              </option>
              <option value="Pending Parts" <?= $ipj['status'] === 'Pending Parts' ? 'selected' : '' ?>>Tunggu Suku Cadang
              </option>
              <option value="Ready for Pickup" <?= $ipj['status'] === 'Ready for Pickup' ? 'selected' : '' ?>>Siap Diambil
              </option>
              <option value="Completed" <?= $ipj['status'] === 'Completed' ? 'selected' : '' ?>>Selesai</option>
            </select>
            <button type="button" class="btn btn-outline btn-sm"
              style="padding:3px 8px; font-size:11px; color:#38bdf8; border-color:#38bdf8;"
              onclick="updateJobStatus(<?= $ipj['id'] ?>, 'Ready for Pickup')">
              Siap Diambil &rarr;
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Column 3: Ready for Pickup -->
  <div class="kanban-column">
    <div class="column-header">
      <div class="column-title">
        <span style="width:8px; height:8px; border-radius:50%; background:#22c55e;"></span>
        <span>Siap Diambil</span>
      </div>
      <span class="column-count">(<?= count($readyJobs) ?>)</span>
    </div>
    <div class="kanban-cards">
      <?php if (empty($readyJobs)): ?>
        <div
          style="padding:24px 16px; text-align:center; color:var(--text-muted); font-size:12px; background:var(--bg-card-alt); border-radius:var(--radius-sm); border:1px dashed var(--border-color);">
          Tidak ada mobil yang siap diambil saat ini.
        </div>
      <?php endif; ?>
      <?php foreach ($readyJobs as $rj): ?>
        <div class="kanban-card">
          <div class="kanban-card-title">
            <span><strong><?= htmlspecialchars($rj['job_code']) ?>:</strong>
              <?= htmlspecialchars($rj['service_type']) ?></span>
            <button type="button" onclick="openEditJobModal(<?= $rj['id'] ?>)"
              style="background:transparent; border:none; color:var(--text-muted); cursor:pointer; font-size:11px;"
              title="Ubah Data">✏️</button>
          </div>
          <div class="kanban-card-info">
            <div class="kanban-card-info-item">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <span style="cursor:pointer; border-bottom:1px dashed rgba(255,255,255,0.25);"
                onclick="editCustomerName(<?= $rj['id'] ?>, '<?= htmlspecialchars(addslashes($rj['customer_name'] ?? 'Pelanggan')) ?>')"
                title="Klik untuk ganti nama">
                <?= htmlspecialchars($rj['customer_name']) ?>
              </span>
            </div>
            <div class="kanban-card-info-item">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 19H5V8h14v11zM7 4h10v4H7V4z">
                </path>
              </svg>
              <span><?= htmlspecialchars(($rj['vehicle_brand'] ?? '') . ' ' . ($rj['vehicle_model'] ?? '')) ?></span>
            </div>
          </div>
          <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:center; gap:6px;">
            <select onchange="updateJobStatus(<?= $rj['id'] ?>, this.value)"
              style="background:var(--bg-card); color:var(--text-main); border:1px solid var(--border-card); border-radius:10px; padding:3px 6px; font-size:11px; font-weight:600; cursor:pointer;">
              <option value="Pending" <?= $rj['status'] === 'Pending' ? 'selected' : '' ?>>Menunggu Antrean</option>
              <option value="In Progress" <?= $rj['status'] === 'In Progress' ? 'selected' : '' ?>>Sedang Dikerjakan</option>
              <option value="Pending Parts" <?= $rj['status'] === 'Pending Parts' ? 'selected' : '' ?>>Tunggu Suku Cadang
              </option>
              <option value="Ready for Pickup" <?= $rj['status'] === 'Ready for Pickup' ? 'selected' : '' ?>>Siap Diambil
              </option>
              <option value="Completed" <?= $rj['status'] === 'Completed' ? 'selected' : '' ?>>Selesai</option>
            </select>
            <a href="index.php?page=invoice&id=<?= $rj['id'] ?>" class="btn btn-outline btn-sm"
              style="padding:3px 8px; font-size:11px; color:#22c55e; border-color:#22c55e;">
              Lihat Faktur &rarr;
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', async () => {
    // Load chart data and draw
    try {
      const res = await fetch('api/reports.php');
      const json = await res.json();
      if (json.success && json.weekly_chart) {
        window.currentChartData = json.weekly_chart;
        MotoCharts.drawWeeklyRevenue('revenueChart', json.weekly_chart);
      }
    } catch (e) {
      console.error("Chart load error:", e);
    }
  });

  function promptChangeStatus(jobId, targetStatus) {
    if (confirm(`Ubah status SPK #${jobId} menjadi '${targetStatus}'?`)) {
      updateJobStatus(jobId, targetStatus);
    }
  }
</script>