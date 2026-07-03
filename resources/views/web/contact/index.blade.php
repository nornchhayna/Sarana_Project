@extends('layouts.app')

@section('content')

<div class="contact-page-container py-5">
    <div class="contact-split-wrapper">
        
        <div class="map-column">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.25280821213!2d-74.11976373099162!3d40.69766374874431!2m3!1f0!2f0!3f0!2m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1656360000000!5m2!1sen!2sus" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <div class="form-column">
            <div class="form-header">
                <h2>Contact Us</h2>
                <p>Our team would love to hear from you</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                
                <div class="form-row">
                    <div class="form-col-6 form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Enter your full name" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    <div class="form-col-6 form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="example@domain.com" required>
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col-6 form-group">
                        <label class="form-label">Phone Number (Optional)</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. +1 234 567 890">
                    </div>

                    <div class="form-col-6 form-group">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="What is this regarding?">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="5" placeholder="Type your message details here..." required>{{ old('message') }}</textarea>
                    @error('message') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="btn-submit-container">
                    <button type="submit" class="btn btn-primary">Send message</button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection