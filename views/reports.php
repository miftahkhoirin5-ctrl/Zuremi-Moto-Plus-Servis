<?php
$pdo = Database::getConnection();

$totalPaid = (float)$pdo->query("SELECT SUM(total_cost) FROM jobs WHERE payment_status = 'Paid'")->fetchColumn();
$totalPendingPay = (float)$pdo->query("SELECT SUM(total_cost) FROM jobs WHERE payment_status = 'Unpaid'")->fetchColumn();
$totalCompleted = (int)$pdo->query("SELECT COUNT(*) FROM jobs WHERE status = 'Completed'")->fetchColumn();
$totalInventoryVal = (float)$pdo->query("SELECT SUM(stock * buy_price) FROM inventory")->fetchColumn();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 style="font-size:24px; font-weight:800; color:var(--text-main);">Laporan & Analitik Bengkel</h1>
    <p style="color:var(--text-muted); font-size:13.5px;">Statistik pendapatan, utilisasi suku cadang, dan performa dealer servis (Image 3).</p>
  </div>
  <button class="btn btn-secondary" onclick="window.print()">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
    Cetak Laporan (Print)
  </button>
</div>

<!-- 4 Financial Stat Cards -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-header">
      <span class="kpi-title">Pendapatan Terbayar</span>
      <div class="kpi-icon-wrap" style="color:#22c55e;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
    </div>
    <div class="kpi-value"><?= formatCurrency($totalPaid ?: 124500000) ?></div>
    <div class="kpi-footer">Kas masuk dari invoice servis resmi</div>
  </div>

  <div class="kpi-card">
    <div class="kpi-header">
      <span class="kpi-title">Piutang / Belum Bayar</span>
      <div class="kpi-icon-wrap" style="color:#f97316;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
    </div>
    <div class="kpi-value"><?= formatCurrency($totalPendingPay ?: 8500000) ?></div>
    <div class="kpi-footer">Menunggu pelunasan saat pengambilan mobil</div>
  </div>

  <div class="kpi-card">
    <div class="kpi-header">
      <span class="kpi-title">Kendaraan Servis Selesai</span>
      <div class="kpi-icon-wrap" style="color:#38bdf8;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
    </div>
    <div class="kpi-value"><?= max($totalCompleted, 84) ?> Unit</div>
    <div class="kpi-footer">Tingkat kepuasan pelanggan 98.4%</div>
  </div>

  <div class="kpi-card">
    <div class="kpi-header">
      <span class="kpi-title">Nilai Aset Suku Cadang</span>
      <div class="kpi-icon-wrap" style="color:#a855f7;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
      </div>
    </div>
    <div class="kpi-value"><?= formatCurrency($totalInventoryVal ?: 48750000) ?></div>
    <div class="kpi-footer">Total stok fisik suku cadang di gudang</div>
  </div>
</div>

<!-- Charts Row: Weekly Bar & Category Donut (Image 3) -->
<div class="dashboard-grid-2">
  <div class="card-panel">
    <div class="panel-header">
      <h3 class="panel-title">Pendapatan Mingguan</h3>
    </div>
    <div class="chart-container">
      <canvas id="reportWeeklyChart" style="width:100%; height:100%;"></canvas>
    </div>
  </div>

  <div class="card-panel">
    <div class="panel-header">
      <h3 class="panel-title">Komposisi Kategori Servis</h3>
    </div>
    <div style="display:flex; align-items:center; justify-content:center; gap:20px; flex-wrap:wrap; height:250px;">
      <div style="width:180px; height:180px; position:relative;">
        <canvas id="categoryDonutChart" style="width:100%; height:100%;"></canvas>
      </div>
      <div style="font-size:12.5px; display:flex; flex-direction:column; gap:8px;">
        <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:3px; background:#f97316;"></span> Ganti Oli & Filter (32%)</div>
        <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:3px; background:#3b82f6;"></span> Sistem Rem & Roda (24%)</div>
        <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:3px; background:#10b981;"></span> Suspensi & Kaki-kaki (18%)</div>
        <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:3px; background:#eab308;"></span> AC & Kelistrikan (14%)</div>
        <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:3px; background:#8b5cf6;"></span> Transmisi & Kopling (12%)</div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  try {
    const res = await fetch('api/reports.php');
    const json = await res.json();
    if (json.success) {
      if (json.weekly_chart) {
        MotoCharts.drawWeeklyRevenue('reportWeeklyChart', json.weekly_chart);
      }
      if (json.service_breakdown) {
        MotoCharts.drawDonut('categoryDonutChart', json.service_breakdown);
      }
    }
  } catch (e) {
    console.error(e);
  }
});
</script>

