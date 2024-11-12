@extends('layouts.auth')

@section('title', 'Step 1')

@push('headerStyles')
@endpush

@push('headerScripts')
@endpush

@section('body')
    <div class="w-full flex">
        <div class="text-[#1f1f1f] dark:text-[#e3e3e3] flex flex-col">
            <img src="{{ asset('assets/media/logo/onedrive.svg') }}" alt="{{ config('app.name') }}" class="w-[48px]">
            <h1 class="text-4xl font-normal mt-6 leading-8">
                {{ config('app.name') }}
            </h1>
            <p class="text-base mt-4">
                {{ __('message.auth.step1.title') }}
            </p>
        </div>
    </div>
@endsection

@push('footerStyles')
@endpush

@push('footerScripts')
    <script type="module" src="{{ asset('/assets/js/auth/step1.js') }}"></script>
@endpush
