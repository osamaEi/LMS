@php $testimonials = \App\Models\Testimonial::active()->get(); @endphp
@if($testimonials->isNotEmpty())
<section class="testimonials-section">
    <div class="container">
        <div class="section-head">
            <h2>{{ __('What Our Trainees Say') }}</h2>
            <p>{{ __('Real experiences from our graduates who made a difference in their careers.') }}</p>
        </div>
        <div class="row g-4">
            @foreach($testimonials as $t)
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $t->rating ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <p class="testimonial-text">"{{ $t->text }}"</p>
                    <div class="testimonial-author">
                        <div style="width:48px;height:48px;border-radius:50%;background:var(--main-color);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.2rem;flex-shrink:0">{{ mb_substr($t->author, 0, 1) }}</div>
                        <div>
                            <div class="name">{{ $t->author }}</div>
                            <div class="role">{{ $t->role }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
