<div class="container">
    <h2>Edit Subtopic: {{ $subtopic->title }}</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.topics', $course->id) }}">{{ $course->full_name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('topics.show', [$course->id, $topic->id]) }}">{{ $topic->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Subtopic</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            <form id="editSubtopicForm" action="{{ route('topics.subtopic.update', [$course->id, $topic->id, $subtopic->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="topic_id" value="{{ $topic->id }}">

                <div class="mb-3">
                    <label for="title" class="form-label">Subtopic Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $subtopic->title) }}" required>
                    @error('title')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="visible" name="visible" value="1" {{ old('visible', $subtopic->visible) ? 'checked' : '' }}>
                    <label class="form-check-label" for="visible">Visible</label>
                    @error('visible')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sortorder hidden field (can be made editable if needed) -->
                <input type="hidden" name="sortorder" value="{{ old('sortorder', $subtopic->sortorder) }}">
                @error('sortorder')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn btn-primary">Update Subtopic</button>
                <a href="{{ route('topics.show', [$course->id, $topic->id]) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editSubtopicForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const errorContainer = document.createElement('div');
    errorContainer.className = 'alert alert-danger mt-3';
    errorContainer.style.display = 'none';
    form.appendChild(errorContainer);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        errorContainer.style.display = 'none';
        if (data.success === false) {
            errorContainer.textContent = data.message || 'Failed to update subtopic.';
            errorContainer.style.display = 'block';
        } else {
            const successContainer = document.createElement('div');
            successContainer.className = 'alert alert-success mt-3';
            successContainer.textContent = 'Subtopic updated successfully!';
            form.appendChild(successContainer);
            setTimeout(() => {
                window.location.href = '{{ route('topics.show', [$course->id, $topic->id]) }}';
            }, 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorContainer.textContent = 'An error occurred while updating the subtopic. Please try again.';
        errorContainer.style.display = 'block';
    });
});
</script>
