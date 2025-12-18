// app.js - Frontend JavaScript for dashboard
let assignments = [];
let editingId = null;

// Load data on page load
document.addEventListener('DOMContentLoaded', () => {
    loadStats();
    loadAssignments();
    updateOverdueStatus();
    
    // Auto-update every minute
    setInterval(updateOverdueStatus, 60000);
});

// API Helper
async function apiCall(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(endpoint, options);
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Terjadi kesalahan');
        }
        
        return result;
    } catch (error) {
        console.error('API Error:', error);
        alert(error.message);
        throw error;
    }
}

// Load Statistics
async function loadStats() {
    try {
        const result = await apiCall('api.php?action=stats');
        const stats = result.data;
        
        const completionRate = stats.total > 0 ? Math.round((stats.Selesai / stats.total) * 100) : 0;
        
        document.getElementById('statsCards').innerHTML = `
            <div class="stat-card">
                <div class="stat-icon" style="background: #dbeafe; color: #2563eb;">
                    <svg class="icon" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                </div>
                <p class="stat-label">Total Tugas</p>
                <p class="stat-value">${stats.total}</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #d1fae5; color: #059669;">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <p class="stat-label">Selesai</p>
                <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                    <p class="stat-value">${stats.Selesai}</p>
                    <span style="color: #059669; font-size: 0.875rem;">(${completionRate}%)</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                    <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <p class="stat-label">Dalam Proses</p>
                <p class="stat-value">${stats['Sedang Dikerjakan']}</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
                    <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <p class="stat-label">Terlambat</p>
                <p class="stat-value">${stats.Terlambat}</p>
            </div>
        `;
    } catch (error) {
        document.getElementById('statsCards').innerHTML = '<div class="loading">Gagal memuat statistik</div>';
    }
}

// Load Assignments
async function loadAssignments() {
    try {
        const filter = document.getElementById('filterStatus').value;
        const sort = document.getElementById('sortBy').value;
        
        const result = await apiCall(`api.php?action=list&filter=${filter}&sort=${sort}`);
        assignments = result.data;
        
        renderAssignments();
    } catch (error) {
        document.getElementById('assignmentsList').innerHTML = '<div class="loading">Gagal memuat tugas</div>';
    }
}

// Update Overdue Status
async function updateOverdueStatus() {
    try {
        await apiCall('api.php?action=update_overdue', 'POST');
        await loadStats();
        await loadAssignments();
    } catch (error) {
        console.error('Failed to update overdue status:', error);
    }
}

