@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-users me-2"></i>Kelola User</h5>
                <div>
                    <a href="{{ route('admin.user.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Tambah User
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="filter-role">
                            <option value="">Semua Role</option>
                            <option value="customer" {{ $role === 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="manager" {{ $role === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="superadmin" {{ $role === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filter-status">
                            <option value="">Semua Status</option>
                            <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $status === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-user" placeholder="Cari user..." value="{{ $search ?? '' }}">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" onclick="applyFilter()">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <button class="btn btn-secondary" onclick="resetFilter()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>

                <!-- User Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-users me-2"></i>Total User</h5>
                                <h3 id="total-users">{{ $totalUsers }}</h3>
                                <small>Semua user terdaftar</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-user me-2"></i>Customer</h5>
                                <h3 id="customer-count">{{ $customerCount }}</h3>
                                <small>Pengguna biasa</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-user-tie me-2"></i>Staff</h5>
                                <h3 id="staff-count">{{ $staffCount }}</h3>
                                <small>Manager & Admin</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-user-check me-2"></i>Aktif</h5>
                                <h3 id="active-count">{{ $activeCount }}</h3>
                                <small>User aktif</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                </th>
                                <th>Avatar</th>
                                <th>Informasi User</th>
                                <th>Kontak</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="user-tbody">
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                                    </td>
                                    <td>
                                        @if($user->profile_photo)
                                            <img src="{{ asset('storage/' . $user->profile_photo) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $user->name }}">
                                        @else
                                            <div class="avatar bg-{{ $user->role === 'customer' ? 'primary' : ($user->role === 'manager' ? 'success' : ($user->role === 'admin' ? 'danger' : 'dark')) }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $user->name }}</strong><br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($user->phone)
                                                <i class="fas fa-phone me-1"></i>{{ $user->phone }}<br>
                                            @endif
                                            @if($user->address)
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ substr($user->address, 0, 20) }}{{ strlen($user->address) > 20 ? '...' : '' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'customer' ? 'info' : ($user->role === 'manager' ? 'warning' : ($user->role === 'admin' ? 'danger' : 'dark')) }}">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">{{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $user->created_at->format('Y-m-d') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="showDetail({{ $user->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->is_active)
                                                <a href="{{ route('admin.user.toggle-status', $user->id) }}" class="btn btn-secondary" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan user ini?')">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('admin.user.toggle-status', $user->id) }}" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin mengaktifkan user ini?')">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            @endif
                                            <button class="btn btn-danger" onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Bulk Actions -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <button class="btn btn-sm btn-danger" onclick="bulkDelete()" id="bulk-delete" style="display: none;">
                            <i class="fas fa-trash me-2"></i>Hapus Terpilih
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="bulkActivate()" id="bulk-activate" style="display: none;">
                            <i class="fas fa-check me-2"></i>Aktifkan Terpilih
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="bulkDeactivate()" id="bulk-deactivate" style="display: none;">
                            <i class="fas fa-ban me-2"></i>Nonaktifkan Terpilih
                        </button>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            @if($users->hasPages())
                                {{ $users->links() }}
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detail-content">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="edit_user_id" name="user_id">
                    
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role</label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="customer">Customer</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">No. HP</label>
                        <input type="tel" class="form-control" id="edit_phone" name="phone">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_is_active" class="form-label">Status</label>
                        <select class="form-select" id="edit_is_active" name="is_active">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Sample user data
const userData = [
    {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        role: 'customer',
        phone: '08123456789',
        address: 'Jakarta, Indonesia',
        is_active: true,
        created_at: '2024-01-10'
    },
    {
        id: 2,
        name: 'Jane Smith',
        email: 'jane@example.com',
        role: 'manager',
        phone: '08123456788',
        address: 'Bandung, Indonesia',
        is_active: true,
        created_at: '2024-01-08'
    },
    {
        id: 3,
        name: 'Admin User',
        email: 'admin@manfutsal.com',
        role: 'admin',
        phone: '08123456787',
        address: 'Jakarta, Indonesia',
        is_active: true,
        created_at: '2024-01-01'
    }
];

function showDetail(id) {
    const user = userData.find(u => u.id === id);
    if (!user) return;
    
    const content = `
        <div class="text-center mb-3">
            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 32px;">
                ${user.name.split(' ').map(n => n[0]).join('')}
            </div>
            <h5 class="mt-3">${user.name}</h5>
            <span class="badge bg-${getRoleBadgeColor(user.role)}">${user.role.toUpperCase()}</span>
        </div>
        
        <table class="table table-sm">
            <tr><td>Email</td><td>${user.email}</td></tr>
            <tr><td>No. HP</td><td>${user.phone || '-'}</td></tr>
            <tr><td>Alamat</td><td>${user.address || '-'}</td></tr>
            <tr><td>Status</td><td>${user.is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>'}</td></tr>
            <tr><td>Terdaftar</td><td>${user.created_at}</td></tr>
        </table>
    `;
    
    document.getElementById('detail-content').innerHTML = content;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}

function editUser(id) {
    const user = userData.find(u => u.id === id);
    if (!user) return;
    
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_phone').value = user.phone || '';
    document.getElementById('edit_address').value = user.address || '';
    document.getElementById('edit_is_active').value = user.is_active ? '1' : '0';
    document.getElementById('edit_password').value = '';
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function saveUser() {
    const form = document.getElementById('editForm');
    const formData = new FormData(form);
    
    if (!formData.get('name') || !formData.get('email')) {
        Swal.fire('Error', 'Nama dan email harus diisi', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Saving...',
        text: 'Sedang menyimpan data user',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'Data user berhasil disimpan', 'success').then(() => {
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            location.reload();
        });
    }, 1500);
}

function deleteUser(id) {
    const user = userData.find(u => u.id === id);
    if (!user) return;
    
    Swal.fire({
        title: 'Hapus User?',
        text: `Apakah Anda yakin ingin menghapus user "${user.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processDelete(id);
        }
    });
}

function processDelete(id) {
    Swal.fire({
        title: 'Deleting...',
        text: 'Sedang menghapus user',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'User berhasil dihapus', 'success').then(() => {
            location.reload();
        });
    }, 1500);
}

function toggleStatus(id) {
    const user = userData.find(u => u.id === id);
    if (!user) return;
    
    const newStatus = !user.is_active;
    const statusText = newStatus ? 'mengaktifkan' : 'menonaktifkan';
    
    Swal.fire({
        title: `${statusText.charAt(0).toUpperCase() + statusText.slice(1)} User?`,
        text: `Apakah Anda yakin ingin ${statusText} user "${user.name}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processToggleStatus(id, newStatus);
        }
    });
}

function processToggleStatus(id, status) {
    Swal.fire({
        title: 'Updating...',
        text: 'Sedang mengubah status user',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'Status user berhasil diubah', 'success').then(() => {
            location.reload();
        });
    }, 1500);
}

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const bulkActions = ['bulk-delete', 'bulk-activate', 'bulk-deactivate'];
    
    bulkActions.forEach(actionId => {
        const element = document.getElementById(actionId);
        element.style.display = checkedBoxes.length > 0 ? 'inline-block' : 'none';
    });
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count === 0) return;
    
    Swal.fire({
        title: 'Hapus User Terpilih?',
        text: `Apakah Anda yakin ingin menghapus ${count} user yang dipilih?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processBulkAction('delete');
        }
    });
}

function bulkActivate() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count === 0) return;
    
    Swal.fire({
        title: 'Aktifkan User Terpilih?',
        text: `Apakah Anda yakin ingin mengaktifkan ${count} user yang dipilih?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Aktifkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processBulkAction('activate');
        }
    });
}

