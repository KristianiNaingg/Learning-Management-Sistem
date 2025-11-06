<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/fo
nt/bootstrap-icons.css">

<div class="content d-flex flex-column flex-column-fluid mt-0" id="kt_content">
    <div class="container-fluid mt-0">
        <!-- Judul dan Tombol Tambah Course -->
        <div class="card mb-4 shadow-sm border-0 bg-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Learning Management Dashboard</h4>
                <a href="{{ route('course.create') }}" class="btn btn-primary fw-semibold px-4" aria-label="Add New Course">
                    <i class="bi bi-plus-lg me-2"></i>Add Course
                </a>
            </div>
        </div>

        <!-- Row: Course List dan Statistik -->
<!-- Row: Statistik Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="{{ route('admin.courses.index') }}" class="text-decoration-none">
            <div class="card shadow-sm rounded-4 stat-card p-2 bg-primary text-white text-center">
                <i class="bi bi-journal-bookmark-fill fs-2 mb-2 opacity-75"></i>
                <h6 class="fw-semibold text-uppercase mb-1">Courses</h6>
                <h3 class="fw-bold mb-1">{{ $course_count }}</h3>
                <small>Modules: {{ $topic_count }}</small>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm rounded-4 stat-card p-2 bg-success text-white text-center">
            <i class="bi bi-person-badge fs-2 mb-2 opacity-75"></i>
            <h6 class="fw-semibold text-uppercase mb-1">Instructors</h6>
            <h3 class="fw-bold mb-1">{{ $instructor_count }}</h3>
            <small>Active</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm rounded-4 stat-card p-2 bg-info text-white text-center">
            <i class="bi bi-people-fill fs-2 mb-2 opacity-75"></i>
            <h6 class="fw-semibold text-uppercase mb-1">Students</h6>
            <h3 class="fw-bold mb-1">{{ $student_count }}</h3>
            <small>Enrolled</small>
        </div>
    </div>
</div>

