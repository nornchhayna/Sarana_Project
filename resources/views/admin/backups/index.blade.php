@extends('layouts.admin')

@section('title', 'System Backups')
@section('page-title', 'Database & Backups')

@section('content')

{{-- ALERT NOTIFICATIONS --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <ul class="mb-0 pl-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- METRIC SUMMARY CARDS --}}
<div class="row">
    {{-- Total Backups --}}
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-archive"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Backups</span>
                <span class="info-box-number text-bold text-lg">{{ count($backups) }}</span>
            </div>
        </div>
    </div>

    {{-- Backup Storage --}}
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-hdd"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Backup Storage Used</span>
                <span class="info-box-number text-bold text-lg">{{ $totalFormatted }}</span>
            </div>
        </div>
    </div>

    {{-- Live Database Size --}}
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-database"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Database Size</span>
                <span class="info-box-number text-bold text-lg">{{ $dbStats['size_formatted'] }} ({{ $dbStats['table_count'] }} tables)</span>
            </div>
        </div>
    </div>

    {{-- Latest Backup --}}
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock text-white"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Latest Backup</span>
                <span class="info-box-number text-bold text-sm">
                    {{ $latestBackup ? $latestBackup['age'] : 'Never' }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ACTION BAR & MODALS --}}
<div class="card card-default shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
        <h3 class="card-title text-bold mb-2 mb-md-0">
            <i class="fas fa-server mr-2 text-primary"></i>
            Backup Management & Actions
        </h3>
        <div class="card-tools d-flex align-items-center flex-wrap gap-2">
            {{-- Primary Create Backup Modal Trigger --}}
            <button type="button" class="btn btn-primary shadow-sm mr-2" id="openCreateBackupBtn" data-toggle="modal" data-target="#createBackupModal">
                <i class="fas fa-plus-circle mr-1"></i> Create Backup
            </button>

            {{-- Instant 1-Click DB Backup Button --}}
            <form action="{{ route('admin.backups.store') }}" method="POST" class="d-inline mr-2" onsubmit="handleQuickSubmit(this);">
                @csrf
                <input type="hidden" name="type" value="database">
                <input type="hidden" name="compress" value="1">
                <button type="submit" class="btn btn-outline-primary shadow-sm" title="Quickly create a database backup now">
                    <i class="fas fa-database mr-1"></i> Quick DB Backup
                </button>
            </form>

            {{-- Upload Backup Modal Trigger --}}
            <button type="button" class="btn btn-outline-secondary shadow-sm" id="openUploadBackupBtn" data-toggle="modal" data-target="#uploadBackupModal">
                <i class="fas fa-upload mr-1"></i> Upload Backup
            </button>
        </div>
    </div>
</div>

{{-- BACKUPS LIST TABLE --}}
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header bg-white">
        <h3 class="card-title text-bold">
            <i class="fas fa-list mr-2"></i>
            Available Backups
        </h3>
        <span class="badge badge-secondary ml-2">{{ count($backups) }} files</span>
    </div>

    <div class="card-body p-0">
        @if (count($backups) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Filename</th>
                            <th>Type</th>
                            <th>File Size</th>
                            <th>Created At</th>
                            <th class="text-right" style="width: 250px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($backups as $index => $b)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($b['type'] === 'database')
                                            <i class="fas fa-database fa-lg text-primary mr-3"></i>
                                        @elseif ($b['type'] === 'files')
                                            <i class="fas fa-file-archive fa-lg text-info mr-3"></i>
                                        @else
                                            <i class="fas fa-box-open fa-lg text-success mr-3"></i>
                                        @endif
                                        <div>
                                            <span class="font-weight-bold text-dark font-monospace">{{ $b['filename'] }}</span>
                                            <small class="d-block text-muted">
                                                Path: <code class="text-muted">storage/app/backups/{{ $b['filename'] }}</code>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($b['type'] === 'database')
                                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-database mr-1"></i> Database</span>
                                    @elseif ($b['type'] === 'files')
                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-images mr-1"></i> Files / Media</span>
                                    @else
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Full (DB + Files)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light border text-dark font-weight-bold px-2 py-1">
                                        {{ $b['size_formatted'] }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $b['created_at_formatted'] }}</div>
                                    <small class="text-muted"><i class="far fa-clock mr-1"></i>{{ $b['age'] }}</small>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm">
                                        {{-- Download Button --}}
                                        <a href="{{ route('admin.backups.download', $b['filename']) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Download Backup">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>

                                        {{-- Restore Button (Only for Database / Full backups) --}}
                                        @if ($b['can_restore'] && ($b['type'] === 'database' || $b['type'] === 'full'))
                                            <button type="button" 
                                                    class="btn btn-outline-warning" 
                                                    title="Restore this backup"
                                                    data-toggle="modal" 
                                                    data-target="#restoreModal_{{ md5($b['filename']) }}">
                                                <i class="fas fa-undo mr-1"></i> Restore
                                            </button>
                                        @endif

                                        {{-- Delete Button --}}
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                title="Delete Backup"
                                                data-toggle="modal" 
                                                data-target="#deleteModal_{{ md5($b['filename']) }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    {{-- RESTORE CONFIRMATION MODAL --}}
                                    @if ($b['can_restore'] && ($b['type'] === 'database' || $b['type'] === 'full'))
                                        <div class="modal fade text-left" id="restoreModal_{{ md5($b['filename']) }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content shadow-lg border-0">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title font-weight-bold">
                                                            <i class="fas fa-exclamation-triangle mr-2"></i> Confirm Database Restore
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('admin.backups.restore', $b['filename']) }}" method="POST" onsubmit="handleRestoreSubmit(this);">
                                                        @csrf
                                                        <div class="modal-body p-4">
                                                            <p class="text-danger font-weight-bold mb-2">
                                                                WARNING: Restoring will overwrite current database records with the contents of this backup file!
                                                            </p>
                                                            <div class="alert alert-light border p-3 mb-3">
                                                                <div><strong>Backup File:</strong> <code>{{ $b['filename'] }}</code></div>
                                                                <div><strong>Size:</strong> {{ $b['size_formatted'] }}</div>
                                                                <div><strong>Date:</strong> {{ $b['created_at_formatted'] }}</div>
                                                                <div><strong>Target Database:</strong> <code>{{ $dbStats['database'] }}</code></div>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mt-3">
                                                                <input type="checkbox" class="custom-control-input" id="confirm_restore_{{ md5($b['filename']) }}" name="confirm_restore" value="1" required>
                                                                <label class="custom-control-label text-dark" for="confirm_restore_{{ md5($b['filename']) }}">
                                                                    I understand that current data will be replaced by this backup.
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-warning font-weight-bold">
                                                                <i class="fas fa-undo mr-1"></i> Yes, Restore Database
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- DELETE CONFIRMATION MODAL --}}
                                    <div class="modal fade text-left" id="deleteModal_{{ md5($b['filename']) }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content shadow-lg border-0">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title font-weight-bold">
                                                        <i class="fas fa-trash-alt mr-2"></i> Delete Backup
                                                    </h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('admin.backups.destroy', $b['filename']) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-body p-4">
                                                        <p class="mb-2">Are you sure you want to permanently delete this backup file?</p>
                                                        <div class="alert alert-light border">
                                                            <strong>{{ $b['filename'] }}</strong> ({{ $b['size_formatted'] }})
                                                        </div>
                                                        <p class="text-muted small mb-0">This file will be permanently removed from disk.</p>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger font-weight-bold">
                                                            <i class="fas fa-trash-alt mr-1"></i> Delete File
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-archive fa-3x text-muted mb-3 d-block"></i>
                <h5 class="text-muted font-weight-bold">No backups found</h5>
                <p class="text-muted mb-3">You have not created any backups yet. Click "Create Backup" above to make your first backup.</p>
                <form action="{{ route('admin.backups.store') }}" method="POST" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="type" value="database">
                    <input type="hidden" name="compress" value="1">
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fas fa-database mr-1"></i> Create First Database Backup
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

