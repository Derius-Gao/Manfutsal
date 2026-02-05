@extends('layouts.app')

@section('title', 'Hak Akses')

@section('content')
<style>
    /* Fix layout untuk tidak tertimpa sidebar */
    .permissions-container {
        width: calc(100% - 200px); /* Subtract sidebar width */
        max-width: 100%;
        margin-left: 200px; /* Match your sidebar width */
        padding: 20px;
    }
    
    .permissions-table-wrapper {
        overflow-x: auto;
        width: 100%;
        margin-top: 20px;
        -webkit-overflow-scrolling: touch;
    }
    
    .permissions-table {
        width: 100%;
        min-width: 1000px;
        margin-bottom: 0;
    }
    
    .permissions-table th,
    .permissions-table td {
        padding: 12px 8px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }
    
    .permissions-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
        white-space: nowrap;
    }
    
    .permissions-table td {
        white-space: nowrap;
    }
    
    .permission-checkbox {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }
    
    .card-body {
        padding: 1.5rem;
        overflow: visible;
    }
    
    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .permissions-table {
            min-width: 1200px;
        }
    }
    
    /* For tablets and smaller - collapse sidebar */
    @media (max-width: 768px) {
        .permissions-container {
            width: 100%;
            margin-left: 0;
            padding: 15px;
        }
    }
    
    /* If sidebar is collapsible, adjust when collapsed */
    .sidebar-collapsed .permissions-container {
        width: calc(100% - 60px);
        margin-left: 60px;
    }
</style>

