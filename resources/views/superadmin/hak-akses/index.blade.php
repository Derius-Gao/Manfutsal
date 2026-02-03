@extends('layouts.app')

@section('title', 'Hak Akses')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-shield-alt me-2"></i>Manajemen Hak Akses</h5>
                <div>
                    <button class="btn btn-success" onclick="exportRoles()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    <button class="btn btn-primary" onclick="refreshData()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Role Hierarchy Info -->
                <div class="alert alert-info mb-4">
                    <h6><i class="fas fa-info-circle me-2"></i>Struktur Hak Akses</h6>
                    <p class="mb-2">Sistem menggunakan Role Hierarchy di mana setiap role memiliki semua akses dari role di bawahnya:</p>
                    <div class="row">
                        <div class="col-md-3">
                            <span class="badge bg-dark">SuperAdmin</span>
                            <ul class="small mt-2">
                                <li>Semua akses Admin</li>
                                <li>Hak Akses Management</li>
                                <li>Web Setting</li>
                                <li>All System Logs</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-danger">Admin</span>
                            <ul class="small mt-2">
                                <li>Semua akses Manager</li>
                                <li>CRUD Users</li>
                                <li>CRUD Lapangan</li>
                                <li>All Bookings</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-warning">Manager</span>
                            <ul class="small mt-2">
                                <li>Semua akses Customer</li>
                                <li>Konfirmasi Booking</li>
                                <li>Lihat Keuangan</li>
                                <li>Activity Log</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-info">Customer</span>
                            <ul class="small mt-2">
                                <li>Register & Login</li>
                                <li>Cari & Booking Lapangan</li>
                                <li>Riwayat Booking</li>
                                <li>Upload Pembayaran</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Role Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-dark text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-user-shield me-2"></i>Super Admin</h5>
                                <h3 id="superadmin-count">1</h3>
                                <small>Administrator tertinggi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-user-tie me-2"></i>Admin</h5>
                                <h3 id="admin-count">3</h3>
                                <small>System administrator</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-user-cog me-2"></i>Manager</h5>
                                <h3 id="manager-count">5</h3>
                                <small>Operational manager</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-users me-2"></i>Customer</h5>
                                <h3 id="customer-count">39</h3>
                                <small>Pengguna biasa</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Role Management -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6>Manajemen Role User</h6>
                        <div>
                            <select class="form-select form-select-sm d-inline-block" id="filter-role" style="width: auto;">
                                <option value="">Semua Role</option>
                                <option value="superadmin">Super Admin</option>
                                <option value="admin">Admin</option>
                                <option value="manager">Manager</option>
                                <option value="customer">Customer</option>
                            </select>
                            <button class="btn btn-sm btn-primary ms-2" onclick="applyFilter()">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role Saat Ini</th>
                                        <th>Status</th>
                                        <th>Terdaftar</th>
                                        <th>Last Login</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="user-role-tbody">
                                    <!-- Sample data -->
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                    SA
                                                </div>
                                                <div>
                                                    <div>Super Admin</div>
                                                    <small class="text-muted">superadmin@manfutsal.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">Super Admin</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Aktif</span>
                                        </td>
                                        <td><small>2024-01-01</small></td>
                                        <td><small>2 jam yang lalu</small></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" onclick="showUserDetail(1)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning" onclick="changeUserRole(1)" disabled>
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                    AU
                                                </div>
                                                <div>
                                                    <div>Admin User</div>
                                                    <small class="text-muted">admin@manfutsal.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">Admin</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Aktif</span>
                                        </td>
                                        <td><small>2024-01-01</small></td>
                                        <td><small>1 hari yang lalu</small></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" onclick="showUserDetail(2)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning" onclick="changeUserRole(2)">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                    MU
                                                </div>
                                                <div>
                                                    <div>Manager User</div>
                                                    <small class="text-muted">manager@manfutsal.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Manager</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Aktif</span>
                                        </td>
                                        <td><small>2024-01-02</small></td>
                                        <td><small>3 jam yang lalu</small></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" onclick="showUserDetail(3)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning" onclick="changeUserRole(3)">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                    JD
                                                </div>
                                                <div>
                                                    <div>John Doe</div>
                                                    <small class="text-muted">john@example.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">Customer</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Aktif</span>
                                        </td>
                                        <td><small>2024-01-10</small></td>
                                        <td><small>5 jam yang lalu</small></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" onclick="showUserDetail(4)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning" onclick="changeUserRole(4)">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation" class="mt-3">
                            <ul class="pagination pagination-sm justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Role Change History -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6>Riwayat Perubahan Role</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline" id="role-history">
                            <!-- Sample history -->
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">Role Diubah</h6>
                                            <p class="text-muted mb-1">
                                                User <strong>Jane Smith</strong> diubah dari <span class="badge bg-info">Customer</span> menjadi <span class="badge bg-warning">Manager</span>
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>2 hari yang lalu
                                                <i class="fas fa-user me-1"></i>Super Admin
                                            </small>
                                        </div>
                                        <span class="badge bg-warning">Warning</span>
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-marker bg-success">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">Admin Baru Ditambahkan</h6>
                                            <p class="text-muted mb-1">
                                                User <strong>Admin User</strong> ditambahkan sebagai <span class="badge bg-danger">Admin</span>
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>1 minggu yang lalu
                                                <i class="fas fa-user me-1"></i>Super Admin
                                            </small>
                                        </div>
                                        <span class="badge bg-success">Success</span>
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger">
                                    <i class="fas fa-user-minus"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">Role Diturunkan</h6>
                                            <p class="text-muted mb-1">
                                                User <strong>Former Admin</strong> diubah dari <span class="badge bg-danger">Admin</span> menjadi <span class="badge bg-info">Customer</span> - Alasan: Pelanggaran kebijakan
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>2 minggu yang lalu
                                                <i class="fas fa-user me-1"></i>Super Admin
                                            </small>
                                        </div>
                                        <span class="badge bg-danger">Danger</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Detail Modal -->
