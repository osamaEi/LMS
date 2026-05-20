<section class="why-section">
    <div class="container">
        <div class="section-head">
            <h2>{{ __('Why Choose Us') }}</h2>
            <p>{{ __('We offer an integrated training system that combines quality, flexibility, and modern technologies to ensure the best educational experience.') }}</p>
        </div>
        <div class="row g-4">
            @php
            $cards = [
                ['icon'=>'bi-headset',      'title'=>__('Continuous Support'),        'text'=>__('24/7 technical support service helps you overcome any technical problem.')],
                ['icon'=>'bi-person-badge', 'title'=>__('Specialized Trainers'),      'text'=>__('Training is conducted by elite certified trainers with academic and professional experience.')],
                ['icon'=>'bi-laptop',       'title'=>__('Digital Education'),         'text'=>__('A smooth, secure educational experience compatible with trainees needs.')],
                ['icon'=>'bi-patch-check',  'title'=>__('Accredited Training'),       'text'=>__('Accredited by official authorities within the Kingdom, ensuring a reliable path for developing your professional skills.')],
                ['icon'=>'bi-award',        'title'=>__('Official Certificates'),     'text'=>__('After completing programs, trainees receive officially accredited certificates that enhance their career opportunities.')],
                ['icon'=>'bi-credit-card',  'title'=>__('Multiple Payment Methods'),  'text'=>__('We provide a flexible payment system that suits all trainees needs.')],
                ['icon'=>'bi-play-btn',     'title'=>__('Interactive Content'),       'text'=>__('Video lessons, files, assessments, and tests that enhance understanding and support learning by practice.')],
                ['icon'=>'bi-map',          'title'=>__('Clear Paths'),               'text'=>__('Educational plans built on clear paths extending up to 10 training quarters.')],
            ];
            @endphp
            @foreach($cards as $card)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="why-card">
                    <div class="why-icon"><i class="bi {{ $card['icon'] }}"></i></div>
                    <h5>{{ $card['title'] }}</h5>
                    <p>{{ $card['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