{{-- CREATE BACKUP MODAL --}}
<div class="modal fade" id="createBackupModal" tabindex="-1" role="dialog" aria-labelledby="createBackupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="createBackupModalLabel">
                    <i class="fas fa-plus-circle mr-2"></i> Create New Backup
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.backups.store') }}" method="POST" id="createBackupForm" onsubmit="handleBackupCreateSubmit(this);">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted mb-3">Select the type of backup to generate:</p>

                    <div class="list-group mb-3">
                        <label class="list-group-item list-group-item-action d-flex align-items-start p-3" style="cursor: pointer;">
                            <input type="radio" name="type" value="database" checked class="mt-1 mr-3" onchange="toggleCompress(true)">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold text-primary">
                                        <i class="fas fa-database mr-1"></i> Database Only (.sql.gz)
                                    </h6>
                                    <span class="badge badge-success">Recommended</span>
                                </div>
                                <small class="text-muted d-block">
                                    Backs up all database tables, users, orders, products, and configuration. Very fast and lightweight (~{{ $dbStats['size_formatted'] }}).
                                </small>
                            </div>
                        </label>

                        <label class="list-group-item list-group-item-action d-flex align-items-start p-3" style="cursor: pointer;">
                            <input type="radio" name="type" value="full" class="mt-1 mr-3" onchange="toggleCompress(false)">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 font-weight-bold text-success">
                                    <i class="fas fa-box-open mr-1"></i> Full System Backup (.zip)
                                </h6>
                                <small class="text-muted d-block">
                                    Complete backup containing both the database SQL dump AND all uploaded images and assets.
                                </small>
                            </div>
                        </label>

                        <label class="list-group-item list-group-item-action d-flex align-items-start p-3" style="cursor: pointer;">
                            <input type="radio" name="type" value="files" class="mt-1 mr-3" onchange="toggleCompress(false)">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 font-weight-bold text-info">
                                    <i class="fas fa-images mr-1"></i> Media & Uploads Only (.zip)
                                </h6>
                                <small class="text-muted d-block">
                                    Archives product photos, categories, avatars, and attachments without the database.
                                </small>
                            </div>
                        </label>
                    </div>

                    <div class="custom-control custom-checkbox mt-2" id="compressOptionWrapper">
                        <input type="checkbox" class="custom-control-input" id="compress" name="compress" value="1" checked>
                        <label class="custom-control-label text-muted" for="compress">
                            Enable Gzip compression (creates <code>.sql.gz</code>, saves ~80% disk space)
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="btnStartBackup">
                        <span class="spinner-border spinner-border-sm d-none mr-1" id="backupSpinner" role="status" aria-hidden="true"></span>
                        <i class="fas fa-play mr-1" id="backupPlayIcon"></i>
                        Start Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- UPLOAD BACKUP MODAL --}}
