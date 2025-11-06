

<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
    <!-- Brand -->
    <div class="brand flex-column-auto mt-5" id="kt_brand">
        <a href="{{ auth()->user()->id_role == 3 ? route('home') : route('dashboard') }}" class="brand-logo d-flex align-items-center">
            <img alt="Logo" src="{{ asset('metch/media/logos/lmsx2.png') }}" class="mr-2" style="max-height:80px; width:auto;" />
        </a>
        <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
            <span class="svg-icon svg-icon-xl">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M5.293 6.707a1 1 0 011.414-1.414l5.414 5.414 5.414-5.414a1 1 0 111.414 1.414l-6.121 6.121-6.121-6.121z" fill="#000"/>
                    </g>
                </svg>
            </span>
        </button>
    </div>

    <!-- Menu -->
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
        <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
            <ul class="menu-nav">
                <style>
                    .menu-item .menu-link:hover { background-color: #f0f3ff; transition:0.3s;}
                    .menu-item .menu-link:hover .menu-icon i, .menu-item .menu-link:hover .menu-text { color:#3699ff; transition:0.3s;}
                    .menu-item.menu-item-active .menu-link { background-color:#f0f3ff;}
                    .menu-item.menu-item-active .menu-icon i, .menu-item.menu-item-active .menu-text { color:#3699ff; font-weight:500;}
                </style>

                @switch(auth()->user()->id_role)

                    {{-- Admin --}}
                    @case(1)
                        <li class="menu-item {{ Request::is('admin/dashboard') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                                <span class="menu-text"><b>Admin Dashboard</b></span>
                            </a>
                        </li>
                        <li class="menu-section"><h4 class="menu-text">Admin Navigasi</h4></li>
                        <li class="menu-item {{ Request::is('admin/courses*') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('admin.courses.index') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-graduation-cap"></i></span>
                                <span class="menu-text">View Course</span>
                            </a>
                        </li>

                        <li class="menu-item {{ Request::is('admin/courses*') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('courses.management') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-book"></i></span>
                                <span class="menu-text">Manage Courses</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/users*') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('users.index') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-users"></i></span>
                                <span class="menu-text">Manage Users</span>
                                <span class="menu-label"><span class="label label-rounded label-primary">{{ \App\Models\User::count() }}</span></span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/lom-logs*') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('admin.lom-logs.index') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-shoe-prints"></i></span>
                                <span class="menu-text">Manage Logs</span>
                            </a>
                        </li>
                    @break

                    {{-- Teacher --}}
                    @case(2)
                        <li class="menu-item {{ Request::is('teacher/dashboard') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('dosen.dashboard') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                                <span class="menu-text">Teacher Dashboard</span>
                            </a>
                        </li>
                        <li class="menu-section"><h4 class="menu-text">Teacher Navigasi</h4></li>
                        <li class="menu-item {{ Request::is('teacher/courses*') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('dosen.courses.index') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-book"></i></span>
                                <span class="menu-text">My Courses</span>
                            </a>
                        </li>

                        {{-- Active Courses Submenu --}}
                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-icon"><i class="fas fa-book-open"></i></span>
                                <span class="menu-text">Active Courses</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="menu-submenu">
                                <ul class="menu-subnav">
                                    @forelse($courses as $course)
                                        <li class="menu-item">
                                            <a href="{{ route('courses.topics', $course->id) }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-line"><span></span></i>
                                                <span class="menu-text">{{ $course->full_name }}</span>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="menu-item">
                                            <span class="menu-link"><span class="menu-text">No active courses</span></span>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </li>

                        {{-- My Students Submenu --}}
                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-icon"><i class="fas fa-users"></i></span>
                                <span class="menu-text">My Students</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="menu-submenu">
                                <ul class="menu-subnav">
                                    @php
                                        $allStudents = $courses->flatMap(fn($c) => $c->users)
                                                               ->where('id_role',3)
                                                               ->unique('id');
                                    @endphp
                                    @forelse($allStudents as $student)
                                        <li class="menu-item">
                                            <span class="menu-link">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                <span class="menu-text">{{ $student->name }}</span>
                                            </span>
                                        </li>
                                    @empty
                                        <li class="menu-item">
                                            <span class="menu-link"><span class="menu-text">No students found</span></span>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </li>
                    @break

                    {{-- Student --}}
                    @case(3)
                        <li class="menu-item {{ Request::is('student/home') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('home') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                                <span class="menu-text">Student Dashboard</span>
                            </a>
                        </li>
                        <li class="menu-section"><h4 class="menu-text">Student Navigasi</h4></li>
                        <li class="menu-item {{ Request::is('student/courses*') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('student.course.index') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-book"></i></span>
                                <span class="menu-text">Available Courses</span>
                            </a>
                        </li>
                        {{-- Enrolled Courses --}}
                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-icon"><i class="fas fa-book-reader"></i></span>
                                <span class="menu-text">My Enrolled Courses</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="menu-submenu">
                                <ul class="menu-subnav">
                                    @forelse(auth()->user()->courses as $course)
                                        <li class="menu-item">
                                            <a href="{{ route('student.topic.index', $course->id) }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                <span class="menu-text">{{ $course->full_name }}</span>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="menu-item">
                                            <span class="menu-link"><span class="menu-text">No courses enrolled</span></span>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </li>
                        <li class="menu-item {{ Request::is('student/profile*') ? 'menu-item-active' : '' }}">
                            <a href="{{ route('profile') }}" class="menu-link">
                                <span class="menu-icon"><i class="fas fa-user"></i></span>
                                <span class="menu-text">My Profile</span>
                            </a>
                        </li>
                    @break

                @endswitch
            </ul>
        </div>
    </div>
</div>
