@extends('layouts.v_template')

@section('content')
    @include('menu.v_menu_admin')
    <div class="container mt-4">
        <h2>Adding a new Quiz for {{ $selectedSubtopic->title }}</h2>
        
        <form action="{{ route('quizs.store') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="sub_topic_id" value="{{ $selectedSubtopic->id }}">

            <!-- General Section -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="invalid-feedback">Please provide a quiz name.</div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="6">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Learning Dimension -->
                    @if($learningDimensions->isNotEmpty())
                        <div class="mb-3">
                            <label for="learning_style_id" class="form-label">Learning Style Dimension</label>
                            <select class="form-select" id="learning_style_id" name="learning_style_id" required>
                                <option value="" disabled selected>Select a dimension</option>
                                @foreach($learningDimensions as $dimension)
                                    <option value="{{ $dimension->id }}" {{ old('learning_style_id') == $dimension->id ? 'selected' : '' }}>
                                        {{ $dimension->dimension }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a learning style dimension.</div>
                            @error('learning_style_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <p>No learning style dimensions available.</p>
                    @endif
                </div>
            </div>

            <!-- Timing Section -->
            <div class="card mb-3">
                <div class="card-header">Timing</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="time_open" class="form-label">Open the quiz</label>
                        <input type="datetime-local" class="form-control" id="time_open" name="time_open" value="{{ old('time_open') }}">
                        @error('time_open')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="time_close" class="form-label">Close the quiz</label>
                        <input type="datetime-local" class="form-control" id="time_close" name="time_close" value="{{ old('time_close') }}">
                        @error('time_close')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="time_limit" class="form-label">Time limit (seconds)</label>
                        <input type="number" class="form-control" id="time_limit" name="time_limit" min="0" value="{{ old('time_limit') }}">
                        @error('time_limit')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Grade Section -->
            <div class="card mb-3">
                <div class="card-header">Grade</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="max_attempts" class="form-label">Attempts allowed</label>
                        <input type="number" class="form-control" id="max_attempts" name="max_attempts" min="1" value="{{ old('max_attempts', 1) }}">
                        @error('max_attempts')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="grade_to_pass" class="form-label">Grade to pass</label>
                        <input type="number" class="form-control" id="grade_to_pass" name="grade_to_pass" min="0" max="100" value="{{ old('grade_to_pass') }}">
                        @error('grade_to_pass')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" name="action" value="save_return">Save and return to course</button>
                <button type="submit" class="btn btn-secondary" name="action" value="save_display">Save and display</button>
                <a href="{{ route('topics.show', [$selectedSubtopic->topic->course_id, $selectedSubtopic->topic_id]) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        // Polyfill for Promise.allSettled (if needed)
        if (!Promise.allSettled) {
            Promise.allSettled = function (promises) {
                return Promise.all(
                    promises.map(function (promise) {
                        return Promise.resolve(promise).then(
                            function (value) {
                                return { status: 'fulfilled', value: value };
                            },
                            function (reason) {
                                return { status: 'rejected', reason: reason };
                            }
                        );
                    })
                );
            };
        }

        // Client-side validation
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    </script>
    @include('components.tinymce', ['selector' => 'textarea#description'])
@endsection