<!-- Row: Course List -->
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-white rounded-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center p-4 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"></i>Course List</h4>
                
            </div>
            <div class="card-body p-4">
                
                <div class="table-responsive">
    <div class="d-flex justify-content-between align-items-center mb-3 px-1">
        <div id="exportButtons"></div>
        <div class="dataTables_filter"></div>
    </div>
    <table class="table table-hover table-borderless align-middle" id="courseTable">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Title</th>
                                <th>Instructor</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $course->full_name }}</td>
                                <td>
                                    @php
                                        $teacher = $course->users && $course->users->count() > 0
                                            ? $course->users->first(fn($user) => $user->pivot->participant_role === 'Teacher')
                                            : null;
                                    @endphp
                                    {{ $teacher ? $teacher->name : 'No Instructor' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge px-3 py-2 {{ $course->visible ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $course->visible ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('courses.topics', $course->id) }}" class="btn btn-sm btn-icon btn-outline-primary btn-circle me-2" title="View"><i class="fi-rr-eye"></i></a>
                                        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-icon btn-outline-success btn-circle me-2" title="Edit"><i class="fi-rr-edit"></i></a>
                                        <button class="btn btn-sm btn-icon btn-outline-danger btn-circle deleteCourse" data-id="{{ $course->id }}" title="Delete"><i class="fi-rr-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No courses available</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



        <!-- USERS Section -->
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 text-dark">User List</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-primary fw-semibold px-4" aria-label="Add New User">
                        <i class="bi bi-person-plus me-2"></i>Manage User
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle" id="tableUser">
                        <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">No.</th>
                            <th class="fw-semibold">NRP/NIP/NIK</th>
                            <th class="fw-semibold">Name</th>
                            <th class="fw-semibold">Email</th>
                            <th class="fw-semibold">Role</th>
                            <th class="fw-semibold">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Data will be populated by DataTable -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #4dabf7 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #198754 0%, #51cf66 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #74e3ff 100%);
    }
    .card[data-hover="true"] {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card[data-hover="true"]:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .btn-group .btn {
        border-radius: 0.25rem !important;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.5em 1em;
    }

 
.stat-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
}
.table thead th {
    background-color: #f8f9fa;
}
.table tbody tr:hover {
    background-color: #f1f3f5;
}

</style>

<script>
    $(document).ready(function () {
        // Inisialisasi DataTable untuk tableUser
        var table = $('#tableUser').DataTable({
            processing: false,
            serverSide: true,
            ordering: false,
            dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rtip',
            buttons: [
                { extend: 'copy', text: 'Copy' },
                { extend: 'excel', text: 'Excel' },
                { extend: 'pdf', text: 'PDF' }
            ],
            ajax: "{{ route('users.index') }}",
            columns: [
                {
                    data: null,
                    name: 'no',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                {
                    data: 'name_role',
                    name: 'name_role',
                    render: function(data, type, row) {
                        return row.role ? row.role.name_role : 'No Role';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row) {
                        const statuses = ['pending', 'active', 'non-active'];
                        let badges = '';
                        statuses.forEach(status => {
                            let badgeClass = '';
                            let textClass = '';
                            const capitalizedStatus = status.charAt(0).toUpperCase() + status.slice(1);

                            if (status === data) {
                                switch (status) {
                                    case 'pending':
                                        badgeClass = 'bg-warning text-dark';
                                        break;
                                    case 'active':
                                        badgeClass = 'bg-success text-white';
                                        break;
                                    case 'non-active':
                                        badgeClass = 'bg-danger text-white';
                                        break;
                                }
                            } else {
                                switch (status) {
                                    case 'pending':
                                        badgeClass = 'border-warning';
                                        textClass = 'text-warning';
                                        break;
                                    case 'active':
                                        badgeClass = 'border-success';
                                        textClass = 'text-success';
                                        break;
                                    case 'non-active':
                                        badgeClass = 'border-danger';
                                        textClass = 'text-danger';
                                        break;
                                }
                            }

                            badges += `
                            <span class="badge status-badge ${badgeClass} ${textClass} me-1"
                                  data-id="${row.id}"
                                  data-status="${status}"
                                  style="cursor: pointer; padding: 6px 10px;"
                                  data-bs-toggle="tooltip"
                                  data-bs-title="Set to ${capitalizedStatus}">
                                ${capitalizedStatus}
                            </span>
                        `;
                        });

                        return `<div class="d-flex">${badges}</div>`;
                    }
                }
            ],
            language: {
                search: 'Search: ',
                
            }
        });

        // Event listener untuk klik badge
        $('#tableUser').on('click', '.status-badge', function() {
            const userId = $(this).data('id');
            const newStatus = $(this).data('status');
            updateStatus(userId, newStatus, $(this));
        });

        // Inisialisasi DataTable untuk courseTable
        const courseTable = $('#courseTable').DataTable({
            dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rtip',
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }
            ],
            language: {
                
                search: 'Search: ',
            },
            columnDefs: [
                { orderable: false, targets: 4 }
            ]
        });

        // Move buttons to custom container
        courseTable.buttons().container().appendTo('#exportButtons');

        // Inisialisasi tooltips untuk semua elemen
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Event listener untuk tombol delete course
        $('#courseTable').on('click', '.deleteCourse', function () {
            const courseId = $(this).data('id');
            const row = $(this).closest('tr');

            // Validasi courseId
            if (!courseId) {
                Swal.fire('Error!', 'Invalid course ID.', 'error');
                return;
            }

            // Konfirmasi sebelum hapus
            Swal.fire({
                title: 'Are you sure?',
                text: 'This course will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim AJAX DELETE request
                    $.ajax({
                        url: `/courses/${courseId}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                courseTable.row(row).remove().draw(false);
                                Swal.fire('Deleted!', response.message || 'Course deleted successfully.', 'success');
                            } else {
                                Swal.fire('Error!', response.message || 'Failed to delete course.', 'error');
                            }
                        },
                        error: function (xhr) {
                            const errorMessage = xhr.responseJSON?.message || 'An error occurred while deleting the course.';
                            Swal.fire('Error!', errorMessage, 'error');
                        }
                    });
                }
            });
        });
    });

    // Fungsi untuk memperbarui status pengguna
    function updateStatus(userId, newStatus, badgeElement) {
    if (!userId || !newStatus || !badgeElement) {
        Swal.fire('Error!', 'Invalid parameters.', 'error');
        return;
    }

    Swal.fire({
        title: 'Confirm',
        text: `Set status to ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, update!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/users/approve/${userId}`,
                type: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: newStatus
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Success!', response.message, 'success');

                        // === UPDATE BADGE SECARA LANGSUNG ===
                        const $badgesContainer = badgeElement.closest('.d-flex');
                        $badgesContainer.find('.status-badge').each(function() {
                            const $badge = $(this);
                            const status = $badge.data('status');

                            // Hapus semua class badge & text
                            $badge.removeClass('bg-success bg-warning bg-danger text-white text-dark border-success border-warning border-danger');

                            if (status === response.user.status) {
                                // Badge aktif
                                switch (status) {
                                    case 'active':
                                        $badge.addClass('bg-success text-white');
                                        break;
                                    case 'non-active':
                                        $badge.addClass('bg-danger text-white');
                                        break;
                                    case 'pending':
                                        $badge.addClass('bg-warning text-dark');
                                        break;
                                }
                            } else {
                                // Badge non-aktif (border only)
                                switch (status) {
                                    case 'active':
                                        $badge.addClass('border-success text-success');
                                        break;
                                    case 'non-active':
                                        $badge.addClass('border-danger text-danger');
                                        break;
                                    case 'pending':
                                        $badge.addClass('border-warning text-warning');
                                        break;
                                }
                            }
                        });

                        // === UPDATE DATA DI DATATABLE (tanpa reload) ===
                        const row = $('#tableUser').DataTable().row($badgeElement.closest('tr'));
                        const rowData = row.data();
                        rowData.status = response.user.status;
                        rowData.name_role = rowData.role ? rowData.role.name_role : 'No Role';
                        row.data(rowData).draw(false); // false = jaga posisi paging

                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'An error occurred.';
                    Swal.fire('Error!', msg, 'error');
                }
            });
        }
    });
}
</script>

