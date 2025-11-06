<!-- Participant Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="participantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="participantModalLabel">Participants of {{ $course->full_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Participant Role</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($participants as $participant)
                            <tr id="participant-row-{{ $participant['id'] }}">
                                <td>{{ $participant['user_id'] ?? 'N/A' }}</td>
                                <td>{{ $participant['name'] }}</td>
                                <td>
                                    <form action="{{ route('participants.update', $participant['id']) }}" method="POST" class="update-role-form">
                                        @csrf
                                        @method('PUT')
                                        <select name="participant_role" class="form-select form-select-sm role-select" 
                                                data-participant-id="{{ $participant['id'] }}" 
                                                data-previous-role="{{ $participant['participant_role'] ?? 'N/A' }}">
                                            <option value="Student" {{ $participant['participant_role'] == 'Student' ? 'selected' : '' }}>Student</option>
                                            <option value="Teacher" {{ $participant['participant_role'] == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                            <option value="Admin" {{ $participant['participant_role'] == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if ($participant['id'])
                                        <form action="{{ route('participants.destroy', $participant['id']) }}" method="POST" class="delete-participant-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-participant-btn" data-participant-id="{{ $participant['id'] }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-danger">Invalid ID</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No participants for this course.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addParticipantModal-{{ $course->id }}">
                    <i class="bi bi-person-plus"></i> Add Participant
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal to Add Participant -->
<div class="modal fade" id="addParticipantModal-{{ $course->id }}" tabindex="-1" aria-labelledby="addParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addParticipantModalLabel">Add Participant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-participant-form-{{ $course->id }}" action="{{ route('participants.store', $course->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3 d-flex align-items-center">
                        <input type="text" class="form-control me-2" id="participant-search-{{ $course->id }}" 
                               placeholder="Search users by ID or name" autocomplete="off">
                        <select class="form-select me-2" id="participant-role-{{ $course->id }}" style="width: 120px;">
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div id="search-results-{{ $course->id }}" class="dropdown-menu w-100 mt-1" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                    <div id="participant-warning-{{ $course->id }}" class="text-danger mt-2" style="display: none;">
                        User is already in this course or selected list. Please choose another.
                    </div>
                    <div class="mb-3">
                        <ul id="selected-participants-{{ $course->id }}" class="list-group" style="max-height: 150px; overflow-y: auto;">
                            <li class="list-group-item text-muted" id="no-participants-{{ $course->id }}">No participants added yet.</li>
                        </ul>
                    </div>
                    <input type="hidden" name="participants" id="participants-input-{{ $course->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="next-btn-{{ $course->id }}">Next</button>
                </div>
            </form>
        </div>
    </div>
</div>
