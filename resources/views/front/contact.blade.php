@extends('layouts.front')

@section('title', __('Contact Us') . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
    /* Contact Section */
    .contact-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
    }

    .contact-section .row {
        display: flex;
        flex-wrap: wrap;
    }

    /* Form Section */
    .form-section {
        background: white;
        padding: 40px;
        border-radius: {{ app()->getLocale() == 'ar' ? '20px 0 0 20px' : '0 20px 20px 0' }};
    }

    .form-title {
        margin-bottom: 30px;
        color: #2c3e50;
    }

    .form-label {
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .required {
        color: #e74c3c;
    }

    .custom-input,
    .custom-textarea,
    .form-select {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px 16px;
        background-color: rgba(230, 229, 229, 0.817);
        transition: all 0.3s;
    }

    .custom-input:focus,
    .custom-textarea:focus,
    .form-select:focus {
        border-color: var(--main-color);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .phone-input-group {
        display: flex;
        gap: 10px;
    }

    .country-code {
        width: 100px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
        background: white;
    }

    .custom-textarea {
        min-height: 80px;
    }

    /* File Upload */
    .file-upload-area {
        margin-bottom: 15px;
    }

    .file-input {
        display: none;
    }

    .file-upload-label {
        cursor: pointer;
        margin-bottom: 15px;
    }

    .upload-hint {
        color: #7f8c8d;
        margin-bottom: 0;
        font-size: 0.85rem;
    }

    .btn-upload {
        background: white;
        border: 2px solid var(--main-color);
        color: var(--main-color);
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-upload:hover {
        background: var(--main-color);
        color: white;
    }

    /* Submit Button */
    .btn-submit {
        background: var(--main-color);
        color: white;
        border: none;
        padding: 14px 40px 18px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s;
        max-width: 300px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Contact Info Section */
    .contact-info-section {
        border-radius: 20px;
        padding: 30px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgb(196, 194, 194);
        background: white;
    }

    .contact-title {
        margin-bottom: 40px;
        font-weight: bold;
    }

    .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        gap: 15px;
    }

    .icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #eaf5fb;
    }

    .icon-wrapper i {
        font-size: 20px;
        color: var(--main-color);
    }

    .contact-details h5 {
        margin-bottom: 5px;
        font-size: 0.95rem;
        color: #666;
    }

    .contact-details a {
        color: var(--main-color);
        text-decoration: none;
        transition: all 0.3s;
    }

    .contact-details a:hover {
        text-decoration: underline;
    }

    .contact-details .bi-copy {
        cursor: pointer;
        {{ app()->getLocale() == 'ar' ? 'margin-right' : 'margin-left' }}: 5px;
        color: #666;
    }

    .social-section {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #e5e7eb;
    }

    .social-section h5 {
        margin-bottom: 15px;
        color: #333;
    }

    .social-icons {
        display: flex;
        gap: 15px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #eaf5fb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--main-color);
        transition: all 0.3s;
        text-decoration: none;
    }

    .social-link:hover {
        background: var(--main-color);
        color: white;
        transform: translateY(-5px);
    }

    /* Another Contact Section */
    .cta-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
        background: #f9fafb;
    }

    .cta-section .row {
        align-items: center;
    }

    .cta-content h2 {
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .cta-content p {
        line-height: 1.8;
        color: rgba(56, 66, 80, 1);
        margin-bottom: 1.5rem;
    }

    .cta-image img {
        width: 100%;
        border-radius: 20px;
    }

    /* Alert Styles */
    .alert {
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    @media (max-width: 991px) {
        .contact-info-section {
            border-radius: 20px 20px 0 0;
            margin-bottom: 0;
        }

        .form-section {
            border-radius: 0 0 20px 20px;
            padding: 35px 25px;
        }
    }

    @media (max-width: 768px) {
        .contact-section,
        .cta-section {
            padding: 1.5rem 1rem;
        }

        .phone-input-group {
            flex-direction: column;
        }

        .country-code {
            width: 100%;
        }

        .contact-info-section,
        .form-section {
            padding: 30px 20px;
        }

        .btn-submit {
            width: 100%;
            max-width: 100%;
        }

        .cta-image {
            margin-top: 2rem;
        }
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="breadcrumb-nav">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <span>></span>
            <span>{{ __('Contact Us') }}</span>
        </div>
        <h2>{{ __('We\'re Happy to Hear From You') }}</h2>
        <p>
            {{ __('At') }} <span style="color: var(--main-color);">{{ __('Al-Ertiqaa High Institute for Training') }}</span>{{ __(', we are keen to provide an integrated educational experience that begins from the moment you contact us.') }}
        </p>
        <p>
            {{ __('You can inquire about courses, training paths, registration dates, or any other information. Our support team is ready to respond to all your inquiries quickly and efficiently.') }}
        </p>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <!-- Form Section -->
                <div class="col-lg-8 form-section">
                    <h2 class="form-title">{{ __('Send Your Message') }}</h2>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- First and Last Name Row -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label">{{ __('First Name') }} <span class="required">*</span></label>
                                <input type="text" name="first_name" class="form-control custom-input" placeholder="{{ __('Enter your first name') }}" value="{{ old('first_name') }}" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Last Name') }} <span class="required">*</span></label>
                                <input type="text" name="last_name" class="form-control custom-input" placeholder="{{ __('Enter your last name') }}" value="{{ old('last_name') }}" required />
                            </div>
                        </div>

                        <!-- Email and Phone Row -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label">{{ __('Email') }} <span class="required">*</span></label>
                                <input type="email" name="email" class="form-control custom-input" placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Phone Number') }} <span class="required">*</span></label>
                                <div class="phone-input-group">
                                    <select class="country-code">
                                        <option value="+966">+966</option>
                                    </select>
                                    <input type="tel" name="phone" class="form-control custom-input" placeholder="00 000 0000" value="{{ old('phone') }}" required />
                                </div>
                            </div>
                        </div>

                        <!-- Category and Subject Row -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label">{{ __('Category') }}</label>
                                <select name="category" class="form-select custom-input">
                                    <option selected disabled>{{ __('Select a category...') }}</option>
                                    <option value="General Inquiry" {{ old('category') == 'General Inquiry' ? 'selected' : '' }}>{{ __('General Inquiry') }}</option>
                                    <option value="Technical Support" {{ old('category') == 'Technical Support' ? 'selected' : '' }}>{{ __('Technical Support') }}</option>
                                    <option value="Registration and Payment" {{ old('category') == 'Registration and Payment' ? 'selected' : '' }}>{{ __('Registration and Payment') }}</option>
                                    <option value="Certificates" {{ old('category') == 'Certificates' ? 'selected' : '' }}>{{ __('Certificates') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Subject') }}</label>
                                <input type="text" name="subject" class="form-control custom-input" placeholder="{{ __('Write your subject here') }}" value="{{ old('subject') }}" />
                            </div>
                        </div>

                        <!-- Message and File Upload Row -->
                        <div class="row mb-4">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label class="form-label">{{ __('How can we help?') }} <span class="required">*</span></label>
                                <textarea name="message" class="form-control custom-textarea" rows="5" placeholder="{{ __('Write your message here...') }}" required>{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ __('Upload Attachments') }}</label>
                                <div class="file-upload-area">
                                    <input type="file" id="fileUpload" name="attachment" class="file-input" accept=".jpg,.jpeg,.png,.pdf" />
                                    <label for="fileUpload" class="file-upload-label">
                                        <p class="upload-hint">
                                            {{ __('Maximum file size is 20 MB. Supported formats: jpg, png, pdf') }}
                                        </p>
                                    </label>
                                    <button type="button" class="btn-upload" onclick="document.getElementById('fileUpload').click()">
                                        {{ __('Browse Files') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-submit">
                            {{ __('Send Message Now') }}
                        </button>
                    </form>
                </div>

                <!-- Contact Info Section -->
                <div class="col-lg-4 contact-info-section">
                    <h3 class="contact-title">{{ __('Contact Us Now') }}</h3>

                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="contact-details">
                            <h5>{{ __('Phone Number') }}</h5>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="tel:9200343222">9200343222</a>
                                <i class="bi bi-copy"></i>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <div class="contact-details">
                            <h5>{{ __('Text Message') }}</h5>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="sms:199099">199099</a>
                                <i class="bi bi-copy"></i>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h5>{{ __('Email') }}</h5>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="mailto:help@company.sa">help@company.sa</a>
                                <i class="bi bi-copy"></i>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <i class="bi bi-printer"></i>
                        </div>
                        <div class="contact-details">
                            <h5>{{ __('Fax') }}</h5>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="tel:00966-11-834-6654">00966-11-834-6654</a>
                                <i class="bi bi-copy"></i>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h5>{{ __('Location') }}</h5>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="#">{{ __('Riyadh') }}</a>
                                <i class="bi bi-link-45deg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="social-section">
                        <h5>{{ __('Follow Us') }}</h5>
                        <div class="social-icons">
                            <a href="#" class="social-link"><i class="bi bi-twitter-x"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="row">
            <div class="col-lg-6">
                <div class="cta-content">
                    <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
                    <h2>{{ __('We\'re Happy to Hear From You... We Value Every Question and Inquiry') }}</h2>
                    <p>
                        {{ __('Our support and guidance team is here to serve you and answer all your questions related to training programs, career paths, registration procedures, term system, course schedules, or any details you need to know before starting your journey with us.') }}
                    </p>
                    <a href="{{ route('faq') }}" class="full-btn">{{ __('Frequently Asked Questions') }}</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="cta-image">
                    <img src="{{ asset('images/contactUs.jpg') }}" alt="Contact Us" onerror="this.src='{{ asset('images/course.jpg') }}'" />
                </div>
            </div>
        </div>
    </section>
@endsection
