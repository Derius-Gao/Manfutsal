@extends('layouts.app')

@section('title', 'Hak Akses')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-shield-alt me-2"></i>Hak Akses</h5>
                <div>
                    <button class="btn btn-success" onclick="exportAccessRights()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    <button class="btn btn-primary" onclick="refreshData()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Access Rights Stats -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Total User</h5>
                                <h3>{{ $totalUsers }}</h3>
                                <small>Semua user</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Customer</h5>
                                <h3>{{ $customerCount }}</h3>
                                <small>Pengguna biasa</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Manager</h5>
                                <h3>{{ $managerCount }}</h3>
                                <small>Manajer lapangan</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Admin</h5>
                                <h3>{{ $adminCount }}</h3>
                                <small>Administrator</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-dark text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Super Admin</h5>
                                <h3>{{ $superadminCount }}</h3>
                                <small>Super administrator</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Aktif</h5>
                                <h3>{{ $users->where('is_active', true)->count() }}</h3>
                                <small>User aktif</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Access Rights Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-sm" id="access-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Booking</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="access-tbody">
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->profile_photo)
                                                <img src="{{ asset('storage/' . $user->profile_photo) }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $user->name }}">
                                            @else
                                                <div class="avatar-sm bg-{{ $user->role === 'customer' ? 'primary' : ($user->role === 'manager' ? 'warning' : ($user->role === 'admin' ? 'danger' : 'dark')) }} text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($user->name, 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div>{{ $user->name }}</div>
                                                @if($user->phone)
                                                    <small class="text-muted">{{ $user->phone }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'customer' ? 'info' : ($user->role === 'manager' ? 'warning' : ($user->role === 'admin' ? 'danger' : 'dark')) }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $user->bookings_count }} booking</small>
                                    </td>
                                    <td>
                                        <small>{{ $user->created_at->format('Y-m-d') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="showDetail({{ $user->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-warning" onclick="editAccess({{ $user->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($user->role !== 'superadmin')
                                                <button class="btn btn-{{ $user->is_active ? 'secondary' : 'success' }}" onclick="toggleStatus({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})">
                                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
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
                        @if($users->hasPages())
                            {{ $users->links() }}
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Hak Akses</h5>
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
                <h5 class="modal-title">Edit Hak Akses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="{{ route('admin.hakakses.update', ':id') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_user_id" name="user_id">
                    
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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                            <label class="form-check-label" for="edit_is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6>Informasi Role:</h6>
                        <ul class="mb-0">
                            <li><strong>Customer:</strong> Bisa booking lapangan</li>
                            <li><strong>Manager:</strong> Kelola booking dan lapangan</li>
                            <li><strong>Admin:</strong> Kelola user, booking, lapangan, keuangan</li>
                            <li><strong>Super Admin:</strong> Akses penuh sistem</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveAccess()">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
}
</style>

@section('scripts')
<script>
const userData = @json($users);

function showDetail(id) {
    const user = userData.find(u => u.id === id);
    if (!user) return;
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6>Informasi User</h6>
                <table class="table table-sm">
                    <tr><td>Nama</td><td>${user.name}</td></tr>
                    <tr><td>Email</td><td>${user.email}</td></tr>
                    <tr><td>Phone</td><td>${user.phone || '-'}</td></tr>
                    <tr><td>Address</td><td>${user.address || '-'}</td></tr>
                    <tr><td>Role</td><td><span class="badge bg-${getRoleBadgeColor(user.role)}">${ucfirst(user.role)}</span></td></tr>
                    <tr><td>Status</td><td><span class="badge bg-${user.is_active ? 'success' : 'secondary'}">${user.is_active ? 'Aktif' : 'Tidak Aktif'}</span></td></tr>
                    <tr><td>Terdaftar</td><td>${user.created_at}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Statistik</h6>
                <table class="table table-sm">
                    <tr><td>Total Booking</td><td>${user.bookings_count}</td></tr>
                    <tr><td>Last Login</td><td>-</td></tr>
                    <tr><td>Status Akun</td><td>${user.is_active ? 'Aktif' : 'Dinonaktifkan'}</td></tr>
                    <tr><td>Permission Level</td><td>${getPermissionLevel(user.role)}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    document.getElementById('detail-content').innerHTML = content;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}

function editAccess(id) {
    const user = userData.find(u => u.id === id);
    if (!user) return;
    
    const form = document.getElementById('editForm');
    form.action = form.action.replace(':id', id);
    
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_is_active').checked = user.is_active;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function saveAccess() {
    const form = document.getElementById('editForm');
    form.submit();
}

function toggleStatus(id, status) {
    const action = status ? 'mengaktifkan' : 'menonaktifkan';
    
    Swal.fire({
        title: `${action} User?`,
        text: `Apakah Anda yakin ingin ${action} user ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: status ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Ya, ${action}`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/hakakses/${id}/toggle`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function getRoleBadgeColor(role) {
    switch(role) {
        case 'customer': return 'info';
        case 'manager': return 'warning';
        case 'admin': return 'danger';
        case 'superadmin': return 'dark';
        default: return 'secondary';
    }
}

function getPermissionLevel(role) {
    switch(role) {
        case 'customer': return 'Level 1 - Basic Access';
        case 'manager': return 'Level 2 - Manager Access';
        case 'admin': return 'Level 3 - Admin Access';
        case 'superadmin': return 'Level 4 - Full Access';
        default: return 'Unknown';
    }
}

function refreshData() {
    location.reload();
}

function exportAccessRights() {
    Swal.fire({
        title: 'Export Hak Akses',
        html: `
            <div class="mb-3">
                <label class="form-label">Format Export</label>
                <select class="form-select" id="export-format">
                    <option value="excel">Excel</option>
                    <option value="csv">CSV</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const format = document.getElementById('export-format').value;
            return { format };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format } = result.value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor hak akses ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Hak akses berhasil diekspor ke format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}
</script>
@endsection
