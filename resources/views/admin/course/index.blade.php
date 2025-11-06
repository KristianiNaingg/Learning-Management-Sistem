<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="content d-flex flex-column flex-column-fluid py-5 mt-2" id="kt_content">
    <div class="container-lg">
        <!-- Notifications -->
        <div id="alert-container" class="mb-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Header -->
        <div class="card mb-4 border-0 shadow-sm rounded-3">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 p-4">
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Courses</h5>
                    <p class="text-muted mb-0">Manage and organize learning content</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('courses.management') }}" class="btn btn-outline-primary rounded-pill px-4 py-2">
                        <i class="fas fa-cog me-2"></i>Manage Courses
                    </a>
                    <a href="{{ route('course.create') }}" class="btn rounded-pill px-4 py-2 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-plus me-2"></i>New Course
                    </a>
                </div>
            </div>
        </div>

        <!-- Course List -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($courses as $course)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden" style="transition: all 0.3s ease;">
                        <img src="{{ $course->course_image ? asset('storage/' . $course->course_image) : 'https://via.placeholder.com/300x180?text=No+Image' }}"
                             class="card-img-top" style="height: 180px; object-fit: cover;">

                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0 fw-bold flex-grow-1 pe-2">
                                    {{ Str::limit($course->full_name, 50) }}
                                </h5>
                                <span class="badge bg-white text-primary border border-primary fs-6">
                                    <i class="fas fa-users me-1"></i>{{ $course->users->count() }}
                                </span>
                            </div>

                            <div class="mt-auto d-flex justify-content-end gap-2">
                                <!-- View -->
                                <a href="{{ route('courses.topics', $course->id) }}"
                                   class="btn btn-sm btn-icon btn-outline-primary btn-circle mr-2 view"
                                   style="width: 38px; height: 38px; color: #667eea; border: 1.5px solid #dee2e6;"
                                   title="View Topics">
                                    <i class="fi-rr-eye"></i>
                                </a>

                                <!-- Participants -->
                                <button type="button"
                                        class="btn btn-sm btn-icon btn-outline-primary btn-circle mr-2 viewUser"
                                        style="width: 38px; height: 38px; color: #2a9d8f; border: 1.5px solid #dee2e6;"
                                        data-bs-toggle="modal" data-bs-target="#participantModal-{{ $course->id }}"
                                        title="View Participants">
                                    <i class="fi-rr-users"></i>
                                </button>

                                <!-- More Options -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-outline-primary btn-circle mr-2 viewUser"
                                            style="width: 38px; height: 38px; color: #6c757d; border: 1.5px solid #dee2e6;"
                                            type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('courses.edit', $course->id) }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="delete-course-form d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participant Modal -->
                @php
                    $participants = $course->users->map(function ($user) use ($course) {
                        if (!isset($user->pivot) || !isset($user->pivot->id)) {
                            \Log::warning('Missing pivot ID for user: ' . $user->id . ' in course: ' . ($user->pivot->course_id ?? 'unknown'));
                            return null;
                        }
                        return [
                            'user_id' => $user->id,
                            'id' => $user->pivot->id,
                            'course_id' => $user->pivot->course_id,
                            'name' => $user->name,
                            'participant_role' => $user->pivot->participant_role ?? 'N/A',
                        ];
                    })->filter()->values();
                @endphp
                @include('admin.course.participant', [
                    'course' => $course,
                    'participants' => $participants,
                    'modalId' => 'participantModal-' . $course->id
                ])
            @endforeach

            <!-- Add Course Card -->
            <div class="col">
                <a href="{{ route('course.create') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm rounded-3 d-flex align-items-center justify-content-center add-course-card"
                         style="border: 2.5px dashed #667eea; background: rgba(102,126,234,0.08); transition: all 0.3s ease;">
                        <i class="fas fa-plus-circle" style="font-size: 3.2rem; color: #667eea;"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- JS -->

