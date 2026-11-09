/**
 * Bayou Automotive - Interactive Service Appointment Calendar
 * Matching Image 2 design & functionality
 */

class BayouCalendar {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.currentDate = new Date();
    this.currentMonth = this.currentDate.getMonth();
    this.currentYear = this.currentDate.getFullYear();
    this.viewMode = 'month'; // 'month' or 'week'
    this.events = [];
    this.colorClasses = ['event-green', 'event-blue', 'event-orange', 'event-red', 'event-purple'];
    
    this.init();
  }

  async init() {
    this.renderHeader();
    await this.fetchAppointments();
    this.render();
  }

  renderHeader() {
    const monthNames = [
      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    const monthLabel = `${monthNames[this.currentMonth]} ${this.currentYear}`;

    const headerHtml = `
      <div class="calendar-view-header">
        <div class="calendar-title-group">
          <h2 class="calendar-page-title">Kalender Servis</h2>
          <button class="btn btn-primary" onclick="BayouCalendar.openAddModal()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            + Tambah Jadwal Booking
          </button>
        </div>
        <div class="calendar-nav-group">
          <button class="btn btn-secondary btn-sm" onclick="window.calendarApp.goToToday()">Hari Ini</button>
          <div style="display:flex; align-items:center; gap:8px;">
            <button class="btn btn-outline btn-sm" onclick="window.calendarApp.prevMonth()">
              <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <span class="calendar-month-label" id="calendarMonthLabel">${monthLabel}</span>
            <button class="btn btn-outline btn-sm" onclick="window.calendarApp.nextMonth()">
              <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
          <div class="view-toggle">
            <button class="view-toggle-btn ${this.viewMode === 'month' ? 'active' : ''}" onclick="window.calendarApp.switchView('month')">Bulan</button>
            <button class="view-toggle-btn ${this.viewMode === 'week' ? 'active' : ''}" onclick="window.calendarApp.switchView('week')">Minggu</button>
          </div>
        </div>
      </div>
      <div class="calendar-grid-wrapper">
        <div class="calendar-weekdays">
          <div>MIN</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
        </div>
        <div class="calendar-days" id="calendarDaysGrid"></div>
      </div>
    `;

    this.container.innerHTML = headerHtml;
  }

  async fetchAppointments() {
    try {
      const monthStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}`;
      const res = await fetch(`api/appointments.php?month=${monthStr}`);
      const json = await res.json();
      if (json.success) {
        this.events = json.data;
      }
    } catch (e) {
      console.error("Failed to load appointments:", e);
    }
  }

  render() {
    const grid = document.getElementById('calendarDaysGrid');
    if (!grid) return;
    grid.innerHTML = '';

    const firstDayIndex = new Date(this.currentYear, this.currentMonth, 1).getDay();
    const lastDayOfMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
    const prevLastDay = new Date(this.currentYear, this.currentMonth, 0).getDate();

    const today = new Date();
    const isCurrentMonth = today.getFullYear() === this.currentYear && today.getMonth() === this.currentMonth;

    // Previous month filler days
    for (let x = firstDayIndex; x > 0; x--) {
      const dayNum = prevLastDay - x + 1;
      const cell = document.createElement('div');
      cell.className = 'calendar-day-cell other-month';
      cell.innerHTML = `<span class="calendar-day-number">${dayNum}</span>`;
      grid.appendChild(cell);
    }

    // Current month days
    for (let day = 1; day <= lastDayOfMonth; day++) {
      const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const cell = document.createElement('div');
      cell.className = 'calendar-day-cell';
      if (isCurrentMonth && day === today.getDate()) {
        cell.classList.add('today');
      }

      cell.innerHTML = `<span class="calendar-day-number">${day}</span><div class="calendar-events-list"></div>`;
      const eventsList = cell.querySelector('.calendar-events-list');

      // Filter events for this day
      const dayEvents = this.events.filter(e => e.appointment_date === dateStr);
      const maxDisplay = 3;

      dayEvents.slice(0, maxDisplay).forEach((evt, idx) => {
        const colorClass = this.colorClasses[(evt.id + idx) % this.colorClasses.length];
        const pill = document.createElement('div');
        pill.className = `calendar-event-pill ${colorClass}`;
        pill.innerHTML = `<span>${evt.customer_name}</span> <span>${evt.appointment_time}</span>`;
        pill.title = `${evt.customer_name} - ${evt.vehicle_info || ''} (${evt.delivery_type})`;
        pill.onclick = () => BayouCalendar.showEventDetail(evt);
        eventsList.appendChild(pill);
      });

      if (dayEvents.length > maxDisplay) {
        const moreBtn = document.createElement('div');
        moreBtn.className = 'event-more-btn';
        moreBtn.innerText = `+${dayEvents.length - maxDisplay} Lainnya`;
        moreBtn.onclick = () => BayouCalendar.showDaySummary(dateStr, dayEvents);
        eventsList.appendChild(moreBtn);
      }

      // Quick click on empty cell to add appointment for this day
      cell.ondblclick = () => {
        BayouCalendar.openAddModal(dateStr);
      };

      grid.appendChild(cell);
    }

    // Next month filler days to complete grid (42 cells)
    const totalRendered = firstDayIndex + lastDayOfMonth;
    const remaining = 35 - totalRendered > 0 ? 35 - totalRendered : (42 - totalRendered);
    for (let j = 1; j <= remaining; j++) {
      const cell = document.createElement('div');
      cell.className = 'calendar-day-cell other-month';
      cell.innerHTML = `<span class="calendar-day-number">${j}</span>`;
      grid.appendChild(cell);
    }
  }

  async prevMonth() {
    this.currentMonth--;
    if (this.currentMonth < 0) {
      this.currentMonth = 11;
      this.currentYear--;
    }
    this.renderHeader();
    await this.fetchAppointments();
    this.render();
  }

  async nextMonth() {
    this.currentMonth++;
    if (this.currentMonth > 11) {
      this.currentMonth = 0;
      this.currentYear++;
    }
    this.renderHeader();
    await this.fetchAppointments();
    this.render();
  }

  async goToToday() {
    const today = new Date();
    this.currentMonth = today.getMonth();
    this.currentYear = today.getFullYear();
    this.renderHeader();
    await this.fetchAppointments();
    this.render();
  }

  switchView(mode) {
    this.viewMode = mode;
    this.renderHeader();
    this.render();
  }

  static openAddModal(selectedDate = null) {
    const modal = document.getElementById('modalAddAppointment');
    if (!modal) return;
    if (selectedDate) {
      const dateInput = document.getElementById('aptDateInput');
      if (dateInput) dateInput.value = selectedDate;
    }
    modal.classList.add('show');
  }

  static closeAddModal() {
    const modal = document.getElementById('modalAddAppointment');
    if (modal) modal.classList.remove('show');
  }

  static showEventDetail(evt) {
    const detailModal = document.getElementById('modalAptDetail');
    if (!detailModal) return;

    document.getElementById('detailAptCustomer').innerText = evt.customer_name;
    document.getElementById('detailAptPhone').innerText = evt.customer_phone || '-';
    document.getElementById('detailAptVehicle').innerText = evt.vehicle_info || '-';
    document.getElementById('detailAptDate').innerText = `${evt.appointment_date} pukul ${evt.appointment_time}`;
    document.getElementById('detailAptDelivery').innerText = evt.delivery_type;
    document.getElementById('detailAptLoaner').innerText = evt.loaner_needed ? 'Ya (Butuh Mobil Pinjaman)' : 'Tidak';
    document.getElementById('detailAptDesc').innerText = evt.description || '-';
    document.getElementById('detailAptStatus').innerHTML = `<span class="status-badge badge-${evt.status.toLowerCase()}">${evt.status}</span>`;

    const convertBtn = document.getElementById('btnConvertToJob');
    if (convertBtn) {
      convertBtn.onclick = () => BayouCalendar.convertToJob(evt.id);
    }

    const deleteBtn = document.getElementById('btnDeleteApt');
    if (deleteBtn) {
      deleteBtn.onclick = () => BayouCalendar.deleteAppointment(evt.id);
    }

    detailModal.classList.add('show');
  }

  static async convertToJob(aptId) {
    if (!confirm("Konfirmasi: Buat Perintah Kerja (Job Order) dari Booking Servis ini?")) return;
    try {
      const res = await fetch('api/appointments.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'convert_to_job', appointment_id: aptId })
      });
      const data = await res.json();
      if (data.success) {
        showToast("Berhasil", data.message);
        document.getElementById('modalAptDetail').classList.remove('show');
        if (window.calendarApp) {
          await window.calendarApp.fetchAppointments();
          window.calendarApp.render();
        }
      } else {
        alert(data.error || "Gagal mengonversi booking.");
      }
    } catch (e) {
      alert("Error: " + e.message);
    }
  }

  static async deleteAppointment(aptId) {
    if (!confirm("Hapus jadwal booking ini?")) return;
    try {
      const res = await fetch('api/appointments.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id: aptId })
      });
      const data = await res.json();
      if (data.success) {
        showToast("Berhasil", "Booking berhasil dihapus.");
        document.getElementById('modalAptDetail').classList.remove('show');
        if (window.calendarApp) {
          await window.calendarApp.fetchAppointments();
          window.calendarApp.render();
        }
      }
    } catch (e) {
      alert("Error: " + e.message);
    }
  }
}

window.BayouCalendar = BayouCalendar;