function bulkDeactivate() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count === 0) return;
    
    Swal.fire({
        title: 'Nonaktifkan User Terpilih?',
        text: `Apakah Anda yakin ingin menonaktifkan ${count} user yang dipilih?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Nonaktifkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processBulkAction('deactivate');
        }
    });
}

function processBulkAction(action) {
    const actionText = {
        'delete': 'menghapus',
        'activate': 'mengaktifkan',
        'deactivate': 'menonaktifkan'
    };
    
    Swal.fire({
        title: 'Processing...',
        text: `Sedang ${actionText[action]} user terpilih`,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', `User berhasil ${actionText[action]}`, 'success').then(() => {
            location.reload();
        });
    }, 1500);
}

function applyFilter() {
    const role = document.getElementById('filter-role').value;
    const status = document.getElementById('filter-status').value;
    const search = document.getElementById('search-user').value;
    
    console.log('Filter applied:', { role, status, search });
    
    Swal.fire({
        title: 'Filtering...',
        text: 'Sedang menerapkan filter',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        updateUserStats();
    });
}

function resetFilter() {
    document.getElementById('filter-role').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('search-user').value = '';
    
    location.reload();
}

function updateUserStats() {
    // Simulate updating stats based on filter
    document.getElementById('total-users').textContent = '42';
    document.getElementById('customer-count').textContent = '31';
    document.getElementById('staff-count').textContent = '9';
    document.getElementById('active-count').textContent = '39';
}

function getRoleBadgeColor(role) {
    const colors = {
        'customer': 'info',
        'manager': 'warning',
        'admin': 'danger',
        'superadmin': 'dark'
    };
    return colors[role] || 'secondary';
}

// Event listeners
document.querySelectorAll('.user-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

document.getElementById('filter-role').addEventListener('change', applyFilter);
document.getElementById('filter-status').addEventListener('change', applyFilter);
document.getElementById('search-user').addEventListener('input', function() {
    if (this.value.length >= 3 || this.value.length === 0) {
        applyFilter();
    }
});
</script>
@endsection
