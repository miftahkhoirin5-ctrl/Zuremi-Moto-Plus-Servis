<?php
$pdo = Database::getConnection();

$employees = $pdo->query("
    SELECT e.*, COUNT(j.id) as active_jobs
    FROM employees e
    LEFT JOIN jobs j ON e.id = j.employee_id AND j.status IN ('In Progress', 'Pending')
    GROUP BY e.id
    ORDER BY e.id ASC
")->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 style="font-size:24px; font-weight:800; color:var(--text-main);">Tim Mekanik & Karyawan Bengkel</h1>
    <p style="color:var(--text-muted); font-size:13.5px;">Kepala bengkel, service advisor, mekanik senior, dan teknisi spesialis.</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('modalAddEmployee')">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    + Tambah Mekanik / Staf
  </button>
</div>

<div class="card-panel">
  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Nama Staf / Mekanik</th>
          <th>Jabatan & Posisi</th>
          <th>Spesialisasi Teknis</th>
          <th>SPK Aktif Dikerjakan</th>
          <th>Kontak Telepon</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($employees as $emp): ?>
          <tr>
            <td>
              <div style="font-weight:700; color:var(--text-main); font-size:14px;"><?= htmlspecialchars($emp['name']) ?></div>
              <div style="font-size:11.5px; color:var(--text-muted);"><?= htmlspecialchars($emp['email'] ?? '') ?></div>
            </td>
            <td>
              <strong style="color:var(--primary);"><?= htmlspecialchars($emp['role']) ?></strong>
            </td>
            <td><?= htmlspecialchars($emp['specialization'] ?? '-') ?></td>
            <td>
              <span class="status-badge <?= $emp['active_jobs'] > 0 ? 'badge-in-progress' : 'badge-completed' ?>">
                <?= $emp['active_jobs'] ?> Mobil Sedang Dikerjakan
              </span>
            </td>
            <td><?= htmlspecialchars($emp['phone'] ?? '-') ?></td>
            <td>
              <span class="status-badge badge-completed"><?= htmlspecialchars($emp['status']) ?></span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Add Employee -->
<div class="modal-backdrop" id="modalAddEmployee">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Mekanik / Staf Baru</h3>
      <button type="button" class="modal-close" onclick="closeModal('modalAddEmployee')">&times;</button>
    </div>
    <form id="formAddEmployee">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="name" class="form-control" placeholder="Nama teknisi" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Posisi / Jabatan</label>
            <input type="text" name="role" class="form-control" placeholder="Senior Mechanic, Teknisi AC, dll." required>
          </div>
          <div class="form-group">
            <label class="form-label">No. HP / WA</label>
            <input type="text" name="phone" class="form-control" placeholder="+62 811-xxxx-xxxx">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Spesialisasi Teknis</label>
          <input type="text" name="specialization" class="form-control" placeholder="Contoh: Overhaul Mesin, Suspensi & Rem, Kelistrikan">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddEmployee')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Karyawan</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('formAddEmployee')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const data = Object.fromEntries(fd.entries());
  data.action = 'create';

  try {
    const res = await fetch('api/employees.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.success) {
      showToast('Sukses', 'Mekanik baru berhasil didaftarkan!');
      closeModal('modalAddEmployee');
      setTimeout(() => location.reload(), 400);
    }
  } catch (err) {
    alert('Error: ' + err.message);
  }
});
</script>

