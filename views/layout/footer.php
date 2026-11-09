    </div><!-- .page-container -->
  </div><!-- .app-main -->
</div><!-- .app-layout -->

<!-- MODAL: Add Appointment (Bayou Automotive - Image 2) -->
<div class="modal-backdrop" id="modalAddAppointment">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Jadwal Booking Servis</h3>
      <button type="button" class="modal-close" onclick="closeModal('modalAddAppointment')">&times;</button>
    </div>
    <form id="formAddAppointment">
      <div class="modal-body">
        <!-- Date & Time -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tanggal Booking</label>
            <input type="date" name="appointment_date" id="aptDateInput" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Jam / Waktu Kedatangan</label>
            <input type="text" name="appointment_time" class="form-control" value="09:30" placeholder="Contoh: 09:30" required>
          </div>
        </div>

        <!-- Customer info -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nama Pelanggan</label>
            <input type="text" name="customer_name" class="form-control" placeholder="Nama Pelanggan" required>
          </div>
          <div class="form-group">
            <label class="form-label">No. Telepon / WhatsApp</label>
            <input type="text" name="customer_phone" class="form-control" placeholder="+62 812-xxxx-xxxx">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Informasi Kendaraan (Merek / Model / Plat)</label>
          <input type="text" name="vehicle_info" class="form-control" placeholder="Contoh: Toyota Fortuner (B 1234 JK)">
        </div>

        <!-- Type of Delivery -->
        <div class="form-group">
          <label class="form-label">Tipe Penyerahan Kendaraan</label>
          <select name="delivery_type" class="form-control">
            <option value="Customer Vehicle Pick-Up" selected>Kendaraan Dijemput Bengkel (Pick-Up)</option>
            <option value="Drop Off">Diantar Sendiri ke Bengkel (Drop Off)</option>
            <option value="Express Service">Layanan Servis Cepat (Express)</option>
            <option value="On-site / Home Service">Servis di Rumah / Tempat (Home Service)</option>
          </select>
        </div>

        <!-- Does customer need loaner? -->
        <div class="form-group">
          <label class="form-label">Butuh Mobil Pinjaman / Pengganti?</label>
          <div class="loaner-toggle">
            <input type="hidden" name="loaner_needed" id="loanerNeededInput" value="0">
            <button type="button" class="loaner-btn" id="loanerYesBtn" onclick="setLoaner(1)">Ya</button>
            <button type="button" class="loaner-btn active" id="loanerNoBtn" onclick="setLoaner(0)">Tidak</button>
          </div>
        </div>

        <!-- Add Description of Services -->
        <div class="form-group">
          <label class="form-label">Deskripsi & Catatan Permintaan Servis</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Tulis rincian keluhan atau permintaan servis..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddAppointment')">Batal</button>
        <button type="submit" class="btn btn-primary" style="background-color:#0284c7;">Simpan Jadwal Booking</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: Appointment Details (Bayou Calendar Event Click) -->
<div class="modal-backdrop" id="modalAptDetail">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 class="modal-title">Detail Booking Servis</h3>
      <button type="button" class="modal-close" onclick="closeModal('modalAptDetail')">&times;</button>
    </div>
    <div class="modal-body">
      <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
        <tr style="border-bottom:1px solid var(--border-color);"><td style="padding:8px 0; color:var(--text-muted); width:130px;">Pelanggan:</td><td style="font-weight:700;" id="detailAptCustomer"></td></tr>
        <tr style="border-bottom:1px solid var(--border-color);"><td style="padding:8px 0; color:var(--text-muted);">No. Telepon:</td><td id="detailAptPhone"></td></tr>
        <tr style="border-bottom:1px solid var(--border-color);"><td style="padding:8px 0; color:var(--text-muted);">Kendaraan:</td><td id="detailAptVehicle"></td></tr>
        <tr style="border-bottom:1px solid var(--border-color);"><td style="padding:8px 0; color:var(--text-muted);">Jadwal & Waktu:</td><td id="detailAptDate"></td></tr>
        <tr style="border-bottom:1px solid var(--border-color);"><td style="padding:8px 0; color:var(--text-muted);">Tipe Penyerahan:</td><td id="detailAptDelivery"></td></tr>
        <tr style="border-bottom:1px solid var(--border-color);"><td style="padding:8px 0; color:var(--text-muted);">Mobil Pinjaman:</td><td id="detailAptLoaner"></td></tr>
        <tr style="border-bottom:1px solid var(--border-color);"><td style="padding:8px 0; color:var(--text-muted);">Status:</td><td id="detailAptStatus"></td></tr>
        <tr><td style="padding:8px 0; color:var(--text-muted); vertical-align:top;">Deskripsi:</td><td style="padding:8px 0;" id="detailAptDesc"></td></tr>
      </table>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" id="btnDeleteApt" style="color:#ef4444; border-color:rgba(239,68,68,0.4);">Hapus</button>
      <button type="button" class="btn btn-secondary" onclick="closeModal('modalAptDetail')">Tutup</button>
      <button type="button" class="btn btn-primary" id="btnConvertToJob">Buat SPK Servis &rarr;</button>
    </div>
  </div>
