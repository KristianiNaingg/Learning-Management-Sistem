@extends('layouts.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Duplikasi Forum {{ $subTopic->title }}</h4>
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

                <form action="{{ route('forums.storeDuplicate', $forum->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="sub_topic_id" value="{{ $subTopic->id }}">
                    <input type="hidden" name="original_forum_id" value="{{ $forum->id }}">

                    <div class="form-group mb-4">
                        <label for="name" class="form-label fw-bold">Nama Forum:</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $forum->name . ' (Salinan)') }}" required>
                        <div class="invalid-feedback">Nama wajib diisi dan harus unik di subtopic ini.</div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="description" class="form-label fw-bold">Deskripsi Forum:</label>
                        <textarea name="description" id="description" class="form-control" rows="6" required>{{ old('description', $forum->description . ' (Salinan)') }}</textarea>
                        <div class="invalid-feedback">Deskripsi wajib diisi dan harus unik di subtopic ini.</div>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label fw-bold">Gaya Belajar:</label>
                        <div id="dimension-container">
                            @php
                                $selectedDimension = $dimensions->firstWhere('id', 3);
                                $selectedOptionId = old('dimension_options', $forum->options->pluck('id')->first());
                            @endphp
                            @if($selectedDimension)
                                <input type="hidden" name="dimension" value="{{ $selectedDimension->id }}">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        {{ $selectedDimension->dimension }}
                                    </label>
                                </div>
                                <div id="options-container">
                                    <label class="form-label">Pilih Opsi Dimensi</label>
                                    <div id="options-list">
                                        @foreach($selectedDimension->options as $option)
                                            <div class="form-check">
                                                <input class="form-check-input option-radio" type="radio"
                                                       name="dimension_options"
                                                       value="{{ $option->id }}"
                                                       id="option-{{ $option->id }}"
                                                       {{ $selectedOptionId == $option->id ? 'checked' : '' }}
                                                       required>
                                                <label class="form-check-label" for="option-{{ $option->id }}">
                                                    {{ $option->nama_opsi_dimensi }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="invalid-feedback d-block" id="options-error" style="display: none;">
                                        Pilih satu opsi dimensi.
                                    </div>
                                    @error('dimension_options')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <p>Dimensi dengan ID: 3 tidak ditemukan.</p>
                            @endif
                        </div>
                    </div>

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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // TinyMCE Initialization
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#description',
                plugins: [
                    'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
                    'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
                ],
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                tinycomments_mode: 'embedded',
                tinycomments_author: 'Author name',
                mergetags_list: [
                    { value: 'First.Name', title: 'First Name' },
                    { value: 'Email', title: 'Email' },
                ],
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

        // Form validation
        const form = document.querySelector('.needs-validation');
        if (form) {
            form.addEventListener('submit', function (event) {
                const checkedOption = document.querySelector('.option-radio:checked');
                const optionsError = document.getElementById('options-error');

                if (!form.checkValidity() || !checkedOption) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (!checkedOption) {
                        optionsError.style.display = 'block';
                    } else {
                        optionsError.style.display = 'none';
                    }
                }
                form.classList.add('was-validated');
            }, false);
        }
    });
</script>
@endsection
