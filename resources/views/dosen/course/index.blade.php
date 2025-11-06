<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="container py-6">
        <!-- Header Section -->
        <div class="card mb-2 border-0 shadow-sm">
            <div class="card-body p-4 text-center">
            <img src="{{ asset('iot/lms_x.png') }}" class="img-fluid" style="max-height: 457px; object-fit: cover;" alt="LMS X Introduction">
                
            </div>
        </div>

        <!-- Welcome Section -->
          <div class="card mb-2 border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 class="fw-bold text-dark">Welcome, {{ Auth::user()->name }}!</h2>
            <p class="text-muted">Easily manage your courses and monitor student progress.</p>
        </div>
        </div>

        <!-- Course Overview -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Your Courses</h4>
                    <div class="d-flex gap-2 align-items-center">
                        <!-- Category Filter -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                All Categories
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                                <li><a class="dropdown-item" href="#" data-filter="all">All Categories</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach($courses->groupBy('category_id') as $categoryId => $courseGroup)
                                    <li><a class="dropdown-item" href="#" data-filter="{{ $categoryId }}">{{ $courseGroup->first()['category_name'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Search Input -->
                        <input type="text" class="form-control form-control-sm w-auto" id="courseSearch" placeholder="Search courses..." onkeyup="filterCourses()">

                        <!-- Sort Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Sort by Name
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                <li><a class="dropdown-item" href="#" data-sort="name-asc">Name (A to Z)</a></li>
                                <li><a class="dropdown-item" href="#" data-sort="name-desc">Name (Z to A)</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" data-sort="semester-asc">Semester (Low to High)</a></li>
                                <li><a class="dropdown-item" href="#" data-sort="semester-desc">Semester (High to Low)</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" data-sort="students-asc">Students (Low to High)</a></li>
                                <li><a class="dropdown-item" href="#" data-sort="students-desc">Students (High to Low)</a></li>
                            </ul>
                        </div>

                        <!-- View Toggle -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm active view-toggle" data-view="card">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm view-toggle" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- No Courses Message -->
                @if($courses->isEmpty())
                    <div class="alert alert-info text-center">No courses available at the moment.</div>
                @else
                    <!-- Course Cards Container -->
                    <div class="row" id="coursesContainer">
                        @foreach($courses as $course)
                            <div class="col-md-4 mb-3 course-card" 
                                 data-category="{{ $course['category_id'] }}" 
                                 data-name="{{ strtolower($course['full_name']) }}" 
                                 data-semester="{{ $course['semester'] }}"
                                 data-students="{{ $course['student_count'] }}">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="mb-2">
                                            <span class="badge bg-light text-dark">Semester {{ $course['semester'] }}</span>
                                        </div>
                                        <h5 class="fw-bold mb-1">
                                            <a href="{{ route('courses.topics', $course['id']) }}" class="text-decoration-none text-dark">
                                                {{ $course['full_name'] }}
                                            </a>
                                        </h5>
                                        <p class="text-muted small mb-3">Students: {{ $course['student_count'] }}</p>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('courses.topics', $course['id']) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="View Topics">
                                                <i class="fas fa-book-open"></i>
                                            </a>
                                            <a href="" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Manage Students">
                                                <i class="fas fa-users"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Filtering, Sorting, and View Toggle -->
<script>
    // Debounced Filter Function
    let debounceTimeout;
    function filterCourses() {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            const searchInput = document.getElementById('courseSearch').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryDropdown').textContent.trim();
            const cards = document.querySelectorAll('.course-card');

            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                const category = card.getAttribute('data-category');
                const matchesSearch = name.includes(searchInput);
                const matchesCategory = categoryFilter === 'All Categories' || card.getAttribute('data-category') === category;
                card.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
            });
        }, 300);
    }

    // Category Filter
    document.querySelectorAll('[data-filter]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('categoryDropdown').textContent = this.textContent;
            filterCourses();
        });
    });

    // Sort Functionality
    document.querySelectorAll('[data-sort]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const sortValue = this.getAttribute('data-sort');
            const container = document.getElementById('coursesContainer');
            const cards = Array.from(document.querySelectorAll('.course-card'));

            document.getElementById('sortDropdown').textContent = this.textContent;

            cards.sort((a, b) => {
                if (sortValue === 'name-asc') {
                    return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                } else if (sortValue === 'name-desc') {
                    return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
                } else if (sortValue === 'semester-asc') {
                    return a.getAttribute('data-semester') - b.getAttribute('data-semester');
                } else if (sortValue === 'semester-desc') {
                    return b.getAttribute('data-semester') - a.getAttribute('data-semester');
                } else if (sortValue === 'students-asc') {
                    return a.getAttribute('data-students') - b.getAttribute('data-students');
                } else if (sortValue === 'students-desc') {
                    return b.getAttribute('data-students') - a.getAttribute('data-students');
                }
                return 0;
            });

            cards.forEach(card => container.appendChild(card));
        });
    });

    // View Toggle
    document.querySelectorAll('.view-toggle').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.view-toggle').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const coursesContainer = document.getElementById('coursesContainer');
            const cards = document.querySelectorAll('.course-card');

            if (this.getAttribute('data-view') === 'list') {
                coursesContainer.classList.add('list-view');
                cards.forEach(card => {
                    card.classList.remove('col-md-4');
                    card.classList.add('col-12');
                });
            } else {
                coursesContainer.classList.remove('list-view');
                cards.forEach(card => {
                    card.classList.add('col-md-4');
                    card.classList.remove('col-12');
                });
            }
        });
    });
</script>

<!-- CSS for Styling -->
<style>
    .content {
        background-color: #f5f5f5;
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1) !important;
    }
    .btn-outline-secondary {
        border-color: #e0e0e0;
        color: #333;
    }
    .btn-outline-secondary:hover {
        background-color: #f0f0f0;
    }
    .btn-outline-primary {
        border-color: #007bff;
        color: #007bff;
    }
    .btn-outline-primary:hover {
        background-color: #e6f0ff;
    }
    .list-view .card {
        flex-direction: row;
        align-items: center;
    }
    .list-view .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 100%;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    .container {
        max-width: 1200px;
    }
</style>