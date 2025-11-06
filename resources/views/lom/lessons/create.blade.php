@extends('layouts.v_template')

@section('content')
    @include($menu)
    <div class="container mt-4">
        <h2>Adding a new Lesson {{ $selectedSubtopic ? 'for ' . $selectedSubtopic->title : '' }}</h2>

        <form action="{{ route('lessons.store') }}" method="POST" class="needs-validation" novalidate>
            @csrf

            <!-- Input Hidden untuk subtopic_id dengan validasi -->
            @if($selectedSubtopic)
                <input type="hidden" name="subtopic_id" value="{{ $selectedSubtopic->id }}">
            @else
                <div class="alert alert-warning">
                    <strong>Warning:</strong> No subtopic selected. Please select a subtopic first.
                </div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <!-- Lesson Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Lesson Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        <div class="invalid-feedback">Please provide a lesson name.</div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="6">{{ old('description') }}</textarea>
                        <div class="invalid-feedback">Please provide a valid description.</div>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Learning Style Dimension (ID = 1) -->
                  
                 @if($learningDimensions->isNotEmpty())
                        @php
                            $dimension = $learningDimensions->first();
                        @endphp
                 
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dimension-checkbox" 
                                       name="dimension" value="{{ $dimension->id }}" 
                                       {{ old('dimension') ? 'checked' : '' }}>
                                <label class="form-check-label" for="dimension-checkbox">
                                    Learning Style Dimension: {{ $dimension->dimension }}
                                </label>
                            </div>
                            <div id="options-container" style="display: {{ old('dimension') ? 'block' : 'none' }};">
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
                        <div class="alert alert-info">
                            <strong>Info:</strong> Learning dimension with ID 3 not found.
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" {{ !$selectedSubtopic ? 'disabled' : '' }}>Submit</button>
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
                
                if (optionsContainer && radioButtons) {
                    optionsContainer.style.display = this.checked ? 'block' : 'none';
                    radioButtons.forEach(radio => {
                        radio.disabled = !this.checked;
                        if (!this.checked) {
                            radio.checked = false;
                        }
                    });
                }
            });
        }

        // Client-side validation for radio buttons
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', function (event) {
                const dimensionChecked = document.getElementById('dimension-checkbox')?.checked || false;
                const radios = document.querySelectorAll('input[name="dimension_options"]:checked');
                const errorDiv = document.getElementById('options-error');
                
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
    </script>
    @include('components.tinymce', ['selector' => 'textarea#description'])
@endsection