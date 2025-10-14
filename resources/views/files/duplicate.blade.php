@extends('layouts.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Duplikasi File {{ $subTopic->title }}</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success mb-4 rounded" style="font-family: 'Poppins', sans-serif;">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger mb-4 rounded" style="font-family: 'Poppins', sans-serif;">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('files.storeDuplicate', $file->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="sub_topic_id" value="{{ $subTopic->id }}">
                    <input type="hidden" name="original_file_id" value="{{ $file->id }}">

                    <div class="form-group mb-4">
                        <label for="name" class="form-label fw-bold">Nama File:</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $file->name . ' (Salinan)') }}" required>
                        <div class="invalid-feedback">Nama file wajib diisi dan harus unik di subtopic ini.</div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="description" class="form-label fw-bold">Deskripsi File:</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $file->description . ' (Salinan)') }}</textarea>
                        <div class="invalid-feedback">Deskripsi harus unik di subtopic ini.</div>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="file_upload" class="form-label fw-bold">File Upload:</label>
                        <input type="file" name="file_upload" id="file_upload" class="form-control" required>
                        <small class="text-muted">Unggah file baru (pdf, doc, docx, ppt, pptx, jpg, png, zip, rar, max 5MB).</small>
                        <div class="invalid-feedback">File wajib diunggah.</div>
                        @error('file_upload')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Learning Style (Dimensions 2 or 3) -->
                    @php
                        $selectedDimensions = $dimensions->whereIn('id', [2, 3]);
                        $selectedOptionId = old('dimension_options', $file->options->pluck('id')->first());
                        $selectedDimensionId = old('dimension', $file->options->pluck('pivot.dimension_id')->first());
                    @endphp
                    @if($selectedDimensions->isNotEmpty())
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Dimensi Learning Style:</label>
                            <div id="dimension-container">
                                @foreach($selectedDimensions as $dimension)
                                    <div class="form-check">
                                        <input class="form-check-input dimension-radio" type="radio"
                                               name="dimension"
                                               value="{{ $dimension->id }}"
                                               id="dimension-{{ $dimension->id }}"
                                               data-options='@json($dimension->options)'
                                               {{ $selectedDimensionId == $dimension->id ? 'checked' : '' }}
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
                        <div class="form-group mb-4" id="options-container" style="display: none;">
                            <label class="form-label fw-bold">Pilih Opsi:</label>
                            <div id="options-list"></div>
                            <div class="invalid-feedback d-block" id="options-error" style="display: none;">
                                Pilih satu opsi dimensi.
                            </div>
                            @error('dimension_options')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="alert alert-warning">Dimensi dengan ID 2 atau 3 tidak ditemukan.</div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('sections.show', [$subTopic->section->course_id, $subTopic->section->id]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                        <div>
                            <button type="submit" class="btn btn-success" name="action" value="save_display">
                                <i class="fas fa-save me-2"></i> Simpan dan Tampilkan
                            </button>
                            <button type="submit" class="btn btn-primary" name="action" value="save_return">
                                <i class="fas fa-save me-2"></i> Simpan dan Kembali ke Kursus
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // TinyMCE Initialization
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#description',
                plugins: [
                    'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount emoticons',
                    'checklist mediaembed casechange formatpainter pageembed a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss markdown'
                ],
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | removeformat',
                menubar: false,
                height: 200,
                setup: function (editor) {
                    editor.on('init', function () {
                        console.log('TinyMCE berhasil diinisialisasi untuk #description');
                    });
                    editor.on('error', function (e) {
                        console.error('TinyMCE error:', e);
                    });
                }
            });
        } else {
            console.error('TinyMCE tidak dimuat!');
        }

        // Dimension and Options Handling
        const dimensionRadios = document.querySelectorAll('.dimension-radio');
        const optionsContainer = document.getElementById('options-container');
        const optionsList = document.getElementById('options-list');
        const selectedOptionId = '{{ $selectedOptionId }}';

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
                                       name="dimension_options"
                                       value="${opt.id}"
                                       id="option-${opt.id}"
                                       ${opt.id == selectedOptionId || opt.id == '{{ old("dimension_options") }}' ? 'checked' : ''}
                                       required>
                                <label class="form-check-label" for="option-${opt.id}">
                                    ${opt.nama_opsi_dimensi}
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
                const dimensionError = document.getElementById('dimension-error');
                const optionsError = document.getElementById('options-error');

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

                if (!form.checkValidity() || !isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }
    });
</script>
@endsection
