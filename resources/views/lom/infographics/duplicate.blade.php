@extends('layouts.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Duplikasi Infografis {{ $subTopic->title }}</h4>
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

                <form action="{{ route('infografis.storeDuplicate', $infografis->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="sub_topic_id" value="{{ $subTopic->id }}">
                    <input type="hidden" name="original_infografis_id" value="{{ $infografis->id }}">

                    <div class="form-group mb-4">
                        <label for="file_upload" class="form-label fw-bold">File Infografis:</label>
                        <input type="file" name="file_upload" id="file_upload" class="form-control" required>
                        <small class="text-muted">Unggah file baru (mp4, jpg, jpeg, png, max 5MB).</small>
                        <div class="invalid-feedback">File infografis wajib diunggah.</div>
                        @error('file_upload')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label fw-bold">Gaya Belajar:</label>
                        <div id="dimension-container">
                            @php
                                $selectedDimension = $dimensions->firstWhere('id', 3);
                                $selectedOptionId = old('dimension_options', $infografis->options->pluck('id')->first());
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
