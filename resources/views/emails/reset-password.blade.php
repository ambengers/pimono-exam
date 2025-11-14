@extends('emails.layout')

@section('title', 'Reset Your Password')

@section('content')
    <h2>Reset Your Password</h2>
    
    <p>Hello {{ $user->name }},</p>
    
    <p>We received a request to reset your password. Click the button below to create a new password:</p>
    
    <div class="button-container">
        <a href="{{ $url }}" class="reset-button">Reset Password</a>
    </div>
    
    <p>If the button doesn't work, copy and paste this link into your browser:</p>
    
    <div class="link-fallback">
        <p>Reset Link:</p>
        <a href="{{ $url }}">{{ $url }}</a>
    </div>
    
    <div class="security-note">
        <p><strong>Security Note:</strong> This password reset link will expire in {{ $count }} minutes. If you didn't request a password reset, please ignore this email.</p>
    </div>
    
    <p>If you're having trouble clicking the button, copy and paste the URL above into your web browser.</p>
@endsection
