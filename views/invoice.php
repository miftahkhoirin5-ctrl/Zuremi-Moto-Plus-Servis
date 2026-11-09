<?php
$pdo = Database::getConnection();
$jobId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("
    SELECT j.*, 
           c.name as customer_name, c.phone as customer_phone, c.email as customer_email, c.address as customer_address,
           v.brand as vehicle_brand, v.model as vehicle_model, v.plate_number, v.year as vehicle_year, v.odometer,
           e.name as mechanic_name, e.role as mechanic_role
    FROM jobs j
    LEFT JOIN customers c ON j.customer_id = c.id
    LEFT JOIN vehicles v ON j.vehicle_id = v.id
    LEFT JOIN employees e ON j.employee_id = e.id
    WHERE j.id = ?
");
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    echo "<div class='card-panel'><p>Data Perintah Kerja tidak ditemukan.</p></div>";
    return;
}

$workshopName = getSetting('workshop_name', 'MotoTrust & Bayou Service Dealer');
$workshopAddress = getSetting('address', 'Jl. Otomotif Raya No. 88, Kawasan Bengkel Resmi');
$workshopPhone = getSetting('phone', '+62 812-3456-7890');
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;" class="no-print">
  <div>
    <a href="index.php?page=jobs" class="btn btn-secondary btn-sm">&larr; Kembali ke Daftar SPK</a>
  </div>
  <div style="display:flex; gap:10px;">
    <?php if ($job['payment_status'] !== 'Paid'): ?>
      <button class="btn btn-primary" onclick="markPaid(<?= $job['id'] ?>)">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        Tandai Lunas (Kasir)
      </button>
    <?php endif; ?>
    <button class="btn btn-secondary" onclick="window.print()">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
      Cetak Faktur (Print)
    </button>
  </div>
</div>

