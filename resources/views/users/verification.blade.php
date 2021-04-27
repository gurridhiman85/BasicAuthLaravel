@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Verification') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('verification') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('OTP Code') }}</label>

                                <div class="col-md-6">
                                    <input id="email_verification_code" type="text" class="form-control @error('email_verification_code') is-invalid @enderror" name="email_verification_code" value="{{ old('name') }}" autocomplete="email_verification_code" autofocus>

                                    @error('email_verification_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <input id="email" type="hidden" name="email" value="{{ isset($email) ? $email : '' }}" required>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Verify') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
