<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container my-4">
    <div class="card p-4 shadow-sm">
        <h1 class="text-center mb-4">Edit {{ $topic->title }}</h1>
        <div id="alert-placeholder"></div>
        <form action="{{ route('topics.update', [$course->id, $topic->id]) }}" method="POST" id="topic-form">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="title" class="form-label fw-bold">Topic Title:</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $topic->title) }}" required aria-describedby="titleHelp">
                <small id="titleHelp" class="form-text text-muted">Enter a clear and concise topic title.</small>
                @error('title')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-bold">Topic Description:</label>
                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter topic description..." required aria-describedby="descriptionHelp">{{ old('description', $topic->description ?? '') }}</textarea>
                <small id="descriptionHelp" class="form-text text-muted">Briefly explain the content of this topic.</small>
                @error('description')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="sub_cpmk" class="form-label fw-bold">Sub-CPMK:</label>
                <textarea class="form-control" id="sub_cpmk" name="sub_cpmk" rows="4" placeholder="Enter Sub-CPMK..." required aria-describedby="subCpmkHelp">{{ old('sub_cpmk', $topic->sub_cpmk ?? '') }}</textarea>
                <small id="subCpmkHelp" class="form-text text-muted">Enter the relevant Sub-CPMK.</small>
                @error('sub_cpmk')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="sort_order" class="form-label fw-bold">Sort Order:</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $topic->sort_order) }}" required min="1" aria-describedby="sortOrderHelp">
                <small id="sortOrderHelp" class="form-text text-muted">Enter the topic order number.</small>
                @error('sort_order')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="visible" class="form-label fw-bold">Visibility:</label>
                <select class="form-select" id="visible" name="visible" required aria-describedby="visibleHelp">
                    <option value="1" {{ old('visible', $topic->visible) ? 'selected' : '' }}>Visible</option>
                    <option value="0" {{ old('visible', $topic->visible) ? '' : 'selected' }}>Hidden</option>
                </select>
                <small id="visibleHelp" class="form-text text-muted">Choose whether this topic is visible.</small>
                @error('visible')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Subtopics:</label>
                <div id="subtopics-list">
                    @foreach (old('subtopics', $topic->subtopics ?? []) as $index => $subtopic)
                        <div class="card p-3 mb-2 position-relative" id="sub-topic-{{ $index + 1 }}">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="deleteSubTopic('{{ $course->id }}', '{{ $topic->id }}', '{{ $subtopic->id ?? '' }}', 'sub-topic-{{ $index + 1 }}')" aria-label="Delete Subtopic"></button>
                            <div class="mb-2">
                                <label for="subtopics_{{ $index + 1 }}" class="form-label">Subtopic Title:</label>
                                <input type="text" class="form-control" id="subtopics_{{ $index + 1 }}" name="subtopics[{{ $index }}][title]" value="{{ old('subtopics.' . $index . '.title', $subtopic->title ?? '') }}" placeholder="Enter subtopic..." required>
                                <input type="hidden" name="subtopics[{{ $index }}][id]" value="{{ old('subtopics.' . $index . '.id', $subtopic->id ?? '') }}">
                                <input type="hidden" name="subtopics[{{ $index }}][sortorder]" value="{{ old('subtopics.' . $index . '.sortorder', $subtopic->sortorder ?? ($index + 1)) }}">
                                <input type="hidden" name="subtopics[{{ $index }}][visible]" value="{{ old('subtopics.' . $index . '.visible', $subtopic->visible ?? 1) }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary mt-2" onclick="addSubTopic()">Add Subtopic</button>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">References:</label>
                <div id="references-list">
                    @foreach (old('references', $topic->references ?? []) as $index => $reference)
                        <div class="card p-3 mb-2 position-relative" id="reference-{{ $index + 1 }}">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="deleteReference('{{ $course->id }}', '{{ $topic->id }}', '{{ $reference->id ?? '' }}', 'reference-{{ $index + 1 }}')" aria-label="Delete Reference"></button>
                            <div class="mb-2">
                                <label for="references_{{ $index + 1 }}" class="form-label">Reference:</label>
                                <textarea class="form-control" id="references_{{ $index + 1 }}" name="references[{{ $index }}][content]" rows="3" placeholder="Enter reference..." required>{{ old('references.' . $index . '.content', $reference->content ?? '') }}</textarea>
                                <input type="hidden" name="references[{{ $index }}][id]" value="{{ old('references.' . $index . '.id', $reference->id ?? '') }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary mt-2" onclick="addReference()">Add Reference</button>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary btn-lg w-100">Save Changes</button>
                <a href="{{ route('courses.topics', $course->id) }}" class="btn btn-secondary btn-lg w-100" onclick="cancelEdit(event)">Cancel</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    let subTopicCount = {{ count(old('subtopics', $topic->subtopics ?? [])) }};
    let referenceCount = {{ count(old('references', $topic->references ?? [])) }};

    // 🔔 Enhanced Alert Function with Icons
    function showAlert(message, type = 'success') {
        const iconMap = {
            success: 'success',
            warning: 'warning',
            danger: 'error'
        };

        Swal.fire({
            icon: iconMap[type] || 'info',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    }
      function editSuccess() {
        showAlert('Changes saved successfully!', 'success');
    }
function cancelEdit(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Cancel Editing?',
            text: 'Your changes will not be saved.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it',
            cancelButtonText: 'Continue editing'
        }).then((result) => {
            if (result.isConfirmed) {
                showAlert('Edit has been cancelled.', 'info');
                window.location.href = "{{ route('courses.topics', $course->id) }}";
            }
        });
    }
    function addSubTopic() {
        subTopicCount++;
        const newSubTopic = document.createElement('div');
        newSubTopic.className = 'card p-3 mb-2 position-relative';
        newSubTopic.id = `sub-topic-${subTopicCount}`;
        newSubTopic.innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                onclick="deleteSubTopic('{{ $course->id }}', '{{ $topic->id }}', '', 'sub-topic-${subTopicCount}')"
                aria-label="Delete Subtopic"></button>
            <div class="mb-2">
                <label for="subtopics_${subTopicCount}" class="form-label">Subtopic Title:</label>
                <input type="text" class="form-control" id="subtopics_${subTopicCount}"
                    name="subtopics[${subTopicCount - 1}][title]" placeholder="Enter subtopic..." required>
                <input type="hidden" name="subtopics[${subTopicCount - 1}][id]" value="">
                <input type="hidden" name="subtopics[${subTopicCount - 1}][sortorder]" value="${subTopicCount}">
                <input type="hidden" name="subtopics[${subTopicCount - 1}][visible]" value="1">
            </div>
        `;
        document.getElementById('subtopics-list').appendChild(newSubTopic);
        showAlert('Subtopic added to the form!', 'success');
    }

    function addReference() {
        referenceCount++;
        const newReference = document.createElement('div');
        newReference.className = 'card p-3 mb-2 position-relative';
        newReference.id = `reference-${referenceCount}`;
        newReference.innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                onclick="deleteReference('{{ $course->id }}', '{{ $topic->id }}', '', 'reference-${referenceCount}')"
                aria-label="Delete Reference"></button>
            <div class="mb-2">
                <label for="references_${referenceCount}" class="form-label">Reference:</label>
                <textarea class="form-control" id="references_${referenceCount}"
                    name="references[${referenceCount - 1}][content]" rows="3"
                    placeholder="Enter reference..." required></textarea>
                <input type="hidden" name="references[${referenceCount - 1}][id]" value="">
            </div>
        `;
        document.getElementById('references-list').appendChild(newReference);
        showAlert('Reference added to the form!', 'success');
    }

    async function deleteSubTopic(courseId, topicId, subtopicId, elementId) {
        if (!subtopicId) {
            document.getElementById(elementId).remove();
            showAlert('Subtopic removed from the form.', 'warning');
            return;
        }

        if (!confirm('Are you sure you want to delete this subtopic?')) return;

        try {
            const response = await fetch(`/courses/${courseId}/topics/${topicId}/subtopics/${subtopicId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to delete subtopic.');
            }

            document.getElementById(elementId).remove();
            showAlert('Subtopic deleted successfully!', 'success');
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    }

    async function deleteReference(courseId, topicId, referenceId, elementId) {
        if (!referenceId) {
            document.getElementById(elementId).remove();
            showAlert('Reference removed from the form.', 'warning');
            return;
        }

        if (!confirm('Are you sure you want to delete this reference?')) return;

        try {
            const response = await fetch(`/courses/${courseId}/topics/${topicId}/references/${referenceId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to delete reference.');
            }

            document.getElementById(elementId).remove();
            showAlert('Reference deleted successfully!', 'success');
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    }

    @if (session('success'))
        window.onload = () => {
            showAlert('{{ session('success') }}', 'success');
        };
    @endif

    @if ($errors->any())
        window.onload = () => {
            showAlert('There are errors in the form. Please check again.', 'danger');
        };
    @endif

</script>
