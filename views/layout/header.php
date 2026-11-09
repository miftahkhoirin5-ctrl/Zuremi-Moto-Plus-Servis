<?php
require_once __DIR__ . '/../../config/app.php';
$currentPage = $_GET['page'] ?? 'dashboard';
$workshopName = getSetting('workshop_name', 'MotoTrust');
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($workshopName) ?> - Bengkel Resmi & Dealer Servis Kendaraan</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/print.css" media="print">
</head>
<body>
<div class="app-layout">
  <!-- Sidebar -->
  <?php include __DIR__ . '/sidebar.php'; ?>

  <!-- Main Content Wrapper -->
  <div class="app-main">
    <!-- Header -->
    <header class="app-header">
      <div class="header-search">
        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input type="text" id="globalSearchInput" placeholder="Cari data SPK, pelanggan, mobil, suku cadang...">
      </div>

      <div class="header-actions">
        <!-- Dual Theme Toggle (MotoTrust Dark / Bayou Light) -->
        <button class="theme-toggle-btn" onclick="toggleTheme()" title="Ganti Tema (Mode Gelap / Mode Terang)">
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
          </svg>
          <span id="themeToggleLabel">Gelap</span>
        </button>

        <!-- Notification Bell with Orange Dot -->
        <button class="btn-icon" title="Notifikasi Bengkel" onclick="showToast('Notifikasi', '3 SPK Menunggu Persetujuan Mekanik')">
          <span class="badge-dot"></span>
          <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
          </svg>
        </button>

        <!-- User Profile Pill (Admin / Angelina) -->
        <div class="user-profile-btn" onclick="openModal('modalUserProfile')">
          <div class="user-avatar">
            <span><?= htmlspecialchars(strtoupper(substr($workshopName, 0, 2))) ?></span>
          </div>
          <span class="user-name">Admin</span>
          <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </div>
      </div>
    </header>

    <!-- Page Content Container -->
    <div class="page-container">

