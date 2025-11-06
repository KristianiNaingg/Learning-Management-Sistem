<div class="container">
    <h2>Edit Referensi untuk {{ $topic->title }}</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.topics', $course->id) }}">{{ $course->full_name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('topics.show', [$course->id, $topic->id]) }}">{{ $topic->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Referensi</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="editReferenceForm" action="{{ route('topics.reference.update', [$course->id, $topic->id, $reference->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="topic_id" value="{{ $topic->id }}">

                <div class="mb-3">
                    <label for="content" class="form-label">Referensi <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="4" required>{{ old('content', $reference->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Referensi</button>
                <a href="{{ route('topics.show', [$course->id, $topic->id]) }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('editReferenceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST', // Laravel handles PUT via _method field
            body: formData,
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success === false) {
                throw new Error(data.message || 'Failed to update reference.');
            }
            alert('Referensi berhasil diperbarui!');
            window.location.href = '{{ route('topics.show', [$course->id, $topic->id]) }}';
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Gagal memperbarui referensi. Silakan coba lagi.');
            if (error.response) {
                const errors = error.response.data.errors || {};
                for (let field in errors) {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = errors[field][0];
                        const parent = input.parentElement;
                        const existingFeedback = parent.querySelector('.invalid-feedback');
                        if (existingFeedback) existingFeedback.remove();
                        parent.appendChild(feedback);
                        input.classList.add('is-invalid');
                    }
                }
            }
        });
    });
</script>
