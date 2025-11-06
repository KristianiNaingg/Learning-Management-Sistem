<div class="content d-flex flex-column flex-column-fluid py-5" id="kt_content">
    <div class="container-lg">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-5">
            <ol class="breadcrumb bg-white p-3 rounded shadow-sm border">
                <li class="breadcrumb-item">
                    <a href="{{ route('courses.topics', $course->id) }}" class="text-dark text-decoration-none fw-semibold">
                        {{ $course->full_name }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('topics.show', [$course->id, $topic->id]) }}" class="text-dark text-decoration-none fw-semibold">
                        {{ $topic->title }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('topics.show', [$course->id, $topic->id]) }}" class="text-dark text-decoration-none fw-semibold">
                        {{ $subtopic->title }}
                    </a>
                </li>
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

        <!-- Page Information -->
        <div class="card mb-5 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-4">
                <h2 class="h3 fw-bold mb-2">{{ $page->name }}</h2>
            </div>
            <div class="card-body py-5">
                @if ($page->description)
                    <h5 class="fw-bold mb-3 text-dark">Deskripsi</h5>
                    <p class="text-muted">{!! nl2br(e($page->description)) !!}</p>
                @endif

                @if ($page->content)
                    <div class="card-text content-wrapper">
                        {!! $page->content !!}
                    </div>
                @else
                    <h5 class="fw-bold mb-3 text-dark">Konten</h5>
                    <p class="text-muted">Tidak ada konten tersedia untuk halaman ini.</p>
                @endif
            </div>
        </div>

        <!-- Subtopic Information -->
        <div class="card mb-5 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-4">
                <h5 class="fw-bold mb-0">Subtopik: {{ $subtopic->title }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-2">
                    Bagian dari: 
                    <a href="{{ route('topics.show', [$course->id, $topic->id]) }}" class="text-primary">
                        {{ $topic->title }}
                    </a>
                </p>
                <p class="text-muted">Mata Kuliah: {{ $course->full_name }}</p>
            </div>
        </div>

        <!-- Navigation Button -->
        <div class="mt-5">
            <a href="{{ route('topics.show', [$course->id, $topic->id]) }}" 
               class="btn btn-outline-secondary px-4 py-2 transition-all hover:shadow">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Topik
            </a>
        </div>
    </div>
</div>

{{-- ====================================================== --}}
{{-- STYLES --}}
{{-- ====================================================== --}}
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
    .content-wrapper img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        display: block;
        margin: 1rem 0;
    }
    .content-wrapper img[src*="blogger.googleusercontent.com"] {
        display: block;
        margin: 1rem 0;
    }
    .content-wrapper img:not([src]) {
        display: none;
    }
    @media (max-width: 576px) {
        .container-lg {
            padding: 0 1rem;
        }
        .card-body {
            padding: 1.5rem;
        }
    }
</style>
@endsection

{{-- ====================================================== --}}
{{-- SCRIPTS --}}
{{-- ====================================================== --}}
@section('scripts')
<script>
    // Fallback untuk gambar rusak
    document.querySelectorAll('.content-wrapper img').forEach(img => {
        img.onerror = function() {
            this.src = '/images/fallback-image.jpg';
            this.alt = 'Gambar tidak dapat dimuat';
        };
    });
</script>

{{-- ========================================================== --}}

{{-- LOG DURASI AKSES — HANYA UNTUK MAHASISWA (role: 3) --}}
{{-- ========================================================== --}}
 <script>
@auth
@if(auth()->user()->id_role === 3)
document.addEventListener("DOMContentLoaded", function () {
    let startTime = Date.now();

    // Fungsi kirim durasi
    function sendDuration() {
        let duration = Math.round((Date.now() - startTime) / 1000);
        if (duration <= 0) return;

        console.log("Durasi dikirim:", duration + " detik");

        const formData = new FormData();
        formData.append("lom_id", "{{ $page->id }}");
        formData.append("lom_type", "page");
        formData.append("duration", duration);
        formData.append("_token", "{{ csrf_token() }}");

        const url = "{{ route('update.duration') }}";

        const sent = navigator.sendBeacon(url, formData);
        console.log("Beacon terkirim?", sent);

        if (!sent) {
            // Fallback kalau sendBeacon gagal
            fetch(url, {
                method: "POST",
                body: formData,
                keepalive: true
            }).then(() => console.log("Fallback fetch terkirim"));
        }
    }

    // Trigger kirim saat tab tidak lagi terlihat
    document.addEventListener("visibilitychange", function () {
        if (document.visibilityState === "hidden") {
            sendDuration();
        }
    });

    // Backup: kirim juga sebelum tab ditutup
    window.addEventListener("beforeunload", sendDuration);
});
@endif
@endauth
</script>

@endsection