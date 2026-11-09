<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$workshopName = getSetting('workshop_name', 'MotoTrust');
$workshopTagline = getSetting('workshop_tagline', 'Dealer Servis Kendaraan');
?>
<aside class="app-sidebar">
  <div class="sidebar-brand" style="display:flex; align-items:center; justify-content:space-between;">
    <a href="index.php?page=dashboard" style="display:flex; align-items:center; gap:12px; text-decoration:none; color:inherit; flex:1; min-width:0;">
      <div class="brand-icon">
        <!-- MotoTrust Gear Icon -->
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
      </div>
      <div class="brand-text" style="min-width:0; overflow:hidden;">
        <span class="brand-title" id="sidebarBrandTitle" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block;"><?= htmlspecialchars($workshopName) ?></span>
        <span class="brand-subtitle" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block;"><?= htmlspecialchars($workshopTagline) ?></span>
      </div>
    </a>
    <button type="button" onclick="editWorkshopName(event)" style="background:rgba(255,255,255,0.06); border:1px solid var(--border-color); border-radius:6px; color:var(--text-muted); cursor:pointer; font-size:12px; padding:4px 7px; margin-left:6px;" title="Ganti Tulisan MotoTrust / Nama Bengkel">
      ✏️
    </button>
  </div>

  <nav class="sidebar-nav">
    <a href="index.php?page=dashboard" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
      </svg>
      <span>Dashboard</span>
    </a>

    <a href="index.php?page=jobs" class="nav-link <?= $currentPage === 'jobs' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
      </svg>
      <span>Daftar SPK</span>
      <span class="nav-badge">10</span>
    </a>

    <a href="index.php?page=calendar" class="nav-link <?= $currentPage === 'calendar' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
      </svg>
      <span>Kalender Servis</span>
    </a>

    <a href="index.php?page=customers" class="nav-link <?= $currentPage === 'customers' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
      </svg>
      <span>Pelanggan</span>
    </a>

    <a href="index.php?page=inventory" class="nav-link <?= $currentPage === 'inventory' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
      </svg>
      <span>Suku Cadang</span>
    </a>

    <a href="index.php?page=employees" class="nav-link <?= $currentPage === 'employees' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
      </svg>
      <span>Mekanik & Staf</span>
    </a>

    <a href="index.php?page=reports" class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
      </svg>
      <span>Laporan</span>
    </a>

    <a href="index.php?page=settings" class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
      </svg>
      <span>Pengaturan</span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <span>Sistem Bengkel v2.4</span>
    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#22c55e;" title="Sistem Online (Aktif)"></span>
  </div>
</aside>

