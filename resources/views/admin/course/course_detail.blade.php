<div class="container my-4">
    <div class="card p-4">
        <h5 class="text-center mb-4">{{ $course->full_name }}</h5>

        <div class="list-group" id="topic-list">
            @if ($topics->isEmpty())
                <div class="list-group-item d-flex justify-content-between align-items-center disabled" aria-disabled="true">
                    <span>Topic 1: No topics available</span>
                    <div>
                        <button class="btn btn-outline-secondary btn-sm me-2" disabled>View Topic</button>
                        <button class="btn btn-outline-primary btn-sm me-2" disabled>Edit Material</button>
                        <button class="btn btn-outline-danger btn-sm" disabled>Delete</button>
                    </div>
                </div>
            @else
                @foreach ($topics as $topic)
                    <div class="list-group-item d-flex justify-content-between align-items-center" onclick="viewTopic({{ $course->id }}, {{ $topic->id }})">
                        <span>Topic {{ $topic->sort_order }}: {{ $topic->title }}</span>
                        <div>
                            <a href="{{ route('topics.show', [$course->id, $topic->id]) }}"
                               class="btn btn-outline-secondary btn-sm me-2"
                               onclick="event.stopPropagation();">
                                View Topic
                            </a>
                            <a href="{{ route('topics.edit', [$course->id, $topic->id]) }}"
                               class="btn btn-outline-primary btn-sm me-2"
                               onclick="event.stopPropagation();">
                                Edit Material
                            </a>
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="event.stopPropagation(); deleteTopic({{ $course->id }}, {{ $topic->id }})">
                                Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <button class="btn btn-outline-primary mt-3" onclick="addTopic()">Add Topic</button>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<script>
    // Navigate to topic
    function viewTopic(course_id, topicId) {
        window.location.href = '{{ route("topics.show", [":course_id", ":topicId"]) }}'
            .replace(':course_id', course_id)
            .replace(':topicId', topicId);
    }

    // Reusable SweetAlert success
    function swal_success(message = 'Action completed successfully!') {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500
        });
    }

    // Reusable SweetAlert error
    function swal_error(message = 'An error occurred while processing your request.') {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Something went wrong!',
            text: message,
            showConfirmButton: true
        });
    }

    //  Add Topic (AJAX)
    function addTopic() {
        const course_id = {{ $course->id }};

        fetch('{{ route("topics.store", $course->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ course_id }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                swal_success('Topic added successfully!');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                swal_error('Failed to add topic: ' + (data.message || 'Unknown error.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            swal_error('An error occurred while adding the topic: ' + error.message);
        });
    }

    // Delete Topic (SweetAlert2 + AJAX)
    function deleteTopic(course_id, topicId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This topic will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = '{{ route("topics.destroy", [":course_id", ":topicId"]) }}'
                .replace(':course_id', course_id)
                .replace(':topicId', topicId);

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');

                // Jika respon JSON dari controller
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }

                // Jika respon redirect (non-AJAX request)
                if (response.redirected) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'The topic has been deleted.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => window.location.href = response.url, 1500);
                    return;
                }

                throw new Error('Unexpected response type.');
            })
            .then(data => {
                if (!data) return; // jika sudah redirect
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message || 'The topic has been deleted.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    swal_error(data.message || 'Failed to delete topic.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal_error('An error occurred while deleting the topic: ' + error.message);
            });
        }
    });
    }
</script>
