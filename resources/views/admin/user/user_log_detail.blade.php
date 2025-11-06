<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Activity: 
            <span class="text-primary">{{ $student->id }}-{{ $student->name }}  </span>
        </h3>
        <a href="{{ route('admin.lom-logs.index') }}" class="btn btn-secondary">
            ← Back to Student List
        </a>
    </div>

    <!-- ===== CHART SECTION ===== -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Activity Summary</h5>
            <canvas id="activityChart" height="120"></canvas>
        </div>
    </div>

    <!-- ===== FILTER + TABLE ===== -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Detailed Logs</h5>
        <div class="d-flex align-items-center">
            <label for="lomTypeFilter" class="me-2 fw-semibold">Filter by Type:</label>
            <select id="lomTypeFilter" class="form-select form-select-sm" style="width: 180px;">
                <option value="">All Types</option>
                <option value="page">Page</option>
                <option value="quiz">Quiz</option>
                <option value="file">File</option>
                <option value="lesson">Lesson</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table id="logDetailTable" class="table table-striped table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>LOM ID</th>
                    <th>LOM Type</th>
                    <th>Total Access</th>
                    <th>Total Duration (minutes)</th>
                    <th>Last Accessed</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $index => $log)
                    <tr class="text-center">
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-light text-dark">{{ $log->lom_id }}</span></td>
                        <td>
                            <span class="badge 
                                @switch($log->lom_type)
                                    @case('page') bg-primary @break
                                    @case('quiz') bg-success @break
                                    @case('file') bg-info @break
                                    @case('lesson') bg-warning text-dark @break
                                    @default bg-secondary
                                @endswitch
                            ">
                                <i class="fas 
                                    @switch($log->lom_type)
                                        @case('page') fa-book-open @break
                                        @case('quiz') fa-question-circle @break
                                        @case('file') fa-file-alt @break
                                        @case('lesson') fa-chalkboard-teacher @break
                                        @default fa-cube
                                    @endswitch
                                "></i>
                                {{ ucfirst($log->lom_type) }}
                            </span>
                        </td>
                        <td>{{ $log->total_access }}</td>
                        <td>{{ round($log->total_duration / 60, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->last_access)->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            No recorded LOM activity yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<!-- ===== Chart.js CDN ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- ===== DataTables Init + Chart ===== -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (!window.jQuery || !$.fn.DataTable) return;

    // ==== DataTables setup ====
    const table = $('#logDetailTable').DataTable({
        order: [[5, 'desc']],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No activity data found",
            info: "Showing _START_–_END_ of _TOTAL_ entries",
            infoEmpty: "No data available",
            infoFiltered: "(filtered from _MAX_ total entries)"
        }
    });

    // ==== Filter by LOM Type ====
    $('#lomTypeFilter').on('change', function() {
        const type = $(this).val();
        table.column(2).search(type ? type : '', true, false).draw();
    });

    // ==== Chart Data Setup ====
    const chartCtx = document.getElementById('activityChart').getContext('2d');
    const chartData = {
        labels: ['Page', 'Quiz', 'File', 'Lesson'],
        datasets: [
            {
                label: 'Total Accesses',
                data: [
                    @json($logs->where('lom_type', 'page')->sum('total_access')),
                    @json($logs->where('lom_type', 'quiz')->sum('total_access')),
                    @json($logs->where('lom_type', 'file')->sum('total_access')),
                    @json($logs->where('lom_type', 'lesson')->sum('total_access'))
                ],
                backgroundColor: 'rgba(13, 110, 253, 0.7)', // blue
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 6
            },
            {
                label: 'Total Duration (minutes)',
                data: [
                    @json(round($logs->where('lom_type', 'page')->sum('total_duration') / 60, 2)),
                    @json(round($logs->where('lom_type', 'quiz')->sum('total_duration') / 60, 2)),
                    @json(round($logs->where('lom_type', 'file')->sum('total_duration') / 60, 2)),
                    @json(round($logs->where('lom_type', 'lesson')->sum('total_duration') / 60, 2))
                ],
                backgroundColor: 'rgba(25, 135, 84, 0.7)', // green
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 1,
                borderRadius: 6
            }
        ]
    };

    // ==== Chart.js Init ====
    new Chart(chartCtx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 13 } }
                },
                title: {
                    display: true,
                    text: 'Total Access vs Duration per LOM Type',
                    font: { size: 16, weight: 'bold' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Count / Minutes' },
                    ticks: { stepSize: 1 }
                },
                x: {
                    title: { display: true, text: 'LOM Type' }
                }
            }
        }
    });
});
</script>
@endpush
