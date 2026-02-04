@extends('layouts.app')

@section('title', 'Hak Akses')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-shield-alt me-2"></i>Manajemen Hak Akses User</h5>
                <div>
                    <button class="btn btn-success" onclick="exportPermissions()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    <button class="btn btn-primary" onclick="refreshData()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Info Section -->
                <div class="alert alert-info mb-4">
                    <h6><i class="fas fa-info-circle me-2"></i>Informasi Hak Akses</h6>
                    <p class="mb-2">Sistem hak akses berbasis checkbox per halaman untuk setiap user:</p>
                    <ul class="mb-0">
                        <li><strong>SuperAdmin</strong> - Otomatis memiliki akses ke semua halaman</li>
                        <li><strong>User Lainnya</strong> - Hak akses diatur melalui checkbox per halaman</li>
                        <li>Centang checkbox untuk memberikan akses, hapus centang untuk mencabut akses</li>
                    </ul>
                </div>

                <!-- User Selection -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="user-select" class="form-label">Pilih User</label>
                        <select class="form-select" id="user-select" onchange="loadUserPermissions()">
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $user->role === 'superadmin' ? 'disabled' : '' }}>
                                    {{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Permissions Table -->
                <div id="permissions-container" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 id="selected-user-name">Hak Akses User</h6>
                            <button class="btn btn-warning btn-sm" onclick="savePermissions()">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                        <div class="card-body">
                            <form id="permissions-form">
                                @csrf
                                <input type="hidden" id="user-id" name="user_id">
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="50%">Halaman</th>
                                                <th width="20%" class="text-center">Akses</th>
                                                <th width="30%">Deskripsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($availablePages as $pageKey => $pageTitle)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $pageTitle }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $pageKey }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-checkbox" 
                                                                   type="checkbox" 
                                                                   name="permissions[{{ $pageKey }}]" 
                                                                   value="1"
                                                                   id="permission-{{ $pageKey }}"
                                                                   onchange="markAsChanged()">
                                                            <label class="form-check-label" for="permission-{{ $pageKey }}">
                                                                <span class="badge bg-success">Boleh Akses</span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            @switch($pageKey)
                                                                @case('dashboard')
                                                                    Halaman utama user dengan statistik dan quick actions
                                                                    @break
                                                                @case('users')
                                                                    Manajemen data user (CRUD, update role, status)
                                                                    @break
                                                                @case('lapangans')
                                                                    Manajemen data lapangan futsal (CRUD, status)
                                                                    @break
                                                                @case('bookings')
                                                                    Manajemen pemesanan lapangan (konfirmasi, reject, complete)
                                                                    @break
                                                                @case('keuangan')
                                                                    Laporan keuangan dan transaksi pembayaran
                                                                    @break
                                                                @case('activities')
                                                                    Log activity dan riwayat aktivitas sistem
                                                                    @break
                                                                @case('settings')
                                                                    Pengaturan sistem dan konfigurasi aplikasi
                                                                    @break
                                                                @case('access')
                                                                    Manajemen hak akses user (hanya superadmin)
                                                                    @break
                                                                @default
                                                                    Akses ke halaman {{ $pageKey }}
                                                            @endswitch
                                                        </small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Perubahan hak akses akan langsung berlaku setelah disimpan. User akan kehilangan akses ke halaman yang tidak dicentang.
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6>Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <button class="btn btn-success w-100" onclick="selectAllPermissions()">
                                            <i class="fas fa-check-double me-2"></i>Select All
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-danger w-100" onclick="deselectAllPermissions()">
                                            <i class="fas fa-times me-2"></i>Deselect All
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-info w-100" onclick="setBasicPermissions()">
                                            <i class="fas fa-user me-2"></i>Basic Access
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-warning w-100" onclick="setManagerPermissions()">
                                            <i class="fas fa-user-tie me-2"></i>Manager Access
                                        </button>
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

@section('scripts')
<script>
let hasChanges = false;
let currentPermissions = {};

function loadUserPermissions() {
    const userId = document.getElementById('user-select').value;
    
    if (!userId) {
        document.getElementById('permissions-container').style.display = 'none';
        return;
    }

    // Show loading
    Swal.fire({
        title: 'Loading...',
        text: 'Memuat data hak akses user',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Get user info
    const userSelect = document.getElementById('user-select');
    const selectedOption = userSelect.options[userSelect.selectedIndex];
    const userName = selectedOption.text.split(' (')[0];
    
    document.getElementById('selected-user-name').textContent = `Hak Akses: ${userName}`;
    document.getElementById('user-id').value = userId;

    // Load permissions via AJAX
    fetch(`/access/permissions/${userId}`)
        .then(response => response.json())
        .then(data => {
            currentPermissions = data.permissions;
            
            // Set checkbox states
            Object.keys(currentPermissions).forEach(pageKey => {
                const checkbox = document.getElementById(`permission-${pageKey}`);
                if (checkbox) {
                    checkbox.checked = currentPermissions[pageKey];
                }
            });

            // Show permissions container
            document.getElementById('permissions-container').style.display = 'block';
            hasChanges = false;
            
            Swal.close();
        })
        .catch(error => {
            console.error('Error loading permissions:', error);
            Swal.fire('Error', 'Gagal memuat data hak akses', 'error');
        });
}

function markAsChanged() {
    hasChanges = true;
}

function savePermissions() {
    if (!hasChanges) {
        Swal.fire('Info', 'Tidak ada perubahan yang disimpan', 'info');
        return;
    }

    const form = document.getElementById('permissions-form');
    const formData = new FormData(form);
    
    // Convert checkbox values to boolean
    const permissions = {};
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('permissions[')) {
            const pageKey = key.replace('permissions[', '').replace(']', '');
            permissions[pageKey] = true;
        }
    }

    // Add unchecked permissions as false
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        const pageKey = checkbox.name.replace('permissions[', '').replace(']', '');
        if (!permissions.hasOwnProperty(pageKey)) {
            permissions[pageKey] = false;
        }
    });

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
            const formData = new FormData();
            formData.append('user_id', document.getElementById('user-id').value);
            formData.append('permissions', JSON.stringify(permissions));

            fetch('/access/permissions/update', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', 'Hak akses berhasil diperbarui', 'success');
                    hasChanges = false;
                    loadUserPermissions(); // Reload to show current state
                } else {
                    Swal.fire('Error', data.message || 'Gagal menyimpan perubahan', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving permissions:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat menyimpan', 'error');
            });
        }
    });
}

function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    markAsChanged();
}

function deselectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    markAsChanged();
}

function setBasicPermissions() {
    // Basic permissions for regular users
    const basicPages = ['dashboard', 'bookings'];
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        const pageKey = checkbox.name.replace('permissions[', '').replace(']', '');
        checkbox.checked = basicPages.includes(pageKey);
    });
    markAsChanged();
}

function setManagerPermissions() {
    // Manager permissions
    const managerPages = ['dashboard', 'bookings', 'keuangan', 'activities'];
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        const pageKey = checkbox.name.replace('permissions[', '').replace(']', '');
        checkbox.checked = managerPages.includes(pageKey);
    });
    markAsChanged();
}

function exportPermissions() {
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
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const format = document.getElementById('export-format').value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor data hak akses ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Data hak akses berhasil diekspor`, 'success');
            }, 2000);
        }
    });
}

function refreshData() {
    location.reload();
}

// Warn user if there are unsaved changes
window.addEventListener('beforeunload', function(e) {
    if (hasChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endsection
