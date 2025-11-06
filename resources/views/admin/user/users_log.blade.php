<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Student Learning Activity</h5>
        <small class="text-muted">Click on a student’s name to view detailed activity per LOM.</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="table-responsive">
        <table id="studentLogsTable" class="table table-hover table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>NRP</th>
                    <th>Total Access</th>
                    <th>Total Duration (minutes)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $index => $student)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.lom-user-logs.show', $student->id) }}" class="fw-semibold text-decoration-none">
                                {{ $student->id }}
                            </a>
                        </td>
                        <td class="text-center">{{ $student->total_access }}</td>
                        <td class="text-center">{{ round($student->total_duration / 60, 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.lom-user-logs.show', $student->id) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            No student activity data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Optional: Add interactivity with DataTables --}}
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (window.jQuery && $.fn.DataTable) {
        $('#studentLogsTable').DataTable({
            order: [[3, 'desc']],
            language: {
            
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                zeroRecords: "No matching records found",
                info: "Showing _START_–_END_ of _TOTAL_ entries",
                infoEmpty: "No data available",
                infoFiltered: "(filtered from _MAX_ total entries)",
                

            }
        });
    }
});
</script>
@endpush
