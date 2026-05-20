<section class="programs-section">
    <div class="container">
        <div class="section-head">
            <h2>{{ __('Comprehensive training paths to build your future') }}</h2>
            <p>{{ __('We provide training paths spanning two and a half years through 10 training quarters, plus short and specialized courses for various professional goals.') }}</p>
        </div>
        <div class="courses-grid">
            @php $programImages = [$lms3f('دبلوم  برمجيات.png'), $lms3f('دبلوم  الموارد.png'), $lms3f('لغة انجليزية.png')]; @endphp
            @forelse($featuredPrograms ?? [] as $i => $program)
            <div class="course-card">
                <img src="{{ $program->image ? asset('storage/' . $program->image) : $programImages[$i % count($programImages)] }}" alt="{{ $program->name }}" />
                <div class="course-card-body d-flex flex-column">
                    <h4>{{ $program->name }}</h4>
                    <p>{{ Str::limit($program->description ?? __('A comprehensive training program designed to develop professional skills.'), 100) }}</p>
                    <div class="marks">
                        @if($program->duration_months)<span class="st"><i class="bi bi-clock"></i> {{ $program->duration_months }} {{ __('months') }}</span>@endif
                        @if($program->price && $program->price > 0)
                        <span class="nd"><i class="bi bi-tag"></i> {{ number_format($program->price,0) }} {{ __('SAR') }}</span>
                        @else
                        <span class="nd"><i class="bi bi-check-circle"></i> {{ __('Free') }}</span>
                        @endif
                        <span class="th"><i class="bi bi-mortarboard"></i> {{ $program->code ?? __('Program') }}</span>
                    </div>
                    <div class="course-btns">
                        <a href="{{ route('training-paths') }}" class="btn-outline-course">{{ __('View Details') }}</a>
                        <a href="{{ auth()->check() ? route('student.my-program') : route('login') }}" class="btn-primary-course">{{ __('Register Now') }}</a>
                    </div>
                </div>
            </div>
            @empty
            @foreach([
                [$lms3f('دبلوم  برمجيات.png'), __('Computer Science Diploma'),  __('Foundations of computing, programming, networks, and databases.'),           12, 5000, 'CS-101'],
                [$lms3f('دبلوم  الموارد.png'), __('Business Administration'),    __('Modern management fundamentals: leadership, planning, and decision-making.'), 12, null,  'BA-201'],
                [$lms3f('لغة انجليزية.png'),   __('Digital Marketing Diploma'),  __('SEO, social media, paid ads, and analytics strategies.'),                     10, 4500, 'DM-301'],
            ] as [$img,$name,$desc,$months,$price,$code])
            <div class="course-card">
                <img src="{{ $img }}" alt="{{ $name }}" />
                <div class="course-card-body d-flex flex-column">
                    <h4>{{ $name }}</h4>
                    <p>{{ $desc }}</p>
                    <div class="marks">
                        <span class="st"><i class="bi bi-clock"></i> {{ $months }} {{ __('months') }}</span>
                        @if($price)<span class="nd"><i class="bi bi-tag"></i> {{ number_format($price,0) }} {{ __('SAR') }}</span>@else<span class="nd"><i class="bi bi-check-circle"></i> {{ __('Free') }}</span>@endif
                        <span class="th"><i class="bi bi-mortarboard"></i> {{ $code }}</span>
                    </div>
                    <div class="course-btns">
                        <a href="{{ route('training-paths') }}" class="btn-outline-course">{{ __('View Details') }}</a>
                        <a href="{{ route('login') }}" class="btn-primary-course">{{ __('Register Now') }}</a>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</section>