<div class="modal fade" id="userDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="user-detail-content">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Role User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="changeRoleForm">
                    @csrf
                    <input type="hidden" id="change_user_id" name="user_id">
                    
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <div class="card bg-light">
                            <div class="card-body" id="selected-user-info">
                                <!-- User info will be displayed here -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="current_role" class="form-label">Role Saat Ini</label>
                        <input type="text" class="form-control" id="current_role" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_role" class="form-label">Role Baru</label>
                        <select class="form-select" id="new_role" name="new_role" required>
                            <option value="">Pilih Role Baru</option>
                            <option value="customer">Customer</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role_change_reason" class="form-label">Alasan Perubahan</label>
                        <textarea class="form-control" id="role_change_reason" name="reason" rows="3" placeholder="Masukkan alasan perubahan role..." required></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Peringatan</h6>
                        <p class="mb-0">Mengubah role user akan mempengaruhi akses user ke sistem. Pastikan Anda telah mempertimbangkan dengan baik sebelum melakukan perubahan.</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="saveRoleChange()">
                    <i class="fas fa-exchange-alt me-2"></i>Ubah Role
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
</style>

@section('scripts')
<script>
// Sample user data
const userData = [
    {
        id: 1,
        name: 'Super Admin',
        email: 'superadmin@manfutsal.com',
        role: 'superadmin',
        is_active: true,
        created_at: '2024-01-01',
        last_login: '2 jam yang lalu'
    },
    {
        id: 2,
        name: 'Admin User',
        email: 'admin@manfutsal.com',
        role: 'admin',
        is_active: true,
        created_at: '2024-01-01',
        last_login: '1 hari yang lalu'
    },
    {
        id: 3,
        name: 'Manager User',
        email: 'manager@manfutsal.com',
        role: 'manager',
        is_active: true,
        created_at: '2024-01-02',
        last_login: '3 jam yang lalu'
    },
    {
        id: 4,
        name: 'John Doe',
        email: 'john@example.com',
        role: 'customer',
        is_active: true,
        created_at: '2024-01-10',
        last_login: '5 jam yang lalu'
    }
];

function showUserDetail(id) {
    const user = userData.find(u => u.id === id);
    if (!user) return;
    
    const content = `
        <div class="text-center mb-3">
            <div class="avatar bg-${getRoleBadgeColor(user.role)} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 32px;">
                ${user.name.split(' ').map(n => n[0]).join('')}
            </div>
            <h5 class="mt-3">${user.name}</h5>
            <span class="badge bg-${getRoleBadgeColor(user.role)}">${user.role.toUpperCase()}</span>
        </div>
        
        <table class="table table-sm">
            <tr><td>Email</td><td>${user.email}</td></tr>
            <tr><td>Status</td><td>${user.is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>'}</td></tr>
            <tr><td>Terdaftar</td><td>${user.created_at}</td></tr>
            <tr><td>Last Login</td><td>${user.last_login}</td></tr>
            <tr><td>Akses</td><td>${getRoleAccess(user.role)}</td></tr>
        </table>
    `;
    
    document.getElementById('user-detail-content').innerHTML = content;
    new bootstrap.Modal(document.getElementById('userDetailModal')).show();
}

function changeUserRole(id) {
    const user = userData.find(u => u.id === id);
    if (!user) return;
    
    // Prevent changing role of the only superadmin
    if (user.role === 'superadmin') {
        Swal.fire('Error', 'Tidak dapat mengubah role Super Admin', 'error');
        return;
    }
    
    document.getElementById('change_user_id').value = user.id;
    document.getElementById('selected-user-info').innerHTML = `
        <div class="d-flex align-items-center">
            <div class="avatar bg-${getRoleBadgeColor(user.role)} text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; font-size: 14px;">
                ${user.name.split(' ').map(n => n[0]).join('')}
            </div>
            <div>
                <strong>${user.name}</strong><br>
                <small class="text-muted">${user.email}</small>
            </div>
        </div>
    `;
    document.getElementById('current_role').value = user.role.charAt(0).toUpperCase() + user.role.slice(1);
    
    // Set available options (exclude current role)
    const select = document.getElementById('new_role');
    select.innerHTML = '<option value="">Pilih Role Baru</option>';
    const roles = ['customer', 'manager', 'admin', 'superadmin'];
    roles.forEach(role => {
        if (role !== user.role) {
            select.innerHTML += `<option value="${role}">${role.charAt(0).toUpperCase() + role.slice(1)}</option>`;
        }
    });
    
    document.getElementById('role_change_reason').value = '';
    
    new bootstrap.Modal(document.getElementById('changeRoleModal')).show();
}

