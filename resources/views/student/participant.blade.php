<!-- Participant Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="participantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-semibold" id="participantModalLabel">
                    <i class="bi bi-people-fill me-2"></i> Participants of {{ $course->full_name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="participantsTable-{{ $course->id }}" class="table table-striped table-hover align-middle mb-0 w-100">
                        <thead class="table-light ">
                            <tr>
                                <th class="text-center">No</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th class="text-center">Participant Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($participants as $index => $participant)
                                <tr id="participant-row-{{ $participant['id'] }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $participant['user_id'] ?? 'N/A' }}</td>
                                    <td>{{ $participant['name'] }}</td>
                                    <td class="text-center">
                                        @php
                                            $role = strtolower($participant['participant_role']);
                                            $badgeClass = match($role) {
                                                'student' => 'bg-info text-dark',
                                                'teacher' => 'bg-success',
                                                'admin' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 py-2">
                                            {{ $participant['participant_role'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-person-dash fs-4 d-block mb-2"></i>
                                        No participants for this course.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 d-flex justify-content-end">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan sebelum penutup </body> -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#participantsTable-{{ $course->id }}').DataTable({
        order: [[2, 'asc']], // urut berdasarkan kolom Name
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        columnDefs: [
            { orderable: false, targets: 0 } // kolom No tidak bisa di-sort
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search participant...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ participants",
            zeroRecords: "No matching participants found"
        }
    });
});
</script>
