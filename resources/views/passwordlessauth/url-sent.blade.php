@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Check your inbox') }}</div>

                    <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            {{ __('We just emailed a magic link associated with your account.') }}
                            {{ __('Click the link to sign in.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
