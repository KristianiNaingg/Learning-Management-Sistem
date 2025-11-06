@extends('layouts.v_template')
@section('content')
    @include($menu)
    <div class="container mt-4">
        <h2>Add New URL {{ $selectedSubtopic ? 'for ' . $selectedSubtopic->title : '' }}</h2>
        
        <form action="{{ route('urls.store') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="subtopic_id" value="{{ $selectedSubtopic ? $selectedSubtopic->id : old('subtopic_id', '') }}">

            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">URL Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="invalid-feedback">Please provide a URL name.</div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="url_link" class="form-label">URL Link</label>
                        <input type="url" class="form-control" id="url_link" name="url_link" value="{{ old('url_link') }}" required>
                        <div class="invalid-feedback">Please provide a valid URL (e.g., https://example.com).</div>
                        @error('url_link')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Learning Style Dimension -->
                    @if($learningDimensions->isNotEmpty())
                        @php
                            $dimension = $learningDimensions->first();
                        @endphp
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dimension-checkbox" name="learning_style_option" value="{{ $dimension->id }}" {{ old('learning_style_option') ? 'checked' : '' }}>
                                <label class="form-check-label" for="dimension-checkbox">
                                    Learning Style: {{ $dimension->dimension }}
                                </label>
                            </div>
                            <div id="options-container" style="display: {{ old('learning_style_option') ? 'block' : 'none' }};">
                                <label class="form-label mt-2">Select Learning Style Option</label>
                                <div id="options-list">
                                    @foreach($dimension->options as $option)
                                        <div class="form-check">
                                            <input class="form-check-input option-radio" type="radio"
                                                   name="learning_style_option_id"
                                                   value="{{ $option->id }}"
                                                   id="option-{{ $option->id }}"
                                                   {{ old('learning_style_option_id') == $option->id ? 'checked' : '' }}
                                                   {{ old('learning_style_option') ? '' : 'disabled' }}>
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
                        <div class="alert alert-info">
                            <strong>Info:</strong> No learning style dimensions available.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create URL</button>
                <a href="{{ $selectedSubtopic ? route('topics.show', [$selectedSubtopic->topic->course_id, $selectedSubtopic->topic_id]) : url()->previous() }}" class="btn btn-secondary">Cancel</a>
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
        const dimensionCheckbox = document.getElementById('dimension-checkbox');
        if (dimensionCheckbox) {
            dimensionCheckbox.addEventListener('change', function () {
                const optionsContainer = document.getElementById('options-container');
                const radioButtons = document.querySelectorAll('.option-radio');
                
                optionsContainer.style.display = this.checked ? 'block' : 'none';
                radioButtons.forEach(radio => {
                    radio.disabled = !this.checked;
                    if (!this.checked) {
                        radio.checked = false;
                    }
                });
            });
        }

        // Client-side validation
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', function (event) {
                const dimensionChecked = document.getElementById('dimension-checkbox')?.checked || false;
                const radios = document.querySelectorAll('input[name="learning_style_option_id"]:checked');
                const errorDiv = document.getElementById('options-error');
                const nameInput = document.getElementById('name');
                const urlInput = document.getElementById('url_link');

                // Validate name
                if (!nameInput.value.trim()) {
                    nameInput.classList.add('is-invalid');
                    event.preventDefault();
                    event.stopPropagation();
                }

                // Validate URL
                const urlPattern = /^(https?:\/\/)?([\w-]+(\.[\w-]+)+\/?|localhost|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:\d+)?(\/.*)?$/;
                if (!urlInput.value.trim() || !urlPattern.test(urlInput.value)) {
                    urlInput.classList.add('is-invalid');
                    event.preventDefault();
                    event.stopPropagation();
                }

                // Validate dimension options if checkbox is checked
                if (dimensionChecked && radios.length === 0 && errorDiv) {
                    errorDiv.style.display = 'block';
                    event.preventDefault();
                    event.stopPropagation();
                } else if (errorDiv) {
                    errorDiv.style.display = 'none';
                }

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // Clear validation on input
        document.getElementById('name')?.addEventListener('input', function () {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });

        document.getElementById('url_link')?.addEventListener('input', function () {
            const urlPattern = /^(https?:\/\/)?([\w-]+(\.[\w-]+)+\/?|localhost|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:\d+)?(\/.*)?$/;
            if (this.value.trim() && urlPattern.test(this.value)) {
                this.classList.remove('is-invalid');
            }
        });
    </script>
    @include('components.tinymce', ['selector' => 'textarea#description'])
@endsection