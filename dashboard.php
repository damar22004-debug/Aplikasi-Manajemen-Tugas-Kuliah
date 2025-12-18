<?php
// dashboard.php
require_once 'config.php';

requireLogin();

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manajemen Tugas Kuliah</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f9fafb;
            color: #111827;
            line-height: 1.5;
        }

        .hidden {
            display: none !important;
        }

        .dashboard {
            min-height: 100vh;
            display: flex;
        }

        /* Header Mobile */
        .mobile-header {
            display: block;
            position: sticky;
            top: 0;
            z-index: 40;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
        }

        .mobile-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Sidebar */
        .sidebar {
            display: none;
            width: 24rem;
            background: white;
            border-right: 1px solid #e5e7eb;
            overflow-y: auto;
        }

        .sidebar-content {
            padding: 1.5rem;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            overflow-y: auto;
        }

        .content-wrapper {
            max-width: 80rem;
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            transition: box-shadow 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            display: inline-flex;
            padding: 0.5rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.875rem;
            font-weight: 600;
            color: #111827;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        input[type="text"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        textarea {
            resize: none;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            color: #374151;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        /* Assignments Grid */
        .assignments-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .assignment-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.25rem;
            transition: all 0.2s;
        }

        .assignment-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
        }

        .assignment-card.overdue {
            border-color: #fca5a5;
            background: #fef2f2;
        }

        .assignment-card.upcoming {
            border-color: #fed7aa;
            background: #fffbeb;
        }

        .alert-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .alert-badge.overdue {
            background: #fee2e2;
            color: #dc2626;
        }

        .alert-badge.upcoming {
            background: #fed7aa;
            color: #d97706;
        }

        .badge {
            display: inline-flex;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            border: 1px solid;
            font-size: 0.875rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .badge-red {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .badge-yellow {
            background: #fef3c7;
            color: #d97706;
            border-color: #fde68a;
        }

        .badge-green {
            background: #d1fae5;
            color: #059669;
            border-color: #a7f3d0;
        }

        .badge-gray {
            background: #f3f4f6;
            color: #6b7280;
            border-color: #e5e7eb;
        }

        .badge-blue {
            background: #dbeafe;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .deadline-info {
            background: #f9fafb;
            border-radius: 0.5rem;
            padding: 0.75rem;
            margin: 1rem 0;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        /* FAB Button */
        .fab {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 3.5rem;
            height: 3.5rem;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 50%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 30;
        }

        .fab:hover {
            background: #2563eb;
        }

        /* Modal/Drawer */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            display: none;
        }

        .modal-overlay.active {
            display: block;
        }

        .bottom-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 1rem 1rem 0 0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
            z-index: 50;
            max-height: 85vh;
            transform: translateY(100%);
            transition: transform 0.3s;
        }

        .bottom-sheet.active {
            transform: translateY(0);
        }

        .bottom-sheet-header {
            position: sticky;
            top: 0;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
            border-radius: 1rem 1rem 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bottom-sheet-content {
            overflow-y: auto;
            padding: 1rem;
            max-height: calc(85vh - 60px);
        }

        .empty-state {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 3rem;
            text-align: center;
        }

        .empty-state p {
            color: #6b7280;
        }

        .icon {
            width: 1.25rem;
            height: 1.25rem;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        /* Responsive */
        @media (min-width: 768px) {
            .mobile-header {
                display: none;
            }

            .sidebar {
                display: block;
            }

            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .fab {
                display: none;
            }
        }

        @media (min-width: 1024px) {
            .assignments-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Mobile Header -->
        <div class="mobile-header">
            <div class="mobile-header-content">
                <div>
                    <h1 style="font-size: 1.25rem;">Manajemen Tugas</h1>
                    <p style="color: #6b7280; font-size: 0.875rem;">Halo, <?php echo htmlspecialchars($username); ?>!</p>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="logout.php" class="btn btn-secondary" style="padding: 0.5rem; color: #dc2626; text-decoration: none;">
                        <svg class="icon" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    </a>
                    <button id="openMobileMenu" class="btn btn-secondary" style="padding: 0.5rem;">
                        <svg class="icon" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-content">
                <div class="sidebar-header">
                    <div>
                        <h1 style="font-size: 1.25rem;">Manajemen Tugas Kuliah</h1>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">Halo, <?php echo htmlspecialchars($username); ?>!</p>
                    </div>
                    <a href="logout.php" class="btn btn-secondary" style="padding: 0.5rem; color: #dc2626; text-decoration: none;" title="Logout">
                        <svg class="icon" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    </a>
                </div>
                <div id="sidebarForm"></div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-wrapper">
                <!-- Statistics -->
                <div id="statsCards" class="stats-grid">
                    <div class="loading">Memuat statistik...</div>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <svg class="icon" viewBox="0 0 24 24" style="color: #6b7280;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        <span style="color: #374151; font-weight: 500;">Filter & Urutkan</span>
                    </div>
                    <div class="filter-grid">
                        <div>
                            <label for="filterStatus" style="display: block; color: #6b7280; font-size: 0.875rem; margin-bottom: 0.375rem;">Filter Status</label>
                            <select id="filterStatus">
                                <option value="all">Semua Tugas</option>
                                <option value="Belum Mulai">Belum Mulai</option>
                                <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Terlambat">Terlambat</option>
                            </select>
                        </div>
                        <div>
                            <label for="sortBy" style="display: block; color: #6b7280; font-size: 0.875rem; margin-bottom: 0.375rem;">Urutkan Berdasarkan</label>
                            <select id="sortBy">
                                <option value="deadline">Deadline</option>
                                <option value="priority">Prioritas</option>
                                <option value="recent">Terbaru</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Assignments List -->
                <div id="assignmentsList">
                    <div class="loading">Memuat tugas...</div>
                </div>
            </div>
        </main>

        <!-- FAB Button -->
        <button id="fabBtn" class="fab">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </button>

        <!-- Mobile Modal -->
        <div id="modalOverlay" class="modal-overlay"></div>
        <div id="bottomSheet" class="bottom-sheet">
            <div class="bottom-sheet-header">
                <h2 id="sheetTitle" style="font-size: 1.125rem;">Tambah Tugas Baru</h2>
                <button id="closeSheet" class="btn btn-secondary" style="padding: 0.5rem;">
                    <svg class="icon" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="bottom-sheet-content" id="mobileForm"></div>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>