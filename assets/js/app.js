/**
 * MotoTrust & Bayou Automotive - Main Application JS
 */

// Theme Management
function initTheme() {
  const savedTheme = localStorage.getItem('mototrust_theme') || 'dark';
  document.documentElement.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);
}

function toggleTheme() {
  const current = document.documentElement.getAttribute('data-theme') || 'dark';
  const next = current === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem('mototrust_theme', next);
  updateThemeIcon(next);

  // Re-draw charts if any
  if (window.currentChartData && window.MotoCharts) {
    window.MotoCharts.drawWeeklyRevenue('revenueChart', window.currentChartData);
  }

  showToast('Tema Diganti', `Mode ${next === 'dark' ? 'Gelap' : 'Terang'} diaktifkan.`);
}

function updateThemeIcon(theme) {
  const label = document.getElementById('themeToggleLabel');
  const icon = document.getElementById('themeToggleIcon');
  if (label) {
    label.innerText = theme === 'dark' ? 'Gelap' : 'Terang';
  }
}

// Toast Notifications
function showToast(title, message, type = 'info') {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = `
    <div style="width:10px; height:10px; border-radius:50%; background:var(--primary);"></div>
    <div>
      <div style="font-weight:700; font-size:13px; color:var(--text-main);">${title}</div>
      <div style="font-size:12px; color:var(--text-muted);">${message}</div>
    </div>
  `;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

// Modal helper
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.add('show');
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.remove('show');
}

// Kanban Status Update
async function updateJobStatus(jobId, newStatus) {
  try {
    const res = await fetch('api/jobs.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'update_status', job_id: jobId, status: newStatus })
    });
    const json = await res.json();
    if (json.success) {
      showToast('Status Diperbarui', `SPK #${jobId} dipindahkan ke ${newStatus}`);
      setTimeout(() => location.reload(), 500);
    } else {
      alert(json.error || 'Gagal memperbarui status.');
    }
  } catch (e) {
    alert('Error: ' + e.message);
  }
}

// Quick Edit Workshop / Brand Name (e.g. MotoTrust)
async function editWorkshopName(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }
  const currentElem = document.getElementById('sidebarBrandTitle');
  const currentName = currentElem ? currentElem.innerText.trim() : 'MotoTrust';
  const newName = prompt("Ubah Nama Bengkel / Tulisan Brand (saat ini: " + currentName + "):", currentName);
  if (newName === null) return;
  const trimmed = newName.trim();
  if (trimmed === "" || trimmed === currentName) return;

  try {
    const res = await fetch('api/settings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'update', workshop_name: trimmed })
    });
    const json = await res.json();
    if (json.success) {
      showToast('Nama Bengkel Diperbarui', `Nama bengkel berhasil diubah menjadi "${trimmed}"`);
      setTimeout(() => location.reload(), 300);
    } else {
      alert(json.error || 'Gagal mengubah nama bengkel');
    }
  } catch (e) {
    alert('Error: ' + e.message);
  }
}

// Quick Edit Customer directly from Customers page
async function editCustomerDirect(custId, currentName) {
  const newName = prompt("Ubah Nama Pelanggan:", currentName);
  if (newName === null) return;
  const trimmed = newName.trim();
  if (trimmed === "" || trimmed === currentName) return;

  try {
    const res = await fetch('api/customers.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'update', id: custId, name: trimmed })
    });
    const json = await res.json();
    if (json.success) {
      showToast('Nama Diperbarui', `Nama pelanggan berhasil diubah menjadi "${trimmed}"`);
      setTimeout(() => location.reload(), 400);
    } else {
      alert(json.error || 'Gagal mengubah nama pelanggan');
    }
  } catch (e) {
    alert('Error: ' + e.message);
  }
}

// Quick Edit Customer Name (1-Click rename)
async function editCustomerName(jobId, currentName) {
  const newName = prompt("Ubah Nama Pelanggan:", currentName);
  if (newName === null) return;
  const trimmed = newName.trim();
  if (trimmed === "" || trimmed === currentName) return;

  try {
    const res = await fetch('api/jobs.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'quick_rename', job_id: jobId, name: trimmed })
    });
    const json = await res.json();
    if (json.success) {
      showToast('Nama Diperbarui', `Nama pelanggan berhasil diubah menjadi "${trimmed}"`);
      setTimeout(() => location.reload(), 400);
    } else {
      alert(json.error || 'Gagal mengubah nama');
    }
  } catch (e) {
    alert('Error: ' + e.message);
  }
}

