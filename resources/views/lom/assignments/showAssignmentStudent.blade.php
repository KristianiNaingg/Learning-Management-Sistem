
<div class="content d-flex flex-column flex-column-fluid py-5" id="kt_content">
    <div class="container-lg">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-5">
            <ol class="breadcrumb bg-white p-3 rounded shadow-sm border">
                <li class="breadcrumb-item"><a href="{{ route('courses.topics', $course->id) }}" class="text-dark text-decoration-none fw-semibold">{{ $course->full_name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('topics.show', [$course->id, $topic->id]) }}" class="text-dark text-decoration-none fw-semibold">{{ $topic->title }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('topics.show', [$course->id, $topic->id]) }}" class="text-dark text-decoration-none fw-semibold">{{ $subTopic->title }}</a></li>
                <li class="breadcrumb-item active fw-bold" aria-current="page">{{ $assignment->name }}</li>
            </ol>
        </nav>

        <!-- Pesan Error atau Sukses -->
        @if (session('errors'))
            <div class="alert alert-danger alert-dismissible fade show rounded" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('errors')->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Assignment Header -->
        <div class="card mb-5 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-4">
                <h2 class="h3 fw-bold mb-2">{{ $assignment->name }}</h2>
                <div class="d-flex flex-wrap gap-3 text-muted fs-6">
                    <div>
                        <span class="fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Created:</span>
                        {{ $assignment->created_at->format('d/m/Y h:i A') }}
                    </div>
                    <div>
                        <span class="fw-semibold"><i class="fas fa-clock me-1"></i>Due Date:</span>
                        {{ $assignment->due_date ? $assignment->due_date->format('d/m/Y h:i A') : 'No due date' }}
                    </div>
                </div>
            </div>

            <div class="card-body py-5">
                <!-- Assignment Description -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-dark">Description</h5>
                        <p class="text-muted">{!! $assignment->description !!}</p>
                        @if($assignment->content && Storage::disk('public')->exists(str_replace('storage/', '', $assignment->content)))
                            <div class="bg-light p-4 rounded">
                                <a href="{{ Storage::url($assignment->content) }}"
                                   class="text-decoration-none text-primary fw-semibold"
                                   download
                                   title="Download {{ basename($assignment->content) }}">
                                    <i class="fas fa-file-download me-2"></i>{{ basename($assignment->content) }}
                                </a>
                            </div>
                        @elseif($assignment->content)
                            <div class="bg-light p-4 rounded text-muted">
                                File tugas tidak ditemukan.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submission Status -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-dark">Submission Status</h5>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="w-25 fw-semibold text-dark">Submission Status</th>
                                        <td>
                                            <span class="badge {{ $submission ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }} px-3 py-2">
                                                {{ $submission ? 'Submitted' : 'No attempt' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="fw-semibold text-dark">Grading Status</th>
                                        <td>
                                            @if($grade)
                                                <span>Graded: <strong class="text-success">{{ $grade->grade }}</strong></span>
                                            @else
                                                <span class="text-muted">Not graded yet</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="fw-semibold text-dark">Time Remaining</th>
                                        <td>
                                            @if($assignment->due_date)
                                                @php
                                                    $now = \Carbon\Carbon::now();
                                                    $dueDate = \Carbon\Carbon::parse($assignment->due_date);
                                                    $diffInSeconds = $dueDate->diffInSeconds($now, false);

                                                    if ($diffInSeconds < 0) {
                                                        $overdue = $now->diff($dueDate);
                                                        $overdueText = '';
                                                        if ($overdue->d > 0) $overdueText .= $overdue->d . ' days ';
                                                        if ($overdue->h > 0) $overdueText .= $overdue->h . ' hours ';
                                                        if ($overdue->i > 0) $overdueText .= $overdue->i . ' mins';
                                                        $remaining = '<span class="text-danger fw-semibold">Overdue by ' . ($overdueText ?: 'less than 1 minute') . '</span>';
                                                    } else {
                                                        $remainingTime = $now->diff($dueDate);
                                                        $remaining = '';
                                                        if ($remainingTime->d > 0) $remaining .= $remainingTime->d . ' days ';
                                                        if ($remainingTime->h > 0) $remaining .= $remainingTime->h . ' hours ';
                                                        if ($remainingTime->i > 0) $remaining .= $remainingTime->i . ' mins';
                                                        $remaining = $remaining ?: 'Less than 1 minute';
                                                    }
                                                @endphp
                                                {!! $remaining !!}
                                            @else
                                                No due date
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="fw-semibold text-dark">Submission Comments</th>
                                        <td>
                                            @if($grade)
                                                <span class="fw-semibold">{{ $grade->feedback }}</span>
                                            @else
                                                <span class="text-muted">Not graded yet</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Submission Form -->
                @if(!$submission || $assignment->allow_resubmission)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4 text-dark">Submit Your Assignment</h5>
                            <form action="{{ route('student.assignment.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="assign_id" value="{{ $assignment->id }}">
                                <div class="mb-4">
                                    <label for="file_path" class="form-label fw-semibold text-dark">Upload Files</label>
                                    <input
                                        class="form-control border-light-subtle"
                                        type="file"
                                        id="file_path"
                                        name="file_path[]"
                                        accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.bmp"
                                        multiple
                                        required
                                        aria-describedby="file_path_help"
                                    >
                                    <div id="file_path_help" class="form-text text-muted">
                                        Maximum file size per file: 50MB. Accepted formats: PDF, DOC, DOCX, PPT, PPTX, JPG, PNG, GIF, BMP
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="confirm_submission" name="confirm_submission" required aria-describedby="confirm_submission_help">
                                        <label class="form-check-label text-dark" for="confirm_submission">
                                            I confirm this is my original work
                                        </label>
                                        <div id="confirm_submission_help" class="form-text text-muted">Please confirm before submitting.</div>
                                    </div>

                                    <button type="submit" class="btn btn-primary px-5 py-2 transition-all hover:shadow">
                                        <i class="fas fa-upload me-2"></i> Submit Assignment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="alert alert-info alert-dismissible fade show rounded" role="alert">
                                <i class="fas fa-info-circle me-2"></i> You have already submitted this assignment.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                                @if($submission && $submission->files->isNotEmpty())
                                    <div class="mt-3">
                                        <strong class="text-dark">Uploaded Files:</strong>
                                        <ul class="list-unstyled mt-2">
                                            @foreach($submission->files as $path)
                                                @php
                                                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                                                    $isPreviewable = in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp']);
                                                    $cleanPath = str_replace('storage/', '', $path);
                                                @endphp
                                                @if(Storage::disk('public')->exists($cleanPath))
                                                    <li class="mb-2">
                                                        <a href="{{ Storage::url($path) }}"
                                                           class="btn btn-sm btn-outline-primary transition-all hover:shadow"
                                                           {{ $isPreviewable ? 'target="_blank"' : 'download' }}
                                                           title="{{ $isPreviewable ? 'Open ' : 'Download ' }}{{ basename($path) }}">
                                                            <i class="fas {{ $isPreviewable ? 'fa-eye' : 'fa-file-download' }} me-2"></i>
                                                            {{ basename($path) }}
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="mb-2 text-muted">
                                                        {{ basename($path) }} (File tidak ditemukan)
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Cancel Submission -->
                                <form action="" method="POST" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="submission_id" value="{{ $submission->id }}">
                                    <button type="submit" class="btn btn-outline-danger btn-sm transition-all hover:shadow" onclick="return confirm('Apakah Anda yakin ingin membatalkan pengumpulan?');">
                                        <i class="fas fa-times me-2"></i> Batalkan Pengumpulan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tombol Navigasi -->
        <div class="mt-5">
            <a href="{{ route('topics.show', [$course->id, $topic->id]) }}" class="btn btn-outline-secondary px-4 py-2 transition-all hover:shadow">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Bagian
            </a>
        </div>
    </div>
</div>

@section('styles')
<style>
    .content {
        background-color: #f4f6f9;
        min-height: 100vh;
    }
    .card {
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }
    .card-header {
        background-color: transparent;
        border-bottom: none;
        padding: 1.5rem 2rem;
    }
    .card-body {
        padding: 2rem;
    }
    .table {
        border-spacing: 0 0.5rem;
        border-collapse: separate;
    }
    .table th {
        font-weight: 600;
        color: #1f2a44;
    }
    .table td, .table th {
        padding: 1rem;
        vertical-align: middle;
    }
    .breadcrumb {
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
    }
    .alert {
        border-radius: 0.75rem;
        padding: 1.25rem;
        border: 1px solid transparent;
    }
    .btn {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    .btn:hover, a.text-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .form-control, .form-check-input {
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    @media (max-width: 576px) {
        .container-lg {
            padding: 0 1rem;
        }
        .d-flex.flex-wrap.gap-3 {
            flex-direction: column;
            gap: 0.75rem;
        }
        .card-body {
            padding: 1.5rem;
        }
    }
</style>
@endsection
