<div class="content flex flex-col min-h-screen bg-gray-50" id="kt_content">
    <div class="container max-w-6xl mx-auto px-4 py-10">
        <!-- Course Information Card -->
        <div class="card mb-8 border-0 shadow-md rounded-2xl overflow-hidden animate__animated animate__fadeIn">
            <div class="card-header bg-gradient-to-r from-teal-500 to-teal-600 text-white py-5 px-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold tracking-tight text-primary"><a href="{{ route('student.topic.index', $course->id) }}">{{ $course->full_name }}</a></h1>
                    <span class="badge bg-teal-100 text-teal-800 font-medium px-4 py-2 rounded-full">{{ $course->code ?? '' }}</span>
                </div>
            </div>
            <div class="card-body bg-white p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-teal-50 p-5 rounded-xl shadow-sm transition hover:shadow-md">
                        <h5 class="text-teal-600 font-semibold mb-4 flex items-center text-lg">
                            <i class="fas fa-info-circle mr-3"></i>Course Summary
                        </h5>
                        <p class="text-gray-700 leading-relaxed">{{ $course->summary }}</p>
                    </div>
                    <div class="bg-teal-50 p-5 rounded-xl shadow-sm transition hover:shadow-md">
                        <h5 class="text-teal-600 font-semibold mb-4 flex items-center text-lg">
                            <i class="fas fa-bullseye mr-3"></i>CPMK
                        </h5>
                        <p class="text-gray-700 leading-relaxed">{{ $course->cpmk }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Topic Information Card -->
        <div class="card mb-8 border-0 shadow-md rounded-2xl overflow-hidden animate__animated animate__fadeIn animate__delay-1s">
            <div class="card-header bg-gradient-to-r from-blue-500 to-blue-600 text-white py-5 px-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold tracking-tight">
                        <i class="fas fa-book-open mr-3"></i>{{ $topic->title ?? 'No Topic Title' }}
                    </h2>
                   
                </div>
            </div>
            <div class="card-body bg-white p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-blue-50 p-5 rounded-xl shadow-sm transition hover:shadow-md">
                        <h5 class="text-blue-600 font-semibold mb-4 flex items-center text-lg">
                            <i class="fas fa-align-left mr-3"></i>Description
                        </h5>
                        <p class="text-gray-700 leading-relaxed">{{ $topic->description ?? 'No description' }}</p>
                    </div>
                    <div class="bg-blue-50 p-5 rounded-xl shadow-sm transition hover:shadow-md">
                        <h5 class="text-blue-600 font-semibold mb-4 flex items-center text-lg">
                            <i class="fas fa-bullseye mr-3"></i>Sub-CPMK
                        </h5>
                        <p class="text-gray-700 leading-relaxed">{{ $topic->sub_cpmk ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

       <!-- Subtopics -->
@if(optional($topic->subtopics)?->count())
    <div class="card mb-8 border-0 shadow-md rounded-2xl overflow-hidden animate__animated animate__fadeIn animate__delay-2s">
        <div class="card-header bg-white p-6 border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-layer-group mr-3"></i>Subtopics
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="accordion accordion-flush" id="subtopicsAccordion">
                @foreach($topic->subtopics as $index => $subTopic)
                    <div class="accordion-item border-0 mb-4" data-subtopic-id="{{ $subTopic->id }}">
                        <div class="accordion-header bg-white rounded-xl shadow-sm">
                            <button class="accordion-button bg-gray-50 hover:bg-gray-100 {{ $index == 0 ? '' : 'collapsed' }} py-4 px-6 text-left"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#subtopicCollapse{{ $subTopic->id }}"
                                    aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                    aria-controls="subtopicCollapse{{ $subTopic->id }}">
                                <h5 class="text-lg font-semibold text-gray-800">
                                    Sub Materi {{ $index + 1 }}: {{ $subTopic->title ?? 'No Title' }}
                                </h5>
                            </button>
                        </div>
                        <div id="subtopicCollapse{{ $subTopic->id }}"
                             class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                             aria-labelledby="subtopicHeading{{ $subTopic->id }}"
                             data-bs-parent="#subtopicsAccordion">
                            <div class="accordion-body p-6">
                                <!-- Subtopic Content -->
                                <div class="mb-6 p-5 bg-gray-50 rounded-xl shadow-sm">
                                    <h6 class="text-gray-600 font-medium mb-4 flex items-center">
                                        <i class="fas fa-info-circle mr-3"></i>Content
                                    </h6>
                                    <div class="p-5 bg-white rounded-lg shadow-sm">
                                        {!! nl2br(e($subTopic->content ?? 'No Content')) !!}
                                    </div>
                                </div>
                                
                                <!-- Resources -->
                                        @if(optional($subTopic->sorted_items)?->isNotEmpty())
    <div class="resources-container mb-6">
        <h6 class="text-gray-600 font-medium mb-4 flex items-center">
            <i class="fas fa-file-alt mr-3"></i>Resources
        </h6>
        <div class="space-y-3">
            {{-- Non-Quiz Items --}}
            @foreach($subTopic->sorted_items as $item)
                @if(!$item instanceof App\Models\LomQuiz)
                    @if($item instanceof App\Models\LomLabel)
                        <div class="p-3 bg-white rounded shadow-sm flex items-center gap-3 border">
                            <i class="fas fa-tag text-primary"></i>
                            <span>{!! $item->content !!}</span>
                        </div>
                    @elseif($item instanceof App\Models\LomFiles)
                        @php
                            $ext = strtolower(pathinfo($item->file_path, PATHINFO_EXTENSION));
                            $icon = match($ext) {
                                'pdf' => 'fa-file-pdf text-danger',
                                'doc','docx' => 'fa-file-word text-blue-600',
                                'ppt','pptx' => 'fa-file-powerpoint text-yellow-500',
                                'jpg','jpeg','png' => 'fa-file-image text-green-500',
                                default => 'fa-file-alt text-gray-500',
                            };
                        @endphp
                        <a href="{{ Storage::url('files/'.basename($item->file_path)) }}" target="_blank"
                           class="flex items-center gap-3 p-3 bg-white rounded shadow-sm hover:bg-gray-50 border">
                            <i class="fas {{ $icon }} fs-4"></i>
                            <div>
                                <h6 class="mb-0">{{ $item->name }}</h6>
                                <small class="text-gray-500">{{ basename($item->file_path) }}</small>
                            </div>
                        </a>
                    @elseif($item instanceof App\Models\LomAssign)
                        <a href="{{ route('student.assignment.show', $item->id) }}"
                           class="flex items-start gap-3 p-3 bg-white rounded shadow-sm hover:bg-gray-50 border">
                            <i class="fas fa-tasks text-yellow-500 fs-4 mt-1"></i>
                            <div>
                                <h6 class="mb-1">{!! $item->name !!}</h6>
                                @if($item->due_date)
                                    <span class="badge bg-yellow-300 text-yellow-900 p-1">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Due: {{ \Carbon\Carbon::parse($item->due_date)->format('d M Y, H:i') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @elseif($item instanceof App\Models\LomForum)
                        <a href="{{ route('forums.show', $item->id) }}"
                           class="flex items-start gap-3 p-3 bg-white rounded shadow-sm hover:bg-gray-50 border">
                            <i class="fas fa-comments text-info fs-4 mt-1"></i>
                            <div>
                                <h6 class="mb-1">{!! $item->name !!}</h6>
                                <p class="text-gray-500 mb-0">{!! $item->description !!}</p>
                            </div>
                        </a>
                    @elseif($item instanceof App\Models\LomPage)
                        <a href="{{ route('student.page.show', $item->id) }}"
                           class="flex items-center gap-3 p-3 bg-white rounded shadow-sm hover:bg-gray-50 border">
                            <i class="fas fa-file-alt text-primary fs-4"></i>
                            <div>
                                <h6 class="mb-0">{!! $item->name !!}</h6>
                                <small class="text-gray-500">{!! $item->description !!}</small>
                            </div>
                        </a>
                    @elseif($item instanceof App\Models\LomFolder)
                        <a href="{{ route('folders.show', $item->id) }}"
                           class="flex items-center gap-3 p-3 bg-white rounded shadow-sm hover:bg-gray-50 border">
                            <i class="fas fa-folder text-yellow-500 fs-4"></i>
                            <div>
                                <h6 class="mb-0">{!! $item->name !!}</h6>
                                <small class="text-gray-500">Folder</small>
                            </div>
                        </a>
                    @elseif($item instanceof App\Models\LomUrl)
                        <a href="{{ $item->url_link }}" target="_blank"
                           class="flex items-center gap-3 p-3 bg-white rounded shadow-sm hover:bg-gray-50 border">
                            <i class="fas fa-link text-red-500 fs-4"></i>
                            <div>
                                <h6 class="mb-1">{!! $item->name !!}</h6>
                                <small class="text-gray-500 truncate block">{!! $item->url_link !!}</small>
                            </div>
                        </a>
                    @elseif($item instanceof App\Models\LomLesson)
                        <div class="p-3 bg-white rounded shadow-sm border">
                            <i class="fas fa-book-open text-purple fs-4 me-2"></i>
                            <div>
                                <h6>{!! $item->name !!}</h6>
                                <p class="text-gray-500 mb-0">{!! $item->description !!}</p>
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach

            {{-- Quiz Items --}}
            @foreach($subTopic->sorted_items as $item)
                @if($item instanceof App\Models\LomQuiz)
                    <a href="{{ route('quiz.showMahasiswa', $item->id) }}"
                       class="flex items-start gap-3 p-3 bg-white rounded shadow-sm hover:bg-gray-50 border">
                        <i class="fas fa-question-circle text-red-500 fs-4 mt-1"></i>
                        <div>
                            <h6 class="mb-1">{!! $item->name !!}</h6>
                            <div class="flex gap-2 mt-1">
                                <span class="badge bg-success"><i class="fas fa-door-open me-1"></i>{{ \Carbon\Carbon::parse($item->time_open)->format('d M Y, H:i') }}</span>
                                <span class="badge bg-warning text-dark"><i class="fas fa-door-closed me-1"></i>{{ \Carbon\Carbon::parse($item->time_close)->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
@else
    <div class="bg-blue-50 text-blue-700 border border-blue-200 rounded-lg p-4 mb-4 flex items-center">
        <i class="fas fa-info-circle mr-3"></i>No resources available for this subtopic.
    </div>
@endif

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
    <div class="bg-blue-50 text-blue-700 border border-blue-200 rounded-lg p-5 mb-8 flex items-center justify-center animate__animated animate__fadeIn animate__delay-2s">
        <i class="fas fa-info-circle mr-3"></i>
        <span class="font-medium">No subtopics available for this topic.</span>
    </div>

        @endif

        <!-- References -->
        @if(optional($topic->references)?->isNotEmpty())
            <div class="card mb-8 border-0 shadow-md rounded-2xl overflow-hidden animate__animated animate__fadeIn animate__delay-3s">
                <div class="card-header bg-white p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-bookmark mr-3"></i>References
                    </h3>
                </div>
                <div class="card-body p-6">
                    <div class="space-y-4">
                        @foreach($topic->references as $referensi)
                            <div class="flex items-start bg-gray-50 p-4 rounded-lg shadow-sm hover:bg-gray-100 transition">
                                <i class="fas fa-quote-left mr-3 text-gray-400 mt-1"></i>
                                <p class="text-gray-700 leading-relaxed">{{ $referensi->content ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="bg-blue-50 text-blue-700 border border-blue-200 rounded-lg p-4 mb-8 flex items-center animate__animated animate__fadeIn animate__delay-3s">
                <i class="fas fa-info-circle mr-3"></i>No References
            </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex flex-col sm:flex-row justify-between gap-4 mt-8 mb-10 animate__animated animate__fadeIn animate__delay-4s">
            <a href="{{ route('student.topic.index', $course->id) }}" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full px-6 py-3 font-medium transition flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to Course
            </a>
           
        </div>
    </div>
</div>

<!-- Bootstrap JS & Animate.css -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">