</div>

<!-- MODAL: Add New Job Order (SPK) -->
<div class="modal-backdrop" id="modalAddJob">
  <div class="modal-dialog modal-lg">
    <div class="modal-header">
      <h3 class="modal-title">Buat Perintah Kerja Baru (SPK)</h3>
      <button type="button" class="modal-close" onclick="closeModal('modalAddJob')">&times;</button>
    </div>
    <form id="formAddJob">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Pelanggan</label>
            <select name="customer_id" class="form-control" id="jobCustomerSelect">
              <?php
              $pdo = Database::getConnection();
              $custs = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC")->fetchAll();
              foreach ($custs as $c):
              ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Kendaraan Pelanggan</label>
            <select name="vehicle_id" class="form-control" id="jobVehicleSelect">
              <?php
              $vehs = $pdo->query("SELECT id, brand, model, plate_number FROM vehicles ORDER BY id ASC")->fetchAll();
              foreach ($vehs as $v):
              ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['brand'] . ' ' . $v['model'] . ' (' . $v['plate_number'] . ')') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Jenis Servis / Keluhan Utama</label>
            <input type="text" name="service_type" class="form-control" placeholder="Contoh: Brake Issue, Oil Change, AC Service" required>
          </div>
          <div class="form-group">
            <label class="form-label">Mekanik / Teknisi Penanggung Jawab</label>
            <select name="employee_id" class="form-control">
              <?php
              $emps = $pdo->query("SELECT id, name, role FROM employees ORDER BY id ASC")->fetchAll();
              foreach ($emps as $e):
              ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name'] . ' (' . $e['role'] . ')') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tingkat Prioritas</label>
            <select name="priority" class="form-control">
              <option value="Normal">Biasa (Normal)</option>
              <option value="High">Tinggi (High)</option>
              <option value="Urgent">Sangat Mendesak (Urgent)</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status Awal</label>
            <select name="status" class="form-control">
              <option value="Pending" selected>Menunggu Antrean</option>
              <option value="In Progress">Sedang Dikerjakan</option>
              <option value="Pending Parts">Tunggu Suku Cadang</option>
              <option value="Ready for Pickup">Siap Diambil</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Estimasi Biaya Jasa Mekanik (Rp)</label>
            <input type="number" name="labor_cost" class="form-control" value="200000" step="5000">
          </div>
          <div class="form-group">
            <label class="form-label">Estimasi Biaya Suku Cadang (Rp)</label>
            <input type="number" name="parts_cost" class="form-control" value="450000" step="5000">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi Pekerjaan & Catatan Keluhan</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan detail keluhan pelanggan dan instruksi servis..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddJob')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan & Terbitkan SPK</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: Edit Job & Customer Details -->