<style>
    .card { transition: all 0.3s ease; }
    .card:hover { transform: translateY(-6px); box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important; }
    .card-img-top { transition: opacity 0.3s; }
    .card-img-top:hover { opacity: 0.9; }

    .btn-sm.rounded-circle {
        transition: all 0.3s ease;
    }
    .btn-sm.rounded-circle:hover {
        background: #667eea !important;
        color: white !important;
        border-color: #667eea !important;
        transform: scale(1.1);
    }

    .add-course-card:hover {
        background: rgba(102,126,234,0.15) !important;
        border-color: #5a67d8 !important;
        transform: translateY(-4px);
    }
    .add-course-card:hover i { transform: scale(1.2); }

    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 0.5rem;
    }
    .dropdown-item {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    .dropdown-item:hover { background: #f1f3f5; }

    .alert {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    @media (max-width: 768px) {
        .btn-sm.rounded-circle { width: 34px; height: 34px; }
        .add-course-card i { font-size: 2.8rem; }
    }
</style>

<script>
    const allParticipants = @json($courses->mapWithKeys(fn($c) => [$c->id => $c->users->pluck('id')->toArray()])->toArray());

    document.addEventListener('DOMContentLoaded', () => {
        // Delete Course
        document.querySelectorAll('.delete-course-form').forEach(form => {
            form.addEventListener('submit', e => {
                e.preventDefault();
                const name = form.closest('.card').querySelector('.card-title').textContent.trim();
                Swal.fire({
                    title: 'Delete course?',
                    text: `"${name}" will be permanently deleted!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e63946',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then(r => r.isConfirmed && form.submit());
            });
        });

        // Delete Participant
        document.querySelectorAll('.delete-participant-form').forEach(form => {
            form.addEventListener('submit', e => {
                e.preventDefault();
                const name = form.closest('tr').querySelector('td:first-child').textContent;
                Swal.fire({
                    title: 'Delete participant?',
                    text: `${name} will be removed from this course.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e63946',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then(r => r.isConfirmed && form.submit());
            });
        });

        // Add Participant Modal
        document.querySelectorAll('.modal[id^="addParticipantModal-"]').forEach(modal => {
            const courseId = modal.id.replace('addParticipantModal-', '');
            const searchInput = document.getElementById(`participant-search-${courseId}`);
            const searchResults = document.getElementById(`search-results-${courseId}`);
            const selectedList = document.getElementById(`selected-participants-${courseId}`);
            const hiddenInput = document.getElementById(`participants-input-${courseId}`);
            const addForm = document.getElementById(`add-participant-form-${courseId}`);
            const warning = document.getElementById(`participant-warning-${courseId}`);
            const roleSelect = document.getElementById(`participant-role-${courseId}`);
            let selected = [];
            let isSelecting = false;

            const existing = allParticipants[courseId] || [];

            const isExist = id => selected.some(p => p.id == id) || existing.includes(Number(id));

            const updateList = () => {
                selectedList.innerHTML = selected.length === 0
                    ? `<li class="list-group-item text-muted">No participants yet</li>`
                    : selected.map(p => `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${p.name}
                            <div>
                                <select class="form-select form-select-sm d-inline w-auto" onchange="updateRole(${p.id}, this.value)">
                                    <option value="Student" ${p.role==='Student'?'selected':''}>Student</option>
                                    <option value="Teacher" ${p.role==='Teacher'?'selected':''}>Teacher</option>
                                    <option value="Admin" ${p.role==='Admin'?'selected':''}>Admin</option>
                                </select>
                                <button class="btn btn-sm btn-danger ms-2" onclick="removeSelected(${p.id})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </li>
                    `).join('');
                hiddenInput.value = JSON.stringify(selected);
            };

            window.updateRole = (id, role) => {
                const p = selected.find(x => x.id == id);
                if (p) p.role = role;
            };

            window.removeSelected = id => {
                selected = selected.filter(p => p.id != id);
                updateList();
                warning.style.display = 'none';
            };

            searchInput?.addEventListener('input', debounce(async () => {
                if (isSelecting) return;
                const q = searchInput.value.trim();
                if (q.length < 2) {
                    searchResults.innerHTML = '';
                    searchResults.style.display = 'none';
                    return;
                }

                try {
                    const res = await fetch(`/users/search?term=${encodeURIComponent(q)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const users = await res.json();

                    searchResults.innerHTML = users.length === 0
                        ? '<div class="dropdown-item text-muted">Not found</div>'
                        : users.map(u => {
                            const disabled = isExist(u.id);
                            return `
                                <div class="dropdown-item ${disabled?'text-muted':''}"
                                     style="${disabled?'opacity:0.5;pointer-events:none;':''}"
                                     ${!disabled?`onclick="addUser(${u.id}, '${u.name.replace(/'/g, "\\'")}', ${courseId})"`:''}>
                                    ${u.id} - ${u.name}
                                </div>`; 
                        }).join('');
                    searchResults.style.display = 'block';
                } catch (e) {
                    searchResults.innerHTML = '<div class="dropdown-item text-danger">Error</div>';
                    searchResults.style.display = 'block';
                }
            }, 300));

            window.addUser = (id, name) => {
                if (isExist(id)) {
                    warning.style.display = 'block';
                    setTimeout(() => warning.style.display = 'none', 2000);
                    return;
                }
                selected.push({ id, name, role: roleSelect?.value || 'Student' });
                updateList();
                searchInput.value = '';
                searchResults.style.display = 'none';
            };

            document.addEventListener('click', e => {
                if (searchInput && !searchInput.contains(e.target) && searchResults && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                    warning.style.display = 'none';
                }
            });

            addForm?.addEventListener('submit', e => {
                e.preventDefault();
                if (selected.length === 0) {
                    Swal.fire('Select participants!', 'At least 1 participant must be selected.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Add participants?',
                    text: `${selected.length} participants will be added.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, add!',
                    cancelButtonText: 'Cancel'
                }).then(r => {
                    if (!r.isConfirmed) return;

                    fetch(addForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ participants: selected.map(p => ({ id: p.id, role: p.role })) })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Failed', data.message || 'An error occurred.', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Failed to add participants.', 'error'));
                });
            });

            modal.addEventListener('hidden.bs.modal', () => {
                selected = [];
                updateList();
                searchInput.value = '';
                searchResults.innerHTML = '';
                warning.style.display = 'none';
            });

            searchInput?.setAttribute('autocomplete', 'off');
        });

        function debounce(func, wait) {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => func(...args), wait);
            };
        }
    });
</script>