function saveRoleChange() {
    const form = document.getElementById('changeRoleForm');
    const formData = new FormData(form);
    
    if (!formData.get('new_role') || !formData.get('reason')) {
        Swal.fire('Error', 'Role baru dan alasan harus diisi', 'error');
        return;
    }
    
    const userId = formData.get('user_id');
    const newRole = formData.get('new_role');
    const reason = formData.get('reason');
    
    const user = userData.find(u => u.id == userId);
    
    Swal.fire({
        title: 'Konfirmasi Perubahan Role?',
        html: `
            <p>Apakah Anda yakin ingin mengubah role user <strong>${user.name}</strong>?</p>
            <p>Dari: <span class="badge bg-${getRoleBadgeColor(user.role)}">${user.role.toUpperCase()}</span></p>
            <p>Menjadi: <span class="badge bg-${getRoleBadgeColor(newRole)}">${newRole.toUpperCase()}</span></p>
            <p><strong>Alasan:</strong> ${reason}</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processRoleChange(userId, newRole, reason);
        }
    });
}

function processRoleChange(userId, newRole, reason) {
    Swal.fire({
        title: 'Changing Role...',
        text: 'Sedang mengubah role user',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'Role user berhasil diubah', 'success').then(() => {
            bootstrap.Modal.getInstance(document.getElementById('changeRoleModal')).hide();
            addRoleChangeHistory(userId, newRole, reason);
            updateRoleStats();
            location.reload();
        });
    }, 1500);
}

function addRoleChangeHistory(userId, newRole, reason) {
    const user = userData.find(u => u.id == userId);
    console.log('Role change history added:', {
        user: user.name,
        oldRole: user.role,
        newRole: newRole,
        reason: reason,
        timestamp: new Date()
    });
}

function updateRoleStats() {
    // Simulate updating role statistics
    document.getElementById('superadmin-count').textContent = '1';
    document.getElementById('admin-count').textContent = '4';
    document.getElementById('manager-count').textContent = '4';
    document.getElementById('customer-count').textContent = '40';
}

function applyFilter() {
    const role = document.getElementById('filter-role').value;
    console.log('Filtering by role:', role);
    
    Swal.fire({
        title: 'Filtering...',
        text: 'Sedang memfilter user',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        console.log('Filter applied:', role);
    });
}

function exportRoles() {
    Swal.fire({
        title: 'Export Role Data',
        html: `
            <div class="mb-3">
                <label class="form-label">Format Export</label>
                <select class="form-select" id="export-format">
                    <option value="excel">Excel</option>
                    <option value="csv">CSV</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Include Data</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-user-info" checked>
                    <label class="form-check-label" for="include-user-info">
                        User Information
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-role-history" checked>
                    <label class="form-check-label" for="include-role-history">
                        Role Change History
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-access-info" checked>
                    <label class="form-check-label" for="include-access-info">
                        Access Information
                    </label>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const format = document.getElementById('export-format').value;
            const includeUserInfo = document.getElementById('include-user-info').checked;
            const includeRoleHistory = document.getElementById('include-role-history').checked;
            const includeAccessInfo = document.getElementById('include-access-info').checked;
            
            return { format, includeUserInfo, includeRoleHistory, includeAccessInfo };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format, includeUserInfo, includeRoleHistory, includeAccessInfo } = result.value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor data role ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Data role berhasil diekspor ke format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}

function refreshData() {
    Swal.fire({
        title: 'Refreshing...',
        text: 'Memperbarui data hak akses',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        location.reload();
    });
}

// Helper functions
function getRoleBadgeColor(role) {
    const colors = {
        'superadmin': 'dark',
        'admin': 'danger',
        'manager': 'warning',
        'customer': 'info'
    };
    return colors[role] || 'secondary';
}

function getRoleAccess(role) {
    const access = {
        'superadmin': 'Semua akses sistem + Hak Akses Management + Web Setting',
        'admin': 'CRUD Users/Lapangan + Semua Booking + Keuangan',
        'manager': 'Konfirmasi Booking + Lihat Keuangan + Activity Log',
        'customer': 'Booking & Riwayat + Upload Pembayaran'
    };
    return access[role] || 'Limited access';
}

// Event listener
document.getElementById('filter-role').addEventListener('change', applyFilter);

// Auto-refresh every 2 minutes
setInterval(refreshData, 120000);
</script>
@endsection
