<?php
$workshopName = getSetting('workshop_name', 'MotoTrust & Bayou Service Dealer');
$workshopTagline = getSetting('workshop_tagline', 'Bengkel Resmi & Dealer Servis Kendaraan');
$workshopAddress = getSetting('address', 'Jl. Otomotif Raya No. 88, Kawasan Bengkel Resmi');
$workshopPhone = getSetting('phone', '+62 812-3456-7890');
$workshopEmail = getSetting('email', 'service@mototrust.id');
$currency = getSetting('currency_symbol', 'Rp');
$taxRate = getSetting('tax_rate', '11');
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
  <div>
    <h1 style="font-size:24px; font-weight:800; color:var(--text-main);">Pengaturan Sistem Bengkel</h1>
    <p style="color:var(--text-muted); font-size:13.5px;">Konfigurasi profil bengkel resmi, mata uang, pajak PPN, dan opsi database.</p>
  </div>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
  <div class="card-panel">
    <div class="panel-header">
      <h3 class="panel-title">Profil Bengkel & Dealer</h3>
    </div>
    <form id="formSettings">
      <div class="form-group">
        <label class="form-label">Nama Bengkel / Tulisan Logo Brand (Pengganti Tulisan "MotoTrust")</label>
        <input type="text" name="workshop_name" class="form-control" value="<?= htmlspecialchars($workshopName) ?>" placeholder="Contoh: MotoTrust, Bengkel Maju Jaya, Auto2000, dll." required>
        <small style="color:var(--text-muted); font-size:12px;">Tulisan ini tampil sebagai logo utama di pojok kiri atas sidebar dan kepala faktur cetak.</small>
      </div>
      <div class="form-group">
        <label class="form-label">Slogan / Tagline</label>
        <input type="text" name="workshop_tagline" class="form-control" value="<?= htmlspecialchars($workshopTagline) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Alamat Lengkap</label>
        <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($workshopAddress) ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Nomor Telepon / Layanan Pelanggan</label>
          <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($workshopPhone) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Email Resmi</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($workshopEmail) ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Format Mata Uang</label>
          <select name="currency_symbol" class="form-control">
            <option value="Rp" <?= $currency === 'Rp' ? 'selected' : '' ?>>Rupiah Indonesia (Rp)</option>
            <option value="₹" <?= $currency === '₹' ? 'selected' : '' ?>>Indian Rupee (₹ - seperti di gambar)</option>
            <option value="$" <?= $currency === '$' ? 'selected' : '' ?>>US Dollar ($)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tarif Pajak PPN (%)</label>
          <input type="number" name="tax_rate" class="form-control" value="<?= htmlspecialchars($taxRate) ?>" step="0.5">
        </div>
      </div>
      <div style="margin-top:14px;">
        <button type="submit" class="btn btn-primary">Simpan Perubahan Pengaturan</button>
      </div>
    </form>
  </div>

  <div style="display:flex; flex-direction:column; gap:20px;">
    <!-- Theme Switch Card -->
    <div class="card-panel">
      <div class="panel-header">
        <h3 class="panel-title">Tampilan & Mode Tema</h3>
      </div>
      <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
        Aplikasi mendukung 2 tampilan sesuai foto referensi:
      </p>
      <div style="display:flex; flex-direction:column; gap:10px;">
        <button class="btn btn-secondary" onclick="setThemeMode('dark')">
          <span style="width:10px; height:10px; border-radius:50%; background:#f97316;"></span>
          Mode MotoTrust Gelap (Dark)
        </button>
        <button class="btn btn-secondary" onclick="setThemeMode('light')">
          <span style="width:10px; height:10px; border-radius:50%; background:#0284c7;"></span>
          Mode Bayou Terang (Light)
        </button>
      </div>
    </div>

    <!-- Localize to Indonesian Names -->
    <div class="card-panel">
      <div class="panel-header">
        <h3 class="panel-title">Ganti Semua ke Nama Indonesia</h3>
      </div>
      <p style="font-size:12.5px; color:var(--text-muted); margin-bottom:14px;">
        Ubah seluruh nama demo (Rajesh Sharma, Priya, Amit, Rahul M, dll.) secara serentak menjadi nama Indonesia (Budi Santoso, Siti Rahma, Agus Pratama, Raditya, dll.).
      </p>
      <button class="btn btn-primary" style="width:100%;" onclick="localizeIndonesianNames()">
        🇮🇩 Ganti Semua ke Nama Indonesia
      </button>
    </div>

    <!-- Database Info & Reset -->
    <div class="card-panel">
      <div class="panel-header">
        <h3 class="panel-title">Reset Database Demo</h3>
      </div>
      <p style="font-size:12.5px; color:var(--text-muted); margin-bottom:14px;">
        Jika Anda ingin mengembalikan seluruh data awal seperti di foto (Rajesh Sharma, Priya Patel, Bayou Calendar, Suku Cadang Gudang):
      </p>
      <button class="btn btn-outline" style="color:#ef4444; border-color:rgba(239,68,68,0.5); width:100%;" onclick="resetDatabaseDemo()">
        Reset & Muat Ulang Data Awal
      </button>
    </div>
  </div>
</div>

<script>
function setThemeMode(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('mototrust_theme', theme);
  updateThemeIcon(theme);
  showToast('Tema Diubah', `Beralih ke mode ${theme}`);
}

async function localizeIndonesianNames() {
  if (!confirm("Ubah seluruh nama pelanggan sampel menjadi nama Indonesia (Budi Santoso, Raditya Mahendra, Siti Rahmawati, dll.)?")) return;
  try {
    const res = await fetch('api/settings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'localize_indonesia' })
    });
    const data = await res.json();
    if (data.success) {
      showToast("Sukses", data.message);
      setTimeout(() => location.reload(), 500);
    }
  } catch (e) {
    alert("Error: " + e.message);
  }
}

async function resetDatabaseDemo() {
  if (!confirm("Peringatan: Seluruh data akan di-reset ke data bawaan referensi gambar. Lanjutkan?")) return;
  try {
    const res = await fetch('api/settings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'reset_database' })
    });
    const data = await res.json();
    if (data.success) {
      alert("Database berhasil di-reset ke data sampel awal!");
      location.reload();
    }
  } catch (e) {
    alert("Error: " + e.message);
  }
}

document.getElementById('formSettings')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const data = Object.fromEntries(fd.entries());
  data.action = 'update';

  try {
    const res = await fetch('api/settings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.success) {
      showToast('Sukses', 'Pengaturan berhasil diperbarui!');
      setTimeout(() => location.reload(), 400);
    }
  } catch (err) {
    alert('Error: ' + err.message);
  }
});
</script>