<div class="modal-backdrop" id="modalEditJobCustomer">
  <div class="modal-dialog modal-lg">
    <div class="modal-header">
      <h3 class="modal-title" id="editJobModalTitle">Edit Data SPK & Pelanggan</h3>
      <button type="button" class="modal-close" onclick="closeModal('modalEditJobCustomer')">&times;</button>
    </div>
    <form id="formEditJobCustomer">
      <input type="hidden" name="id" id="editJobId">
      <div class="modal-body">
        <div style="background:var(--bg-card-alt); padding:12px 16px; border-radius:var(--radius-sm); margin-bottom:16px; border-left:3px solid var(--primary); font-size:13px; color:var(--text-secondary);">
          Ganti nama pelanggan dan rincian mobil di bawah ini. Perubahan akan langsung tersimpan di sistem.
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nama Pelanggan <span style="color:#ef4444;">*</span></label>
            <input type="text" name="customer_name" id="editJobCustomerName" class="form-control" required placeholder="Contoh: Budi Santoso">
          </div>
          <div class="form-group">
            <label class="form-label">No. Telepon / WhatsApp</label>
            <input type="text" name="customer_phone" id="editJobCustomerPhone" class="form-control" placeholder="+62 812-xxxx-xxxx">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Merek Mobil</label>
            <input type="text" name="vehicle_brand" id="editJobVehicleBrand" class="form-control" placeholder="Toyota, Honda, dll.">
          </div>
          <div class="form-group">
            <label class="form-label">Tipe / Model Mobil <span style="color:#ef4444;">*</span></label>
            <input type="text" name="vehicle_model" id="editJobVehicleModel" class="form-control" required placeholder="Avanza, Innova, Creta, dll.">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nomor Polisi (Plat)</label>
            <input type="text" name="plate_number" id="editJobPlateNumber" class="form-control" placeholder="B 1234 ABC">
          </div>
          <div class="form-group">
            <label class="form-label">Status SPK</label>
            <select name="status" id="editJobStatus" class="form-control">
              <option value="Pending">Menunggu Antrean</option>
              <option value="In Progress">Sedang Dikerjakan</option>
              <option value="Pending Parts">Tunggu Suku Cadang</option>
              <option value="Ready for Pickup">Siap Diambil</option>
              <option value="Completed">Selesai</option>
              <option value="Cancelled">Dibatalkan</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Jenis Servis / Keluhan</label>
            <input type="text" name="service_type" id="editJobServiceType" class="form-control" placeholder="Contoh: Ganti Oli, Rem, AC">
          </div>
          <div class="form-group">
            <label class="form-label">Mekanik Bertugas</label>
            <select name="employee_id" id="editJobEmployeeId" class="form-control">
              <?php
              $allEmps = $pdo->query("SELECT id, name, role FROM employees ORDER BY id ASC")->fetchAll();
              foreach ($allEmps as $emp):
              ?>
                <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name'] . ' (' . $emp['role'] . ')') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Biaya Jasa Mekanik (Rp)</label>
            <input type="number" name="labor_cost" id="editJobLaborCost" class="form-control" step="5000">
          </div>
          <div class="form-group">
            <label class="form-label">Biaya Suku Cadang (Rp)</label>
            <input type="number" name="parts_cost" id="editJobPartsCost" class="form-control" step="5000">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Catatan Servis</label>
          <textarea name="description" id="editJobDescription" class="form-control" rows="2"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditJobCustomer')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan Data</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: User Profile -->
<div class="modal-backdrop" id="modalUserProfile">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 class="modal-title">Profil Pengguna</h3>
      <button type="button" class="modal-close" onclick="closeModal('modalUserProfile')">&times;</button>
    </div>
    <div class="modal-body" style="text-align:center;">
      <div style="width:70px; height:70px; border-radius:50%; background:linear-gradient(135deg, #f97316 0%, #fb923c 100%); margin:0 auto 14px; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:700; color:white;"><?= htmlspecialchars(strtoupper(substr(getSetting('workshop_name', 'MotoTrust'), 0, 2))) ?></div>
      <h3 style="font-size:18px; margin-bottom:4px;"><?= htmlspecialchars(getSetting('workshop_name', 'MotoTrust')) ?> Admin</h3>
      <p style="color:var(--text-muted); font-size:13px; margin-bottom:16px;">Super Administrator & Service Advisor</p>
      <div style="text-align:left; background:var(--bg-card-alt); padding:14px; border-radius:var(--radius-md); font-size:13px; display:flex; flex-direction:column; gap:8px;">
        <div><strong>Status:</strong> <span style="color:#22c55e;">Aktif</span></div>
        <div><strong>Email:</strong> admin@mototrust.id</div>
        <div><strong>Cabang Bengkel:</strong> Dealer Servis Utama</div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" onclick="closeModal('modalUserProfile')">Tutup</button>
    </div>
  </div>
</div>

<script>
function setLoaner(val) {
  document.getElementById('loanerNeededInput').value = val;
  const yesBtn = document.getElementById('loanerYesBtn');
  const noBtn = document.getElementById('loanerNoBtn');
  if (val === 1) {
    yesBtn.classList.add('active');
    noBtn.classList.remove('active');
  } else {
    noBtn.classList.add('active');
    yesBtn.classList.remove('active');
  }
}
</script>

<script src="assets/js/charts.js"></script>
<script src="assets/js/calendar.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>