<div class="modal fade" id="uploadBackupModal" tabindex="-1" role="dialog" aria-labelledby="uploadBackupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title font-weight-bold" id="uploadBackupModalLabel">
                    <i class="fas fa-upload mr-2"></i> Upload Backup File
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.backups.upload') }}" method="POST" enctype="multipart/form-data" onsubmit="handleUploadSubmit(this);">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted mb-3">
                        Upload an existing <code>.sql</code>, <code>.sql.gz</code>, or <code>.zip</code> backup file into the system:
                    </p>
                    <div class="form-group">
                        <label for="backup_file" class="font-weight-bold">Select File</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="backup_file" name="backup_file" accept=".sql,.gz,.zip" required onchange="updateFileName(this)">
                            <label class="custom-file-label" for="backup_file" id="backup_file_label">Choose backup file...</label>
                        </div>
                        <small class="form-text text-muted">Maximum allowed file size: 512 MB.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="btnUploadSubmit">
                        <span class="spinner-border spinner-border-sm d-none mr-1" id="uploadSpinner" role="status" aria-hidden="true"></span>
                        <i class="fas fa-cloud-upload-alt mr-1" id="uploadIcon"></i>
                        Upload File
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SYSTEM INFO & BEST PRACTICES CARD --}}
<div class="card card-default shadow-sm mt-4">
    <div class="card-header bg-light">
        <h3 class="card-title text-muted text-bold">
            <i class="fas fa-info-circle mr-2 text-info"></i>
            Database & Storage Details
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="font-weight-bold"><i class="fas fa-database text-primary mr-2"></i> Database Configuration</h6>
                <ul class="list-unstyled text-muted pl-4">
                    <li><strong>Database Name:</strong> <code>{{ $dbStats['database'] }}</code></li>
                    <li><strong>Server Engine:</strong> {{ $dbStats['version'] }}</li>
                    <li><strong>Total Tables:</strong> {{ $dbStats['table_count'] }} tables</li>
                    <li><strong>Storage Location:</strong> <code>storage/app/backups/</code></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="font-weight-bold"><i class="fas fa-shield-alt text-success mr-2"></i> Best Practices</h6>
                <ul class="list-unstyled text-muted pl-4">
                    <li><i class="fas fa-check text-success mr-1"></i> Periodically download backup files to an external hard drive or secure cloud storage.</li>
                    <li><i class="fas fa-check text-success mr-1"></i> Always generate a backup before performing major system migrations or updating settings.</li>
                    <li><i class="fas fa-check text-success mr-1"></i> Database dumps are safe to run during live store operations.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle Gzip option visibility based on backup type
