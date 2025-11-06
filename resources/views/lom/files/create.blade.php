@extends('layouts.v_template')

@section('content')
    @include('menu.v_menu_admin')
    <div class="container mt-4">
        <h2>Adding a new LOM File for {{ $selectedSubtopic->title }}</h2>
        @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif
        @if($errors->has('error'))
            <div class="alert alert-danger mb-3">{{ $errors->first('error') }}</div>
        @endif
        <form action="{{ route('files.store') }}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="sub_topic_id" value="{{ $selectedSubtopic->id }}">

            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="invalid-feedback">Please provide a file name.</div>
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

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label for="file_path" class="form-label">Upload File</label>
                        <input type="file" class="form-control" id="file_path" name="file_path[]" multiple required>
                        <div class="invalid-feedback">Pilih setidaknya satu file.</div>
                        <small class="form-text text-muted">Format: PDF, DOC(X), PPT(X), JPG, PNG, ZIP, RAR. Maks. 5MB</small>
                        <div id="file-preview-container" class="mt-2"></div>
                        @error('file_path.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Learning Style (Dimensions 2 or 3) -->
                    @php
                        $selectedDimensions = $learningDimensions->whereIn('id', [2, 3]);
                    @endphp
                    @if($selectedDimensions->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label">Dimensi Learning Style</label>
                            <div id="dimension-container">
                                @foreach($selectedDimensions as $dimension)
                                    <div class="form-check">
                                        <input class="form-check-input dimension-radio" type="radio"
                                               name="dimension"
                                               value="{{ $dimension->id }}"
                                               id="dimension-{{ $dimension->id }}"
                                               data-options='@json($dimension->options)'
                                               {{ old('dimension') == $dimension->id ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label" for="dimension-{{ $dimension->id }}">
                                            {{ $dimension->dimension }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="invalid-feedback d-block" id="dimension-error" style="display: none;">
                                Pilih satu dimensi.
                            </div>
                            @error('dimension')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" id="options-container" style="display: none;">
                            <label class="form-label">Pilih Opsi</label>
                            <div id="options-list"></div>
                            <div class="invalid-feedback d-block" id="options-error" style="display: none;">
                                Pilih satu opsi dimensi.
                            </div>
                            @error('dimension_options.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="alert alert-warning">Dimensi dengan ID 2 atau 3 tidak ditemukan.</div>
                    @endif

                    <!-- Folder Selection -->
                    @if($folders->isNotEmpty())
                        <div class="mb-3">
                            <label for="folders" class="form-label">Select Folders (Optional)</label>
                            <select class="form-control" id="folders" name="folders[]" multiple>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" {{ in_array($folder->id, old('folders', [])) ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('folders')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="alert alert-warning">No folders available.</div>
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

        // TinyMCE Initialization
        if (typeof tinymce === 'undefined') {
            console.error('TinyMCE tidak dimuat!');
        } else {
            console.log('TinyMCE terdeteksi, inisialisasi dimulai...');
            tinymce.init({
                selector: '#description',
                plugins: [
                    'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
                    'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
                ],
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ychecker typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                tinycomments_mode: 'embedded',
                tinycomments_author: 'Author name',
                mergetags_list: [
                    { value: 'First.Name', title: 'First Name' },
                    { value: 'Email', title: 'Email' },
                ],
                ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
                setup: function (editor) {
                    editor.on('init', function () {
                        console.log('TinyMCE berhasil diinisialisasi untuk #description');
                    });
                    editor.on('error', function (e) {
                        console.error('TinyMCE error:', e);
                    });
                }
            });
        }

        // File Preview
        const fileInput = document.getElementById('file_path');
        const previewContainer = document.getElementById('file-preview-container');

        if (fileInput && previewContainer) {
            fileInput.addEventListener('change', function () {
                previewContainer.innerHTML = '';
                if (fileInput.files && fileInput.files.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        const file = fileInput.files[i];
                        const fileElement = document.createElement('div');
                        fileElement.className = 'd-flex align-items-center justify-content-between bg-light rounded p-2 mb-1';
                        fileElement.innerHTML = `
                            <div>
                                <small class="text-muted">${file.name} (${(file.size / 1024).toFixed(2)} KB)</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeFile(${i})">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        previewContainer.appendChild(fileElement);
                    }
                }
            });
        }

        window.removeFile = function (index) {
            if (!fileInput) return;
            const newFiles = new DataTransfer();
            for (let i = 0; i < fileInput.files.length; i++) {
                if (i !== index) {
                    newFiles.items.add(fileInput.files[i]);
                }
            }
            fileInput.files = newFiles.files;
            fileInput.dispatchEvent(new Event('change'));
        };

        // Dimension and Options Handling
        const dimensionRadios = document.querySelectorAll('.dimension-radio');
        const optionsContainer = document.getElementById('options-container');
        const optionsList = document.getElementById('options-list');
        const dimensionError = document.getElementById('dimension-error');
        const optionsError = document.getElementById('options-error');

        if (dimensionRadios.length > 0 && optionsContainer && optionsList) {
            dimensionRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    optionsList.innerHTML = '';
                    if (this.checked) {
                        const options = JSON.parse(this.getAttribute('data-options'));
                        options.forEach(opt => {
                            const div = document.createElement('div');
                            div.className = 'form-check';
                            div.innerHTML = `
                                <input class="form-check-input option-radio" type="radio"
                                       name="dimension_options[]"
                                       value="${opt.id}"
                                       id="option-${opt.id}"
                                       ${opt.id == '{{ old("dimension_options.0") }}' ? 'checked' : ''}
                                       required>
                                <label class="form-check-label" for="option-${opt.id}">
                                    ${opt.style_option_name}
                                </label>
                            `;
                            optionsList.appendChild(div);
                        });
                        optionsContainer.style.display = 'block';
                    } else {
                        optionsContainer.style.display = 'none';
                    }
                });

                if (radio.checked) {
                    radio.dispatchEvent(new Event('change'));
                }
            });
        }

        // Form Validation
        const form = document.querySelector('.needs-validation');
        if (form) {
            form.addEventListener('submit', function (event) {
                let isValid = true;
                const checkedDimension = document.querySelector('.dimension-radio:checked');
                const checkedOption = document.querySelector('.option-radio:checked');

                if (!checkedDimension) {
                    dimensionError.style.display = 'block';
                    isValid = false;
                } else {
                    dimensionError.style.display = 'none';
                }

                if (checkedDimension && !checkedOption) {
                    optionsError.style.display = 'block';
                    isValid = false;
                } else {
                    optionsError.style.display = 'none';
                }

                const fileInput = document.getElementById('file_path');
                if (fileInput.files.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        const fileSize = fileInput.files[i].size / 1024 / 1024; // Size in MB
                        const allowedTypes = [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'image/jpeg',
                            'image/png',
                            'application/zip',
                            'application/x-rar-compressed'
                        ];
                        if (fileSize > 5) {
                            fileInput.classList.add('is-invalid');
                            isValid = false;
                        } else if (!allowedTypes.includes(fileInput.files[i].type)) {
                            fileInput.classList.add('is-invalid');
                            isValid = false;
                        }
                    }
                } else {
                    fileInput.classList.add('is-invalid');
                    isValid = false;
                }

                if (!form.checkValidity() || !isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }
    </script>
    @include('components.tinymce', ['selector' => 'textarea#description'])
@endsection