// Open Full Edit Modal for Job & Customer
async function openEditJobModal(jobId) {
  try {
    const res = await fetch(`api/jobs.php?id=${jobId}`);
    const json = await res.json();
    if (!json.success || !json.data) {
      alert('Gagal mengambil data SPK');
      return;
    }
    const d = json.data;
    document.getElementById('editJobId').value = d.id;
    document.getElementById('editJobModalTitle').innerText = `Edit SPK ${d.job_code} (${d.customer_name})`;
    document.getElementById('editJobCustomerName').value = d.customer_name || '';
    document.getElementById('editJobCustomerPhone').value = d.customer_phone || '';
    document.getElementById('editJobVehicleBrand').value = d.vehicle_brand || '';
    document.getElementById('editJobVehicleModel').value = d.vehicle_model || '';
    document.getElementById('editJobPlateNumber').value = d.plate_number || '';
    document.getElementById('editJobStatus').value = d.status || 'Pending';
    document.getElementById('editJobServiceType').value = d.service_type || '';
    document.getElementById('editJobLaborCost').value = d.labor_cost || 0;
    document.getElementById('editJobPartsCost').value = d.parts_cost || 0;
    document.getElementById('editJobDescription').value = d.description || '';
    if (d.employee_id) {
      document.getElementById('editJobEmployeeId').value = d.employee_id;
    }

    openModal('modalEditJobCustomer');
  } catch (e) {
    alert('Error: ' + e.message);
  }
}

// Appointment form handler (Image 2)
document.addEventListener('DOMContentLoaded', () => {
  initTheme();

  // Handle Edit Job & Customer form
  const editJobForm = document.getElementById('formEditJobCustomer');
  if (editJobForm) {
    editJobForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(editJobForm);
      const data = Object.fromEntries(fd.entries());
      data.action = 'update';

      try {
        const res = await fetch('api/jobs.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
          showToast('Sukses', 'Data pelanggan dan SPK berhasil diperbarui!');
          closeModal('modalEditJobCustomer');
          setTimeout(() => location.reload(), 400);
        } else {
          alert(result.error || 'Gagal menyimpan perubahan');
        }
      } catch (err) {
        alert('Error: ' + err.message);
      }
    });
  }

  // Handle Add Appointment form
  const aptForm = document.getElementById('formAddAppointment');
  if (aptForm) {
    aptForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(aptForm);
      const data = Object.fromEntries(fd.entries());
      data.action = 'create';

      try {
        const res = await fetch('api/appointments.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
          showToast('Sukses', 'Jadwal servis berhasil ditambahkan!');
          closeModal('modalAddAppointment');
          aptForm.reset();
          if (window.calendarApp) {
            await window.calendarApp.fetchAppointments();
            window.calendarApp.render();
          } else {
            setTimeout(() => location.reload(), 500);
          }
        } else {
          alert(result.error || 'Gagal menyimpan data');
        }
      } catch (err) {
        alert('Terjadi kesalahan: ' + err.message);
      }
    });
  }

  // Handle Add Job form
  const jobForm = document.getElementById('formAddJob');
  if (jobForm) {
    jobForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(jobForm);
      const data = Object.fromEntries(fd.entries());
      data.action = 'create';

      try {
        const res = await fetch('api/jobs.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
          showToast('Sukses', `Perintah Kerja ${result.job_code} berhasil dibuat!`);
          closeModal('modalAddJob');
          jobForm.reset();
          setTimeout(() => location.reload(), 500);
        } else {
          alert(result.error || 'Gagal membuat job order');
        }
      } catch (err) {
        alert('Error: ' + err.message);
      }
    });
  }

  // Setup Global Search
  const searchInput = document.getElementById('globalSearchInput');
  if (searchInput) {
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        const q = encodeURIComponent(searchInput.value.trim());
        if (q) {
          window.location.href = `index.php?page=jobs&search=${q}`;
        }
      }
    });
  }
});

