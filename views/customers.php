<?php
$pdo = Database::getConnection();
$searchQuery = $_GET['search'] ?? '';

$sql = "
    SELECT c.*, 
           COUNT(DISTINCT v.id) as vehicle_count,
           COUNT(DISTINCT j.id) as job_count,
           COALESCE(SUM(j.total_cost), 0) as total_spent,
           GROUP_CONCAT(v.brand || ' ' || v.model || ' (' || v.plate_number || ')', ' | ') as vehicle_summary
    FROM customers c
    LEFT JOIN vehicles v ON c.id = v.customer_id
    LEFT JOIN jobs j ON c.id = j.customer_id
    WHERE 1=1
";
$params = [];

if ($searchQuery) {
    $sql .= " AND (c.name LIKE ? OR c.phone LIKE ? OR c.email LIKE ?)";
    $w = "%$searchQuery%";
    $params = [$w, $w, $w];
}

$sql .= " GROUP BY c.id ORDER BY c.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 style="font-size:24px; font-weight:800; color:var(--text-main);">Data Pelanggan & Kendaraan</h1>
    <p style="color:var(--text-muted); font-size:13.5px;">Buku kontak pemilik mobil, riwayat servis kendaraan, dan total pengeluaran servis.</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('modalAddCustomer')">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    + Tambah Pelanggan & Mobil
  </button>
</div>

<div class="card-panel">
  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Nama Pelanggan</th>
          <th>Kontak & WhatsApp</th>
          <th>Kendaraan Terdaftar</th>
          <th>Jumlah Servis</th>
          <th>Total Pengeluaran</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($customers as $c): ?>
          <tr>
            <td>
              <div style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;" onclick="editCustomerDirect(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>')" title="Klik untuk ganti nama pelanggan">
                <span style="font-weight:700; color:var(--text-main); font-size:14px; border-bottom:1px dashed rgba(255,255,255,0.3);"><?= htmlspecialchars($c['name']) ?></span>
                <span style="font-size:11px; opacity:0.7;" title="Klik untuk ubah nama">✏️</span>
              </div>
              <div style="font-size:12px; color:var(--text-muted);"><?= htmlspecialchars($c['address'] ?? 'Alamat belum diisi') ?></div>
            </td>
            <td>
              <div><?= htmlspecialchars($c['phone'] ?? '-') ?></div>
              <div style="font-size:11.5px; color:var(--text-muted);"><?= htmlspecialchars($c['email'] ?? '') ?></div>
            </td>
            <td>
              <div style="font-size:13px; font-weight:500; color:var(--text-secondary);">
                <?= htmlspecialchars($c['vehicle_summary'] ?: 'Belum ada data') ?>
              </div>
            </td>
            <td>
              <span class="status-badge badge-scheduled"><?= $c['job_count'] ?> kali servis</span>
            </td>
            <td style="font-weight:700; color:var(--text-main);">
              <?= formatCurrency($c['total_spent']) ?>
            </td>
            <td style="text-align:right;">
              <div style="display:flex; justify-content:flex-end; gap:6px;">
                <button class="btn btn-secondary btn-sm" onclick="editCustomerDirect(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>')" title="Ubah Nama">Edit</button>
                <button class="btn btn-outline btn-sm" onclick="openAddJobForCustomer(<?= $c['id'] ?>)">+ Buat SPK</button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Add Customer -->
<div class="modal-backdrop" id="modalAddCustomer">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 class="modal-title">Daftarkan Pelanggan Baru</h3>
      <button type="button" class="modal-close" onclick="closeModal('modalAddCustomer')">&times;</button>
    </div>
    <form id="formAddCustomer">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Lengkap Pelanggan</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">No. Telepon / WA</label>
            <input type="text" name="phone" class="form-control" placeholder="+62 812-xxxx-xxxx" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="nama@email.com">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Alamat</label>
          <input type="text" name="address" class="form-control" placeholder="Alamat domisili">
        </div>
        <hr style="border:none; border-top:1px solid var(--border-color); margin:16px 0;">
        <h4 style="font-size:14px; margin-bottom:12px; color:var(--primary);">Data Kendaraan Pertama</h4>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Merek Mobil</label>
            <input type="text" name="vehicle_brand" class="form-control" placeholder="Toyota, Honda, dll.">
          </div>
          <div class="form-group">
            <label class="form-label">Model / Tipe Mobil</label>
            <input type="text" name="vehicle_model" class="form-control" placeholder="Innova, Avanza, Creta, dll.">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nomor Polisi (Plat)</label>
            <input type="text" name="vehicle_plate" class="form-control" placeholder="B 1234 ABC">
          </div>
          <div class="form-group">
            <label class="form-label">Tahun Pembuatan</label>
            <input type="number" name="vehicle_year" class="form-control" value="<?= date('Y') ?>">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddCustomer')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddJobForCustomer(custId) {
  openModal('modalAddJob');
  const sel = document.getElementById('jobCustomerSelect');
  if (sel) sel.value = custId;
}

document.getElementById('formAddCustomer')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const data = Object.fromEntries(fd.entries());
  data.action = 'create';

  try {
    const res = await fetch('api/customers.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.success) {
      showToast('Sukses', 'Pelanggan dan kendaraan berhasil disimpan!');
      closeModal('modalAddCustomer');
      setTimeout(() => location.reload(), 400);
    }
  } catch (err) {
    alert('Error: ' + err.message);
  }
});
</script>

