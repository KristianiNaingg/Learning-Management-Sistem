<!-- ✅ CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<div class="content d-flex flex-column flex-column-fluid py-5 mt-2" id="kt_content">
    <div class="container-lg">

        <!-- ✅ Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h4 class="fw-bold text-dark mb-0">Course Overview</h4>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Filter Category -->
                <select id="filterCategory" class="form-select form-select-sm rounded-3 shadow-sm" style="width: 160px;">
                    <option value="all" selected>All Categories</option>
                    <option value="tech">Technology</option>
                    <option value="design">Design</option>
                    <option value="business">Business</option>
                </select>

                <!-- Search -->
                <input type="text" id="searchCourse" class="form-control form-control-sm rounded-3 shadow-sm"
                       placeholder="Search courses..." style="width: 240px;">

                <!-- Sort -->
                <select id="sortCourse" class="form-select form-select-sm rounded-3 shadow-sm" style="width: 180px;">
                    <option value="name">Sort by course name</option>
                    <option value="participants">Sort by participants</option>
                </select>

                <!-- Toggle View -->
                <div class="btn-group btn-group-sm shadow-sm" role="group">
                    <button id="cardViewBtn" class="btn btn-light active">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i> Card
                    </button>
                    <button id="listViewBtn" class="btn btn-light">
                        <i class="bi bi-list-ul me-1"></i> List
                    </button>
                </div>
            </div>
        </div>

        <!-- ✅ Enroll Courses -->
       
        <div id="joinedCoursesContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
            @forelse ($joinedCourses as $course)
                <div class="col course-item" 
                     data-category="tech" 
                     data-name="{{ strtolower($course->full_name) }}" 
                     data-participants="{{ $course->users->count() }}">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <img src="{{ $course->course_image ? asset('storage/' . $course->course_image) : 'https://via.placeholder.com/300x180?text=No+Image' }}"
                             class="card-img-top" style="height: 180px; object-fit: cover;" alt="Course Image">

                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="fw-bold text-dark">{{ Str::limit($course->full_name, 50) }}</h5>
                            <p class="text-muted small mb-3">Semester {{ $course->semester }}</p>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-primary border">
                                    <i class="bi bi-people-fill me-1"></i>{{ $course->users->count() }}
                                </span>

                                <a href="{{ route('student.topic.index', $course->id) }}"
                                   class="btn btn-outline-primary btn-sm rounded-pill px-3">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">You haven't joined any courses yet.</p>
            @endforelse
        </div>

        <!-- ✅ Not Joined Courses -->
        <h5 class="fw-semibold mb-3 text-secondary">Available Courses</h5>
        <div id="notJoinedCoursesContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse ($notJoinedCourses as $course)
                <div class="col course-item" 
                     data-category="tech" 
                     data-name="{{ strtolower($course->full_name) }}" 
                     data-participants="{{ $course->users->count() }}">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <img src="{{ $course->course_image ? asset('storage/' . $course->course_image) : 'https://via.placeholder.com/300x180?text=No+Image' }}"
                             class="card-img-top" style="height: 180px; object-fit: cover;" alt="Course Image">

                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="fw-bold text-dark">{{ Str::limit($course->full_name, 50) }}</h5>
                            <p class="text-muted small mb-3">Semester {{ $course->semester }}</p>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-primary border">
                                    <i class="bi bi-people-fill me-1"></i>{{ $course->users->count() }}
                                </span>

                                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 not-enrolled">View</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">All courses have been joined.</p>
            @endforelse
        </div>

    </div>
</div>

<!-- ✅ JS -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchCourse');
    const filterSelect = document.getElementById('filterCategory');
    const sortSelect = document.getElementById('sortCourse');
    const cardBtn = document.getElementById('cardViewBtn');
    const listBtn = document.getElementById('listViewBtn');

    const allCourses = Array.from(document.querySelectorAll('.course-item'));
    const allContainers = [document.getElementById('joinedCoursesContainer'), document.getElementById('notJoinedCoursesContainer')];

    // 🔍 Search
    searchInput.addEventListener('input', () => {
        const keyword = searchInput.value.toLowerCase();
        allCourses.forEach(card => {
            const name = card.dataset.name;
            card.style.display = name.includes(keyword) ? '' : 'none';
        });
    });

    // 🏷️ Filter
    filterSelect.addEventListener('change', () => {
        const selected = filterSelect.value;
        allCourses.forEach(card => {
            card.style.display = (selected === 'all' || card.dataset.category === selected) ? '' : 'none';
        });
    });

    // 🔢 Sort
    sortSelect.addEventListener('change', () => {
        allContainers.forEach(container => {
            const sorted = [...container.children].sort((a, b) => {
                if (sortSelect.value === 'participants') {
                    return b.dataset.participants - a.dataset.participants;
                }
                return a.dataset.name.localeCompare(b.dataset.name);
            });
            container.innerHTML = '';
            sorted.forEach(c => container.appendChild(c));
        });
    });

    // 🧩 Toggle View
    cardBtn.addEventListener('click', () => {
        cardBtn.classList.add('active');
        listBtn.classList.remove('active');
        allContainers.forEach(c => c.classList.remove('list-view'));
    });

    listBtn.addEventListener('click', () => {
        listBtn.classList.add('active');
        cardBtn.classList.remove('active');
        allContainers.forEach(c => c.classList.add('list-view'));
    });

    // ⚠️ Alert
    document.querySelectorAll('.not-enrolled').forEach(btn => {
        btn.addEventListener('click', () => alert("⚠️ You haven’t enrolled in this class!"));
    });
});
</script>

<!-- ✅ Optional CSS for List View -->
<style>
.list-view .col {
    flex: 0 0 100%;
}
.list-view .card {
    flex-direction: row;
    height: 150px;
}
.list-view .card img {
    width: 200px;
    height: 150px;
    object-fit: cover;
}
.list-view .card-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.btn-group .btn.active {
    background-color: #e9ecef;
    font-weight: 600;
}
</style>
