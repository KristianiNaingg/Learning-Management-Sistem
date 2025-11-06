@extends('layouts.v_template')

@section('content')
    @include($menu)
    <div class="container mt-4">
        <h2>Adding a new Folder {{ $selectedSubtopic ? 'for ' . $selectedSubtopic->title : '' }}</h2>
        @if(session('success'))
            <div class="alert alert-success mb-3" style="font-family: 'Poppins', sans-serif;">{{ session('success') }}</div>
        @endif
        @if($errors->has('error'))
            <div class="alert alert-danger mb-3" style="font-family: 'Poppins', sans-serif;">{{ $errors->first('error') }}</div>
        @endif
        <form action="{{ route('folders.store') }}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
            @csrf
            @if($selectedSubtopic)
                <input type="hidden" name="subtopic_id" value="{{ $selectedSubtopic->id }}">
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <!-- Subtopic, Name, File Upload (tetap sama seperti sebelumnya) -->
                    @if(!$selectedSubtopic)
                        <div class="mb-3">
                            <label for="subtopic_id" class="form-label" style="font-family: 'Poppins', sans-serif; color: #3F4254;">Subtopic</label>
                            <select class="form-control" id="subtopic_id" name="subtopic_id" required>
                                <option value="">Select Subtopic</option>
                                @foreach($subtopics as $subtopic)
                                    <option value="{{ $subtopic->id }}" {{ old('subtopic_id', $subtopicId) == $subtopic->id ? 'selected' : '' }}>{{ $subtopic->title }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" style="font-family: 'Poppins', sans-serif;">Please select a subtopic.</div>
                            @error('subtopic_id')
                                <div class="text-danger" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label" style="font-family: 'Poppins', sans-serif; color: #3F4254;">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="invalid-feedback" style="font-family: 'Poppins', sans-serif;">Please provide a folder name.</div>
                        @error('name')
                            <div class="text-danger" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label" style="font-family: 'Poppins', sans-serif; color: #3F4254;">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="6">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- File Upload (tetap sama) -->
                    <div class="mb-3">
                        <label for="file_upload" class="form-label" style="font-family: 'Poppins', sans-serif; color: #3F4254;">Unggah File ke Folder (Opsional)</label>
                        <div id="upload-area" class="file-upload-area p-4 rounded border text-center"
                             style="border-color: #E4E6EF; border-style: dashed; min-height: 150px; display: flex; flex-direction: column; justify-content: center; align-items: center; cursor: pointer;"
                             onclick="document.getElementById('file_upload').click()">
                            <i class="fas fa-cloud-upload-alt mb-2" style="font-size: 2rem; color: #3699FF;"></i>
                            <h5 class="mb-1" style="font-family: 'Poppins', sans-serif;">Klik atau tarik file ke sini</h5>
                            <p class="mb-0 text-muted" style="font-family: 'Poppins', sans-serif;">Format: PDF, DOC(X), PPT(X), JPG, PNG, ZIP, RAR. Maks. 5MB</p>
                            <input type="file" name="files[]" id="file_upload" class="d-none" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.rar" onchange="previewFile(this)" multiple>
                        </div>
                        <div id="file-preview-container" class="mt-3"></div>
                        <small class="form-text text-muted" style="font-family: 'Poppins', sans-serif;">Format: PDF, DOC(X), PPT(X), JPG, PNG, ZIP, RAR. Maks. 5MB</small>
                        @error('files.*')
                            <div class="text-danger" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Learning Style (tetap sama seperti sebelumnya) -->
                    @php
                        $selectedDimensions = $learningDimensions->whereIn('id', [2, 3]);
                    @endphp
                    @if($selectedDimensions->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label" style="font-family: 'Poppins', sans-serif; color: #3F4254;">Dimensi Learning Style <span class="text-danger">*</span></label>
                            <div id="dimension-container" class="@error('dimension') is-invalid @enderror">
                                @foreach($selectedDimensions as $dimension)
                                    <div class="form-check">
                                        <input class="form-check-input dimension-radio" type="radio"
                                               name="dimension"
                                               value="{{ $dimension->id }}"
                                               id="dimension-{{ $dimension->id }}"
                                               data-options='@json($dimension->options)'
                                               {{ old('dimension') == $dimension->id ? 'checked' : '' }}
                                               required
                                               aria-describedby="dimension-error">
                                        <label class="form-check-label" for="dimension-{{ $dimension->id }}" style="font-family: 'Poppins', sans-serif;">
                                            {{ $dimension->dimension }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="invalid-feedback d-block" id="dimension-error" style="display: none; font-family: 'Poppins', sans-serif;">
                                Pilih satu dimensi.
                            </div>
                            @error('dimension')
                                <div class="text-danger" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" id="options-container" style="display: {{ old('dimension') ? 'block' : 'none' }};">
                            <label class="form-label" style="font-family: 'Poppins', sans-serif; color: #3F4254;">Pilih Opsi <span class="text-danger">*</span></label>
                            <div id="options-list" class="@error('dimension_options.*') is-invalid @enderror"></div>
                            <div class="invalid-feedback d-block" id="options-error" style="display: none; font-family: 'Poppins', sans-serif;">
                                Pilih tepat satu opsi dimensi.
                            </div>
                            @error('dimension_options.*')
                                <div class="text-danger" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="alert alert-warning" style="font-family: 'Poppins', sans-serif;">Dimensi dengan ID 2 atau 3 tidak ditemukan.</div>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" style="font-family: 'Poppins', sans-serif;">Submit</button>
                <a href="{{ $selectedSubtopic ? route('topics.show', [$selectedSubtopic->topic->course_id, $selectedSubtopic->topic_id]) : route('lom_folders.index') }}" class="btn btn-secondary" style="font-family: 'Poppins', sans-serif;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.tinyeditor.com/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        // Inisialisasi TinyMCE
        if (typeof tinymce === 'undefined') {
            console.error('TinyMCE not loaded!');
            alert('Gagal memuat editor teks. Silakan coba lagi atau hubungi administrator.');
        } else {
            tinymce.init({
                selector: '#description',
                plugins: 'advlist autolink lists link image charmap code fullscreen',
                toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
                height: 300,
                setup: function (editor) {
                    editor.on('init', function () {
                        console.log('TinyMCE initialized for #description');
                    });
                    editor.on('error', function (e) {
                        console.error('TinyMCE error:', e);
                    });
                }
            });
        }

        // File Preview with Drag-and-Drop and Image Support
        const fileInput = document.getElementById('file_upload');
        const uploadArea = document.getElementById('upload-area');
        const previewContainer = document.getElementById('file-preview-container');

        function previewFile(input) {
            console.log('Files selected:', input.files.length);
            previewContainer.innerHTML = '';
            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach((file, index) => {
                    const fileElement = document.createElement('div');
                    fileElement.className = 'd-flex align-items-center justify-content-between bg-light rounded p-2 mb-1';
                    let previewContent = `<small class="text-muted" style="font-family: 'Poppins', sans-serif;">${file.name} (${(file.size / 1024).toFixed(2)} KB)</small>`;
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            fileElement.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <img src="${e.target.result}" alt="${file.name}" style="max-width: 50px; max-height: 50px; margin-right: 10px;">
                                    ${previewContent}
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeFile(${index})">
                                    <i class="fas fa-times"></i>
                                </button>
                            `;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        fileElement.innerHTML = `
                            <div>${previewContent}</div>
                            <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeFile(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                    }
                    previewContainer.appendChild(fileElement);
                });
            }
        }

        window.removeFile = function (index) {
            if (!fileInput) return;
            console.log('Removing file at index:', index);
            const newFiles = new DataTransfer();
            Array.from(fileInput.files).forEach((file, i) => {
                if (i !== index) {
                    newFiles.items.add(file);
                }
            });
            fileInput.files = newFiles.files;
            previewFile(fileInput);
        };

        if (fileInput && uploadArea && previewContainer) {
            fileInput.addEventListener('change', () => previewFile(fileInput));
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.style.backgroundColor = '#f8f9fa';
            });
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.style.backgroundColor = 'transparent';
            });
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.style.backgroundColor = 'transparent';
                fileInput.files = e.dataTransfer.files;
                console.log('Files dropped:', e.dataTransfer.files.length);
                previewFile(fileInput);
            });
        }

        // Dimension and Options Handling
        const dimensionRadios = document.querySelectorAll('.dimension-radio');
        const optionsContainer = document.getElementById('options-container');
        const optionsList = document.getElementById('options-list');
        const dimensionError = document.getElementById('dimension-error');
        const optionsError = document.getElementById('options-error');

        function updateOptions() {
            const checkedDimension = document.querySelector('.dimension-radio:checked');
            optionsList.innerHTML = '';
            if (checkedDimension) {
                const options = JSON.parse(checkedDimension.getAttribute('data-options'));
                console.log('Dimension selected, options:', options);
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
                        <label class="form-check-label" for="option-${opt.id}" style="font-family: 'Poppins', sans-serif;">
                            ${opt.style_option_name ?? opt.name}
                        </label>
                    `;
                    optionsList.appendChild(div);
                });
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }

        if (dimensionRadios.length > 0 && optionsContainer && optionsList) {
            dimensionRadios.forEach(radio => {
                radio.addEventListener('change', updateOptions);
                if (radio.checked) {
                    updateOptions();
                }
            });
        }

        // Ensure only one option can be selected
        optionsList.addEventListener('change', (e) => {
            if (e.target.classList.contains('option-radio') && e.target.checked) {
                document.querySelectorAll('.option-radio').forEach(opt => {
                    if (opt !== e.target) opt.checked = false;
                });
            }
        });

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

                const fileInput = document.getElementById('file_upload');
                if (fileInput.files.length > 0) {
                    console.log('Validating files:', fileInput.files.length);
                    for (let i = 0; i < fileInput.files.length; i++) {
                        const fileSize = fileInput.files[i].size / 1024 / 1024; // Size in MB
                        const allowedTypes = [
                            'image/jpeg',
                            'image/png',
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
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
                }

                if (!form.checkValidity() || !isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }
    </script>
@endsection