<div class="permissions-container">
    <div class="row">
        <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-shield-alt me-2"></i>Manajemen Hak Akses</h5>
                <div>
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

                <!-- User Role Management dengan Checkbox -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6>Manajemen Hak Akses User</h6>
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
                        <div class="table-responsive permissions-table-wrapper">
                            <table class="table table-striped table-bordered permissions-table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role Saat Ini</th>
                                        <th>Dashboard</th>
                                        <th>Users</th>
                                        <th>Lapangans</th>
                                        <th>Bookings</th>
                                        <th>Keuangan</th>
                                        <th>Activities</th>
                                        <th>Settings</th>
                                        <th>Access</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="user-role-tbody">
                                    @php
                                        $availablePages = [
                                            'dashboard' => 'Dashboard',
                                            'users' => 'Users', 
                                            'lapangans' => 'Lapangans',
                                            'bookings' => 'Bookings',
                                            'keuangan' => 'Keuangan',
                                            'activities' => 'Activities',
                                            'settings' => 'Settings',
                                            'access' => 'Access'
                                        ];
                                    @endphp
                                    
                                    @foreach($users as $user)
                                        @php
                                            $permissions = isset($userPermissions[$user->id]) ? $userPermissions[$user->id] : [];
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar bg-{{ $user->role === 'superadmin' ? 'dark' : ($user->role === 'admin' ? 'danger' : ($user->role === 'manager' ? 'warning' : 'info')) }} text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                        {{ implode('', array_map(function($n) { return strtoupper($n[0]); }, explode(' ', $user->name))) }}
                                                    </div>
                                                    <div>
                                                        <div>{{ $user->name }}</div>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $user->role === 'superadmin' ? 'dark' : ($user->role === 'admin' ? 'danger' : ($user->role === 'manager' ? 'warning' : 'info')) }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            
                                            {{-- Checkbox permissions --}}
                                            @foreach($availablePages as $pageKey => $pageTitle)
                                                <td class="text-center">
                                                    @if($user->role === 'superadmin')
                                                        <i class="fas fa-check-circle text-success" title="Auto access (SuperAdmin) - Selalu memiliki akses"></i>
                                                    @else
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input permission-checkbox" 
                                                                   type="checkbox" 
                                                                   data-user-id="{{ $user->id }}"
                                                                   data-page="{{ $pageKey }}"
                                                                   id="permission-{{ $user->id }}-{{ $pageKey }}"
                                                                   {{ isset($permissions[$pageKey]) && $permissions[$pageKey] ? 'checked' : '' }}
                                                                   onchange="updatePermission({{ $user->id }}, '{{ $pageKey }}', this.checked)">
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                            
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-info" onclick="showUserDetail({{ $user->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($user->role !== 'superadmin')
                                                        <button class="btn btn-success" onclick="saveUserPermissions({{ $user->id }})">
                                                            <i class="fas fa-save"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
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
// Store permission changes
let permissionChanges = {};

// Update permission when checkbox changes
function updatePermission(userId, pageKey, isChecked) {
    if (!permissionChanges[userId]) {
        permissionChanges[userId] = {};
    }
    permissionChanges[userId][pageKey] = isChecked;
    
    // Show save button for this user
    const saveBtn = document.querySelector(`button[onclick="saveUserPermissions(${userId})"]`);
    if (saveBtn) {
        saveBtn.classList.remove('btn-success');
        saveBtn.classList.add('btn-warning');
        saveBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
    }
}

// Save permissions for specific user
function saveUserPermissions(userId) {
    // Get all current checkbox states for this user, not just changes
    const allPermissions = {};
    const availablePages = ['dashboard', 'users', 'lapangans', 'bookings', 'keuangan', 'activities', 'settings', 'access'];
    
    availablePages.forEach(pageKey => {
        const checkbox = document.getElementById(`permission-${userId}-${pageKey}`);
        if (checkbox) {
            allPermissions[pageKey] = checkbox.checked;
        }
    });

    // If no permissions found, use changes
    if (Object.keys(allPermissions).length === 0 && permissionChanges[userId]) {
        allPermissions = permissionChanges[userId];
    }

    if (Object.keys(allPermissions).length === 0) {
        Swal.fire('Info', 'Tidak ada perubahan hak akses', 'info');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menyimpan perubahan hak akses?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang menyimpan perubahan hak akses',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('permissions', JSON.stringify(allPermissions));

            fetch('/access/permissions/update', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        try {
                            const data = JSON.parse(text);
                            throw new Error(data.message || data.error || 'Gagal menyimpan');
                        } catch (e) {
                            throw new Error(text || 'Gagal menyimpan perubahan');
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', 'Hak akses berhasil diperbarui', 'success');
                    
                    // Reset save button
                    const saveBtn = document.querySelector(`button[onclick="saveUserPermissions(${userId})"]`);
                    if (saveBtn) {
                        saveBtn.classList.remove('btn-warning');
                        saveBtn.classList.add('btn-success');
                        saveBtn.innerHTML = '<i class="fas fa-save"></i>';
                    }
                    
                    // Clear changes for this user
                    delete permissionChanges[userId];
                    
                    // Reload page to show updated permissions
                    setTimeout(() => location.reload(), 1000);
                } else {
                    Swal.fire('Error', data.message || 'Gagal menyimpan perubahan', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving permissions:', error);
                Swal.fire('Error', error.message || 'Terjadi kesalahan saat menyimpan', 'error');
            });
        }
    });
}

// Quick select all permissions for a user
function selectAllPermissions(userId) {
    document.querySelectorAll(`input[data-user-id="${userId}"]`).forEach(checkbox => {
        checkbox.checked = true;
        updatePermission(userId, checkbox.dataset.page, true);
    });
}

// Quick deselect all permissions for a user
function deselectAllPermissions(userId) {
    document.querySelectorAll(`input[data-user-id="${userId}"]`).forEach(checkbox => {
        checkbox.checked = false;
        updatePermission(userId, checkbox.dataset.page, false);
    });
}

// Set basic permissions (dashboard + bookings)
function setBasicPermissions(userId) {
    document.querySelectorAll(`input[data-user-id="${userId}"]`).forEach(checkbox => {
        const isBasic = ['dashboard', 'bookings'].includes(checkbox.dataset.page);
        checkbox.checked = isBasic;
        updatePermission(userId, checkbox.dataset.page, isBasic);
    });
}

// Set manager permissions
function setManagerPermissions(userId) {
    document.querySelectorAll(`input[data-user-id="${userId}"]`).forEach(checkbox => {
        const isManagerAccess = ['dashboard', 'bookings', 'keuangan', 'activities'].includes(checkbox.dataset.page);
        checkbox.checked = isManagerAccess;
        updatePermission(userId, checkbox.dataset.page, isManagerAccess);
    });
}

// Show user details (keep existing function)
function showUserDetail(id) {
    // Find user data from current page
    const users = @json($users);
    const user = users.find(u => u.id === id);
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
            <tr><td>Role</td><td><span class="badge bg-${getRoleBadgeColor(user.role)}">${user.role.toUpperCase()}</span></td></tr>
            <tr><td>Akses</td><td>${getRoleAccess(user.role)}</td></tr>
        </table>
    `;
    
    document.getElementById('user-detail-content').innerHTML = content;
    new bootstrap.Modal(document.getElementById('userDetailModal')).show();
}

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
    const accesses = {
        'superadmin': 'Full access to all features',
        'admin': 'Access to user management, lapangans, bookings, keuangan',
        'manager': 'Access to bookings, keuangan, activities',
        'customer': 'Access to bookings and personal data'
    };
    return accesses[role] || 'Limited access';
}

function applyFilter() {
    const role = document.getElementById('filter-role').value;
    console.log('Filtering by role:', role);
}


function refreshData() {
    location.reload();
}

// Warn user if there are unsaved changes
window.addEventListener('beforeunload', function(e) {
    if (Object.keys(permissionChanges).length > 0) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endsection