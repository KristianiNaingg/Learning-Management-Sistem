@extends('layouts.v_template')
@section('content')
    @include('menu.v_menu_admin')
    <div class="container mt-4">
        <h2>Adding a new Assignment for {{ $selectedSubtopic->title }}</h2>
        
        <form action="{{ route('assignments.store') }}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="sub_topic_id" value="{{ $selectedSubtopic->id }}">
        
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="invalid-feedback">Please provide an assignment name.</div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="6">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Konten Tugas (Opsional, PDF/DOC/JPG/PNG, max 10MB)</label>
                        <input type="file" class="form-control" id="content" name="content" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <div class="invalid-feedback">File harus berupa PDF, DOC, DOCX, JPG, JPEG, atau PNG, maksimum 10MB.</div>
                        @error('content')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                        <div class="invalid-feedback">Please provide a due date.</div>
                        @error('due_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Processing Dimension (ID = 1) -->
                    @if($learningDimensions->isNotEmpty())
                        @php
                            $dimension = $learningDimensions->first();
                        @endphp
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dimension-checkbox" name="dimension" value="{{ $dimension->id }}" {{ old('dimension') ? 'checked' : '' }}>
                                <label class="form-check-label" for="dimension-checkbox">
                                    Learning Style Dimension: {{ $dimension->dimension }}
                                </label>
                            </div>
                            <div id="options-container" style="display: {{ old('dimension') ? 'block' : 'none' }}">
                                <label class="form-label">Select Option</label>
                                <div id="options-list">
                                    @foreach($dimension->options as $option)
                                        <div class="form-check">
                                            <input class="form-check-input option-radio" type="radio"
                                                   name="dimension_options"
                                                   value="{{ $option->id }}"
                                                   id="option-{{ $option->id }}"
                                                   {{ old('dimension_options') == $option->id ? 'checked' : '' }}
                                                   {{ old('dimension') ? '' : 'disabled' }}>
                                            <label class="form-check-label" for="option-{{ $option->id }}">
                                                {{ $option->style_option_name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="invalid-feedback d-block" id="options-error" style="display: none;">
                                    Please select one option for {{ $dimension->dimension }}.
                                </div>
                            </div>
                        </div>
                    @else
                        <p>Processing dimension (ID: 1) not found.</p>
                    @endif
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('topics.show', [$selectedSubtopic->topic->course_id, $selectedSubtopic->topic_id]) }}" class="btn btn-secondary">Cancel</a>
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

        // Toggle options visibility and enable/disable radio buttons based on checkbox
        document.getElementById('dimension-checkbox').addEventListener('change', function () {
            const optionsContainer = document.getElementById('options-container');
            const radioButtons = document.querySelectorAll('.option-radio');
            optionsContainer.style.display = this.checked ? 'block' : 'none';
            radioButtons.forEach(radio => {
                radio.disabled = !this.checked;
                if (!this.checked) {
                    radio.checked = false; // Uncheck radio buttons when disabled
                }
            });
        });

        // Client-side validation for radio buttons and file input
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', function (event) {
                const dimensionChecked = document.getElementById('dimension-checkbox').checked;
                const radios = document.querySelectorAll('input[name="dimension_options"]:checked');
                const errorDiv = document.getElementById('options-error');
                if (dimensionChecked && radios.length === 0) {
                    errorDiv.style.display = 'block';
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    errorDiv.style.display = 'none';
                }

                const contentInput = document.getElementById('content');
                if (contentInput.files.length > 0) {
                    const fileSize = contentInput.files[0].size / 1024 / 1024; // Size in MB
                    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
                    if (fileSize > 10) {
                        contentInput.classList.add('is-invalid');
                        event.preventDefault();
                        event.stopPropagation();
                    } else if (!allowedTypes.includes(contentInput.files[0].type)) {
                        contentInput.classList.add('is-invalid');
                        event.preventDefault();
                        event.stopPropagation();
                    }
                }

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