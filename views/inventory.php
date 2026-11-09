<?php
$pdo = Database::getConnection();
$catFilter = $_GET['category'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

$sql = "SELECT * FROM inventory WHERE 1=1";
$params = [];

if ($catFilter !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $catFilter;
}

if ($searchQuery) {
    $sql .= " AND (sku LIKE ? OR name LIKE ? OR category LIKE ?)";
    $w = "%$searchQuery%";
    $params = [$w, $w, $w];
}

$sql .= " ORDER BY (stock <= min_stock) DESC, name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM inventory ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);
$lowStockCount = (int)$pdo->query("SELECT COUNT(*) FROM inventory WHERE stock <= min_stock")->fetchColumn();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 style="font-size:24px; font-weight:800; color:var(--text-main);">Gudang & Suku Cadang (Spare Parts)</h1>
    <p style="color:var(--text-muted); font-size:13.5px;">Monitoring stok pelumas, filter, rem, baterai aki, dan suku cadang bengkel resmi.</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('modalAddPart')">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    + Tambah Suku Cadang
  </button>
</div>

<?php if ($lowStockCount > 0): ?>
  <div style="background:rgba(239, 68, 68, 0.1); border:1px solid rgba(239, 68, 68, 0.4); border-radius:var(--radius-md); padding:12px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
    <div style="display:flex; align-items:center; gap:12px; color:#f87171;">
      <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
      <div>
        <strong style="color:var(--text-main);">Peringatan Stok Menipis!</strong>
        <span style="font-size:13px; color:var(--text-muted);"> Terdapat <?= $lowStockCount ?> suku cadang yang telah mencapai batas minimum stok. Harap lakukan pemesanan ulang (Restock).</span>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Category Filter Pills -->
<div style="display:flex; gap:8px; margin-bottom:18px; overflow-x:auto; padding-bottom:6px;">
  <a href="index.php?page=inventory&category=all" class="btn <?= $catFilter === 'all' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">Semua Kategori</a>
  <?php foreach ($categories as $cat): ?>
    <a href="index.php?page=inventory&category=<?= urlencode($cat) ?>" class="btn <?= $catFilter === $cat ? 'btn-primary' : 'btn-secondary' ?> btn-sm"><?= htmlspecialchars($cat) ?></a>
  <?php endforeach; ?>
</div>

<!-- Spare Parts Table -->
<div class="card-panel">
  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Kode SKU</th>
          <th>Nama Suku Cadang</th>
          <th>Kategori</th>
          <th>Sisa Stok</th>
          <th>Batas Min</th>
          <th>Harga Beli</th>
          <th>Harga Jual</th>
          <th style="text-align:right;">Kelola Stok</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): 
          $isLow = $item['stock'] <= $item['min_stock'];
        ?>
          <tr>
            <td><code><?= htmlspecialchars($item['sku']) ?></code></td>
            <td style="font-weight:600; color:var(--text-main);">
              <?= htmlspecialchars($item['name']) ?>
              <?php if ($isLow): ?>
                <span style="background:rgba(239,68,68,0.2); color:#ef4444; font-size:10px; font-weight:700; padding:2px 6px; border-radius:10px; margin-left:6px;">LOW</span>
              <?php endif; ?>
            </td>
            <td><span class="status-badge badge-scheduled" style="font-size:11px;"><?= htmlspecialchars($item['category']) ?></span></td>
            <td>
              <strong style="font-size:15px; color:<?= $isLow ? '#ef4444' : 'var(--text-main)' ?>;">
                <?= $item['stock'] ?> <?= htmlspecialchars($item['unit']) ?>
              </strong>
            </td>
            <td style="color:var(--text-muted);"><?= $item['min_stock'] ?> <?= htmlspecialchars($item['unit']) ?></td>
            <td><?= formatCurrency($item['buy_price']) ?></td>
            <td style="font-weight:700; color:var(--text-main);"><?= formatCurrency($item['sell_price']) ?></td>
            <td style="text-align:right;">
              <div style="display:flex; justify-content:flex-end; gap:6px;">
                <button class="btn btn-outline btn-sm" onclick="adjustStock(<?= $item['id'] ?>, 5)" title="Tambah 5">+5</button>
                <button class="btn btn-outline btn-sm" onclick="adjustStock(<?= $item['id'] ?>, -1)" title="Kurangi 1">-1</button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Add Part -->
<div class="modal-backdrop" id="modalAddPart">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Suku Cadang / Oli Baru</h3>
      <button type="button" class="modal-close" onclick="closeModal('modalAddPart')">&times;</button>
    </div>
    <form id="formAddPart">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Kode SKU</label>
            <input type="text" name="sku" class="form-control" placeholder="Contoh: OIL-10W40-01" required>
          </div>
          <div class="form-group">
            <label class="form-label">Kategori</label>
            <input type="text" name="category" class="form-control" placeholder="Oli, Rem, Filter, dll." required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Barang / Part</label>
          <input type="text" name="name" class="form-control" placeholder="Nama lengkap suku cadang" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Jumlah Stok Awal</label>
            <input type="number" name="stock" class="form-control" value="20" required>
          </div>
          <div class="form-group">
            <label class="form-label">Batas Minimum Stok</label>
            <input type="number" name="min_stock" class="form-control" value="5" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Harga Beli (Rp)</label>
            <input type="number" name="buy_price" class="form-control" value="100000" step="1000" required>
          </div>
          <div class="form-group">
            <label class="form-label">Harga Jual (Rp)</label>
            <input type="number" name="sell_price" class="form-control" value="150000" step="1000" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Satuan</label>
          <select name="unit" class="form-control">
            <option value="pcs">pcs</option>
            <option value="galon">galon (4L)</option>
            <option value="botol">botol (1L)</option>
            <option value="set">set</option>
            <option value="unit">unit</option>
            <option value="paket">paket</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddPart')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan ke Inventaris</button>
      </div>
    </form>
  </div>
</div>

<script>
async function adjustStock(id, amount) {
  try {
    const res = await fetch('api/inventory.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'adjust_stock', id: id, amount: amount })
    });
    const data = await res.json();
    if (data.success) {
      showToast('Stok Diperbarui', `Stok barang berhasil diubah (Sisa: ${data.new_stock})`);
      setTimeout(() => location.reload(), 400);
    }
  } catch (e) {
    alert('Error: ' + e.message);
  }
}

document.getElementById('formAddPart')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const data = Object.fromEntries(fd.entries());
  data.action = 'create';

  try {
    const res = await fetch('api/inventory.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.success) {
      showToast('Sukses', 'Suku cadang baru berhasil didaftarkan!');
      closeModal('modalAddPart');
      setTimeout(() => location.reload(), 400);
    } else {
      alert(result.error || 'Gagal menyimpan part.');
    }
  } catch (err) {
    alert('Error: ' + err.message);
  }
});
</script>