<div class="card-panel invoice-card" style="max-width:850px; margin:0 auto; padding:36px; background:var(--bg-card);">
  <!-- Invoice Header -->
  <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid var(--border-color); padding-bottom:20px; margin-bottom:24px;">
    <div>
      <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
        <div style="width:36px; height:36px; border-radius:8px; background:var(--primary); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold;">
          MT
        </div>
        <div>
          <h2 style="font-size:22px; font-weight:800; color:var(--text-main); margin:0;"><?= htmlspecialchars($workshopName) ?></h2>
          <div style="font-size:12px; color:var(--text-muted);">Bengkel Resmi & Dealer Servis Kendaraan Berstandar OEM</div>
        </div>
      </div>
      <div style="font-size:12px; color:var(--text-muted); line-height:1.6;">
        <?= htmlspecialchars($workshopAddress) ?><br>
        Telepon / WhatsApp: <?= htmlspecialchars($workshopPhone) ?>
      </div>
    </div>
    <div style="text-align:right;">
      <h3 style="font-size:20px; font-weight:800; color:var(--primary); margin:0 0 4px 0;">FAKTUR SERVIS RESMI</h3>
      <div style="font-size:13px; font-weight:700; color:var(--text-main);">NO: INV-<?= htmlspecialchars($job['job_code']) ?></div>
      <div style="font-size:12px; color:var(--text-muted);">Tanggal: <?= htmlspecialchars($job['date_received']) ?></div>
      <div style="margin-top:8px;">
        <?= formatStatusBadge($job['payment_status'] === 'Paid' ? 'Completed' : 'Pending') ?>
      </div>
    </div>
  </div>

  <!-- Customer & Vehicle Info -->
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:24px; background:var(--bg-card-alt); padding:16px 20px; border-radius:var(--radius-md);">
    <div>
      <div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; margin-bottom:6px;">Informasi Pelanggan</div>
      <div style="font-weight:700; font-size:15px; color:var(--text-main);"><?= htmlspecialchars($job['customer_name'] ?? 'Pelanggan') ?></div>
      <div style="font-size:12.5px; color:var(--text-muted);">No. Telp: <?= htmlspecialchars($job['customer_phone'] ?? '-') ?></div>
      <div style="font-size:12.5px; color:var(--text-muted);">Alamat: <?= htmlspecialchars($job['customer_address'] ?? '-') ?></div>
    </div>
    <div>
      <div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; margin-bottom:6px;">Data Kendaraan</div>
      <div style="font-weight:700; font-size:15px; color:var(--text-main);"><?= htmlspecialchars(($job['vehicle_brand'] ?? '') . ' ' . ($job['vehicle_model'] ?? '')) ?></div>
      <div style="font-size:12.5px; color:var(--text-muted);">Nomor Polisi: <strong style="color:var(--text-main);"><?= htmlspecialchars($job['plate_number'] ?? '-') ?></strong></div>
      <div style="font-size:12.5px; color:var(--text-muted);">Mekanik Bertugas: <?= htmlspecialchars($job['mechanic_name'] ?? 'Mekanik') ?></div>
    </div>
  </div>

  <!-- Service Description -->
  <div style="margin-bottom:24px;">
    <div style="font-size:12px; font-weight:700; color:var(--text-muted); margin-bottom:6px;">Keluhan Pelanggan & Catatan Servis:</div>
    <div style="font-size:13.5px; color:var(--text-secondary); background:var(--bg-card-alt); padding:12px 16px; border-radius:var(--radius-sm); border-left:3px solid var(--primary);">
      <strong><?= htmlspecialchars($job['service_type']) ?></strong> &mdash; <?= htmlspecialchars($job['description'] ?: 'Pemeriksaan standar berkala.') ?>
    </div>
  </div>

  <!-- Itemized Cost Table -->
  <table class="custom-table" style="margin-bottom:24px;">
    <thead>
      <tr>
        <th style="width:50px;">No</th>
        <th>Uraian Pekerjaan / Suku Cadang</th>
        <th style="text-align:center; width:80px;">Qty</th>
        <th style="text-align:right; width:140px;">Tarif Satuan</th>
        <th style="text-align:right; width:150px;">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>
          <div style="font-weight:600; color:var(--text-main);">Jasa Mekanik & Inspeksi Teknis</div>
          <div style="font-size:11.5px; color:var(--text-muted);">Tenaga ahli bersertifikat resmi & garansi pengerjaan 14 hari</div>
        </td>
        <td style="text-align:center;">1 pkt</td>
        <td style="text-align:right;"><?= formatCurrency($job['labor_cost']) ?></td>
        <td style="text-align:right; font-weight:600;"><?= formatCurrency($job['labor_cost']) ?></td>
      </tr>
      <tr>
        <td>2</td>
        <td>
          <div style="font-weight:600; color:var(--text-main);">Suku Cadang & Pelumas (Spare Parts & Oil)</div>
          <div style="font-size:11.5px; color:var(--text-muted);">Komponen original OEM bergaransi</div>
        </td>
        <td style="text-align:center;">1 set</td>
        <td style="text-align:right;"><?= formatCurrency($job['parts_cost']) ?></td>
        <td style="text-align:right; font-weight:600;"><?= formatCurrency($job['parts_cost']) ?></td>
      </tr>
    </tbody>
  </table>

  <!-- Calculation Summary -->
  <div style="display:flex; justify-content:flex-end; margin-bottom:30px;">
    <div style="width:300px; display:flex; flex-direction:column; gap:8px; font-size:13.5px;">
      <div style="display:flex; justify-content:space-between; color:var(--text-muted);">
        <span>Subtotal Biaya:</span>
        <span style="font-weight:600; color:var(--text-main);"><?= formatCurrency($job['labor_cost'] + $job['parts_cost']) ?></span>
      </div>
      <div style="display:flex; justify-content:space-between; color:var(--text-muted);">
        <span>PPN (11%):</span>
        <span style="font-weight:600; color:var(--text-main);"><?= formatCurrency($job['tax_amount']) ?></span>
      </div>
      <hr style="border:none; border-top:1px solid var(--border-color); margin:4px 0;">
      <div style="display:flex; justify-content:space-between; font-size:16px; font-weight:800; color:var(--primary);">
        <span>TOTAL TAGIHAN:</span>
        <span><?= formatCurrency($job['total_cost']) ?></span>
      </div>
      <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--text-muted); margin-top:4px;">
        <span>Metode Pembayaran:</span>
        <span><?= htmlspecialchars($job['payment_method'] ?? 'Transfer / QRIS / Tunai') ?></span>
      </div>
    </div>
  </div>

  <!-- Signatures -->
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; text-align:center; margin-top:40px; padding-top:20px; border-top:1px dashed var(--border-color);">
    <div>
      <div style="font-size:12px; color:var(--text-muted); margin-bottom:60px;">Pelanggan / Pemilik Kendaraan,</div>
      <div style="font-weight:700; text-decoration:underline;"><?= htmlspecialchars($job['customer_name'] ?? 'Pelanggan') ?></div>
    </div>
    <div>
      <div style="font-size:12px; color:var(--text-muted); margin-bottom:60px;">Service Advisor & Kasir Bengkel,</div>
      <div style="font-weight:700; text-decoration:underline;">Angelina Devina / MotoTrust</div>
    </div>
  </div>
</div>

<script>
async function markPaid(jobId) {
  const method = prompt("Masukkan metode pembayaran (contoh: QRIS, Transfer BCA, Tunai, Kartu Debit):", "QRIS");
  if (!method) return;

  try {
    const res = await fetch('api/jobs.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'update',
        id: jobId,
        payment_status: 'Paid',
        payment_method: method,
        status: 'Completed'
      })
    });
    const data = await res.json();
    if (data.success) {
      showToast("Sukses", "Pembayaran telah dicatat lunas.");
      setTimeout(() => location.reload(), 400);
    }
  } catch (e) {
    alert("Error: " + e.message);
  }
}
</script>

