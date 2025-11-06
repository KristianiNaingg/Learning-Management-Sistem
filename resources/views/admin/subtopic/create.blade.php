<div class="container">
    <h2>Tambah Sub Materi Untuk {{ $topic->title }}</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.topics', $course->id) }}">{{ $course->full_name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('topics.show', [$course->id, $topic->id]) }}">{{ $topic->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Sub Materi</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-body">
            <form id="createSubtopicForm" action="{{ route('topics.subtopic.store', [$course->id, $topic->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="topic_id" value="{{ $topic->id }}">

                <div class="mb-3">
                    <label for="title" class="form-label">Subtopic Title</label>
                    <input type="text" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" id="title" name="title" value="{{ old('title') }}" required aria-describedby="titleError">
                    @error('title')
                    <div id="titleError" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input {{ $errors->has('visible') ? 'is-invalid' : '' }}" id="visible" name="visible" value="1" {{ old('visible', 1) ? 'checked' : '' }} aria-describedby="visibleError">
                    <label class="form-check-label" for="visible">Visible</label>
                    @error('visible')
                    <div id="visibleError" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="sortorder" value="{{ old('sortorder', $topic->subtopics->count() + 1) }}">
                @error('sortorder')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn btn-primary" id="submitButton">Save Subtopic</button>
                <a href="{{ route('topics.show', [$course->id, $topic->id]) }}" class="btn btn-secondary" onclick="return confirm('Are you sure you want to cancel?')">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('createSubtopicForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitButton = form.querySelector('#submitButton');
        submitButton.disabled = true;

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success === false) {
                    let errorMessage = data.message || 'Failed to create subtopic.';
                    if (data.errors) {
                        errorMessage = 'Please fix the following errors:\n' + Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire('Error', errorMessage, 'error');
                } else {
                    Swal.fire('Success', 'Subtopic created successfully!', 'success').then(() => {
                        window.location.href = '{{ route('topics.show', [$course->id, $topic->id]) }}';
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to create subtopic. Please try again.', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
            });
    });
</script>