// Render Assignments
function renderAssignments() {
    const container = document.getElementById('assignmentsList');
    
    if (assignments.length === 0) {
        const filter = document.getElementById('filterStatus').value;
        container.innerHTML = `
            <div class="empty-state">
                <p>${filter === 'all' ? 'Belum ada tugas. Tambahkan tugas pertama Anda!' : `Tidak ada tugas dengan status "${filter}"`}</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <div class="assignments-grid">
            ${assignments.map(assignment => renderAssignmentCard(assignment)).join('')}
        </div>
    `;
}

// Render Assignment Card
function renderAssignmentCard(assignment) {
    const deadline = new Date(assignment.deadline);
    const now = new Date();
    const hoursUntilDeadline = (deadline - now) / (1000 * 60 * 60);
    const isUpcoming = hoursUntilDeadline > 0 && hoursUntilDeadline <= 24;
    const isOverdue = assignment.status === 'Terlambat';
    
    const priorityColors = {
        'Tinggi': 'badge-red',
        'Sedang': 'badge-yellow',
        'Rendah': 'badge-green',
    };
    
    const statusColors = {
        'Belum Mulai': 'badge-gray',
        'Sedang Dikerjakan': 'badge-blue',
        'Selesai': 'badge-green',
        'Terlambat': 'badge-red',
    };
    
    const formatDeadline = new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(deadline);
    
    const formatTimeRemaining = () => {
        if (isOverdue) return 'Terlambat';
        const days = Math.floor(hoursUntilDeadline / 24);
        const hours = Math.floor(hoursUntilDeadline % 24);
        if (days > 0) return `${days} hari ${hours} jam lagi`;
        if (hours > 0) return `${hours} jam lagi`;
        const minutes = Math.floor((hoursUntilDeadline * 60) % 60);
        return `${minutes} menit lagi`;
    };
    
    return `
        <div class="assignment-card ${isOverdue ? 'overdue' : isUpcoming ? 'upcoming' : ''}">
            ${(isOverdue || isUpcoming) ? `
                <div class="alert-badge ${isOverdue ? 'overdue' : 'upcoming'}">
                    <svg class="icon" style="width: 1rem; height: 1rem;" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    ${isOverdue ? 'Tugas Terlambat!' : 'Deadline Segera!'}
                </div>
            ` : ''}
            
            <div style="margin-bottom: 0.75rem;">
                <h3 style="font-size: 1.125rem; margin-bottom: 0.25rem;">${assignment.name}</h3>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #6b7280;">
                    <svg class="icon" style="width: 1rem; height: 1rem;" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    <span>${assignment.course}</span>
                </div>
            </div>

            ${assignment.description ? `
                <p style="color: #6b7280; margin-bottom: 1rem;" class="line-clamp-2">${assignment.description}</p>
            ` : ''}

            <div style="margin-bottom: 1rem;">
                <span class="badge ${priorityColors[assignment.priority]}">${assignment.priority}</span>
                <span class="badge ${statusColors[assignment.status]}">${assignment.status}</span>
            </div>

            <div class="deadline-info" style="margin-bottom: 1rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.5rem; color: #374151; margin-bottom: 0.5rem;">
                    <svg class="icon" style="width: 1rem; height: 1rem; margin-top: 0.125rem;" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <p style="font-size: 0.875rem; word-break: break-word;">${formatDeadline}</p>
                </div>
                ${assignment.status !== 'Selesai' ? `
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: ${isOverdue ? '#dc2626' : isUpcoming ? '#d97706' : '#374151'};">
                        <svg class="icon" style="width: 1rem; height: 1rem;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span style="font-size: 0.875rem;">${formatTimeRemaining()}</span>
                    </div>
                ` : ''}
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button onclick="editAssignment(${assignment.id})" class="btn btn-primary" style="flex: 1; padding: 0.5rem;">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit
                </button>
                <button onclick="deleteAssignment(${assignment.id})" class="btn btn-secondary" style="padding: 0.5rem; color: #dc2626; border-color: #fecaca;">
                    <svg class="icon" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        </div>
    `;
}

// Render Form
function renderForm(containerId, isEditing = false) {
    const container = document.getElementById(containerId);
    const assignment = isEditing ? assignments.find(a => a.id === editingId) : null;
    
    // Convert MySQL datetime to input format
    let deadlineValue = '';
    if (assignment && assignment.deadline) {
        const d = new Date(assignment.deadline);
        deadlineValue = d.toISOString().slice(0, 16);
    }
    
    container.innerHTML = `
        <form id="assignmentForm-${containerId}" onsubmit="handleFormSubmit(event, '${containerId}', ${isEditing})">
            <div class="form-group">
                <label for="name-${containerId}">Nama Tugas *</label>
                <input type="text" id="name-${containerId}" placeholder="Masukkan nama tugas" value="${assignment?.name || ''}" required>
            </div>
            <div class="form-group">
                <label for="course-${containerId}">Mata Kuliah *</label>
                <input type="text" id="course-${containerId}" placeholder="Masukkan mata kuliah" value="${assignment?.course || ''}" required>
            </div>
            <div class="form-group">
                <label for="description-${containerId}">Deskripsi</label>
                <textarea id="description-${containerId}" rows="3" placeholder="Masukkan deskripsi tugas (opsional)">${assignment?.description || ''}</textarea>
            </div>
            <div class="form-group">
                <label for="deadline-${containerId}">Deadline *</label>
                <input type="datetime-local" id="deadline-${containerId}" value="${deadlineValue}" required>
            </div>
            <div class="form-group">
                <label for="priority-${containerId}">Prioritas *</label>
                <select id="priority-${containerId}" required>
                    <option value="Rendah" ${assignment?.priority === 'Rendah' ? 'selected' : ''}>Rendah</option>
                    <option value="Sedang" ${assignment?.priority === 'Sedang' ? 'selected' : ''}>Sedang</option>
                    <option value="Tinggi" ${assignment?.priority === 'Tinggi' ? 'selected' : ''}>Tinggi</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status-${containerId}">Status *</label>
                <select id="status-${containerId}" required>
                    <option value="Belum Mulai" ${assignment?.status === 'Belum Mulai' ? 'selected' : ''}>Belum Mulai</option>
                    <option value="Sedang Dikerjakan" ${assignment?.status === 'Sedang Dikerjakan' ? 'selected' : ''}>Sedang Dikerjakan</option>
                    <option value="Selesai" ${assignment?.status === 'Selesai' ? 'selected' : ''}>Selesai</option>
                    <option value="Terlambat" ${assignment?.status === 'Terlambat' ? 'selected' : ''}>Terlambat</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem; padding-top: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    ${isEditing ? 'Simpan Perubahan' : 'Tambah Tugas'}
                </button>
                ${isEditing ? `<button type="button" onclick="cancelEdit()" class="btn btn-secondary">Batal</button>` : ''}
            </div>
        </form>
    `;
}

// Handle Form Submit
async function handleFormSubmit(event, containerId, isEditing) {
    event.preventDefault();
    
    const formData = {
        name: document.getElementById(`name-${containerId}`).value,
        course: document.getElementById(`course-${containerId}`).value,
        description: document.getElementById(`description-${containerId}`).value,
        deadline: document.getElementById(`deadline-${containerId}`).value.replace('T', ' ') + ':00',
        priority: document.getElementById(`priority-${containerId}`).value,
        status: document.getElementById(`status-${containerId}`).value,
    };
    
    try {
        if (isEditing && editingId) {
            formData.id = editingId;
            await apiCall('api.php?action=update', 'POST', formData);
        } else {
            await apiCall('api.php?action=create', 'POST', formData);
        }
        
        editingId = null;
        closeMobileSheet();
        await loadStats();
        await loadAssignments();
        renderAll();
    } catch (error) {
        // Error already handled in apiCall
    }
}

// Edit Assignment
function editAssignment(id) {
    editingId = id;
    document.getElementById('sheetTitle').textContent = 'Edit Tugas';
    openMobileSheet();
    renderAll();
}

// Delete Assignment
async function deleteAssignment(id) {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        try {
            await apiCall('api.php?action=delete', 'POST', { id });
            await loadStats();
            await loadAssignments();
        } catch (error) {
            // Error already handled in apiCall
        }
    }
}

// Cancel Edit
function cancelEdit() {
    editingId = null;
    closeMobileSheet();
    renderAll();
}

// Mobile Sheet Functions
function openMobileSheet() {
    document.getElementById('modalOverlay').classList.add('active');
    document.getElementById('bottomSheet').classList.add('active');
}

function closeMobileSheet() {
    document.getElementById('modalOverlay').classList.remove('active');
    document.getElementById('bottomSheet').classList.remove('active');
    editingId = null;
    document.getElementById('sheetTitle').textContent = 'Tambah Tugas Baru';
}

// Event Listeners
document.getElementById('openMobileMenu').addEventListener('click', () => {
    editingId = null;
    openMobileSheet();
    renderAll();
});

document.getElementById('fabBtn').addEventListener('click', () => {
    editingId = null;
    openMobileSheet();
    renderAll();
});

document.getElementById('closeSheet').addEventListener('click', closeMobileSheet);
document.getElementById('modalOverlay').addEventListener('click', closeMobileSheet);

document.getElementById('filterStatus').addEventListener('change', loadAssignments);
document.getElementById('sortBy').addEventListener('change', loadAssignments);

// Render All
function renderAll() {
    renderForm('sidebarForm', !!editingId);
    renderForm('mobileForm', !!editingId);
}