<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Course Management</h5>
                <a href="{{ route('course.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add Course
                </a>
            </div>

            <div class="card-body">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table" id="tableCourse">
                            <thead class="font-weight-bold text-center">
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Semester</th>
                                    <th>Status</th>
                                    <th>Instructor</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @forelse($courses as $course)
                                    <tr>
                                        <td>{{ $course->short_name }}</td>
                                        <td>{{ $course->full_name }}</td>
                                        <td>{{ $course->semester }}</td>
                                        <td>{{ $course->visible ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            @php
                                                $teacher = $course->users->first(function ($user) {
                                                    return $user->pivot->participant_role === 'Teacher';
                                                });
                                            @endphp
                                            {{ $teacher ? $teacher->name : 'No instructor assigned' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('courses.topics', $course->id) }}" 
                                               class="btn btn-sm btn-icon btn-outline-primary btn-circle me-2" 
                                               data-bs-toggle="tooltip" title="View Topics">
                                                <i class="fi-rr-eye"></i>
                                            </a>
                                            <a href="{{ route('courses.edit', $course->id) }}" 
                                               class="btn btn-sm btn-icon btn-outline-success btn-circle me-2" 
                                               data-bs-toggle="tooltip" title="Edit Course">
                                                <i class="fi-rr-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-icon btn-outline-danger btn-circle deleteCourse" 
                                                    data-id="{{ $course->id }}" title="Delete Course">
                                                <i class="fi-rr-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">No courses available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
$(document).ready(function () {

    // Search filter 
    $("#searchCourse").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tableCourse tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // SweetAlert Helper Functions
    function swal_success(message = 'Operation completed successfully!') {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
            showConfirmButton: false,
            timer: 1500,
            position: 'top-end'
        });
    }

    function swal_error(message = 'Something went wrong!') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonText: 'OK'
        });
    }

    function swal_cancel(message = 'Action has been cancelled.') {
        Swal.fire({
            icon: 'info',
            title: 'Cancelled',
            text: message,
            timer: 1200,
            showConfirmButton: false
        });
    }

    // Setup CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //  Delete Course
    $('body').on('click', '.deleteCourse', function () {
        var course_id = $(this).data("id");

        Swal.fire({
            title: 'Are you sure?',
            text: "This course will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('courses.destroy', ':id') }}".replace(':id', course_id),
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            swal_success('Course has been deleted.');
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            swal_error(data.message);
                        }
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON?.message || 'An unexpected error occurred.';
                        swal_error(msg);
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swal_cancel('Course deletion was cancelled.');
            }
        });
    });

    // Scrollable & Sortable Table (no pagination)
    $('#tableCourse').DataTable({
        order: [[3, 'desc']],
        buttons: ['copy', 'excel', 'pdf'],

            language: {
                search: "Search",
                
                lengthMenu: "Show _MENU_ entries",
                zeroRecords: "No matching records found",
                info: "Showing _START_–_END_ of _TOTAL_ entries",
                infoEmpty: "No data available",
                infoFiltered: "(filtered from _MAX_ total entries)"
            }
    });

});
</script>
@endpush