function toggleCompress(show) {
    var el = document.getElementById('compressOptionWrapper');
    if (el) {
        el.style.display = show ? 'block' : 'none';
    }
}

// Update file input label on selection
function updateFileName(input) {
    var fileName = input.files[0] ? input.files[0].name : 'Choose backup file...';
    document.getElementById('backup_file_label').textContent = fileName;
}

// Loading state on Create Backup submission
function handleBackupCreateSubmit(form) {
    var btn = document.getElementById('btnStartBackup');
    var spinner = document.getElementById('backupSpinner');
    var icon = document.getElementById('backupPlayIcon');
    if (btn) btn.disabled = true;
    if (spinner) spinner.classList.remove('d-none');
    if (icon) icon.classList.add('d-none');
}

// Loading state on Quick DB Backup
function handleQuickSubmit(form) {
    var btn = form.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> Creating...';
    }
}

// Loading state on Upload
function handleUploadSubmit(form) {
    var btn = document.getElementById('btnUploadSubmit');
    var spinner = document.getElementById('uploadSpinner');
    var icon = document.getElementById('uploadIcon');
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Uploading...';
    }
    if (spinner) spinner.classList.remove('d-none');
    if (icon) icon.classList.add('d-none');
}

// Loading state on Restore
function handleRestoreSubmit(form) {
    var btn = form.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> Restoring database...';
    }
}

// Universal Vanilla JS modal opener fallback (guarantees buttons work regardless of jQuery / Bootstrap JS issues)
document.addEventListener('DOMContentLoaded', function () {
    function openModalById(modalId) {
        var modal = document.querySelector(modalId);
        if (!modal) return;

        // Try jQuery / Bootstrap 4 first
        if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
            window.jQuery(modal).modal('show');
            return;
        }

       
        modal.classList.add('show');
        modal.style.display = 'block';
        modal.setAttribute('aria-modal', 'true');
        modal.removeAttribute('aria-hidden');

        // Add backdrop if not present
        var backdrop = document.querySelector('.modal-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }
        document.body.classList.add('modal-open');

        // Bind close buttons
        var closeButtons = modal.querySelectorAll('[data-dismiss="modal"], .close');
        closeButtons.forEach(function (btn) {
            btn.onclick = function () {
                modal.classList.remove('show');
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
                modal.removeAttribute('aria-modal');
                var bd = document.querySelector('.modal-backdrop');
                if (bd) bd.remove();
                document.body.classList.remove('modal-open');
            };
        });
    }

    // Attach click listener for Create Backup button
    var createBtn = document.getElementById('openCreateBackupBtn');
    if (createBtn) {
        createBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openModalById('#createBackupModal');
        });
    }

    // Attach click listener for Upload Backup button
    var uploadBtn = document.getElementById('openUploadBackupBtn');
    if (uploadBtn) {
        uploadBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openModalById('#uploadBackupModal');
        });
    }

    // Delegate clicks for any data-toggle="modal" elements (restore and delete modals)
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-toggle="modal"]');
        if (trigger && !trigger.id) {
            var targetSelector = trigger.getAttribute('data-target');
            if (targetSelector) {
                e.preventDefault();
                openModalById(targetSelector);
            }
        }
    });
});
</script>
@endpush

@endsection
