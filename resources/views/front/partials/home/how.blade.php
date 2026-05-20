<section class="how-section">
    <div class="container">
        <div class="section-head">
            <h2>{{ __('How does our training system work?') }}</h2>
            <p>{{ __('An integrated training system that ensures a clear, organized educational journey with measurable results.') }}</p>
        </div>
        <div class="row align-items-center g-5">
            <div class="col-lg-6 {{ app()->getLocale()=='ar' ? 'order-2' : 'order-1' }}">
                <div class="how-image" style="height:420px">
                    <img loading="lazy" src="{{ $lms3s('13.png') }}" alt="How It Works" />
                </div>
            </div>
            <div class="col-lg-6 {{ app()->getLocale()=='ar' ? 'order-1' : 'order-2' }}">
                <div class="how-steps">
                    <div class="how-step">
                        <div class="how-step-number">1</div>
                        <div class="how-step-text">
                            <h5>{{ __('Registration and Getting Started') }}</h5>
                            <p>{{ __('Start your educational journey easily by creating an account or logging in through Nafath, then discover programs and paths designed to suit your goals.') }}</p>
                        </div>
                    </div>
                    <div class="how-step">
                        <div class="how-step-number">2</div>
                        <div class="how-step-text">
                            <h5>{{ __('Choosing the Right Program for You') }}</h5>
                            <p>{{ __('Whether you\'re looking for an academic path spanning two and a half years, or a short course, you will find what suits your goals.') }}</p>
                        </div>
                    </div>
                    <div class="how-step">
                        <div class="how-step-number">3</div>
                        <div class="how-step-text">
                            <h5>{{ __('Learning and Follow-up') }}</h5>
                            <p>{{ __('Study through visual and organized content, with an attendance system, clear training progress, and direct communication with trainers.') }}</p>
                        </div>
                    </div>
                    <div class="how-step">
                        <div class="how-step-number">4</div>
                        <div class="how-step-text">
                            <h5>{{ __('Assessment and Certification') }}</h5>
                            <p>{{ __('After completing your training requirements, you will be evaluated and your accredited digital certificate will be issued.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
