@extends('layouts.auth')

@section('title', __('message.auth.step2.title'))

@push('headerStyles')
@endpush

@push('headerScripts')
@endpush

@section('body')
    <div class="w-full flex flex-col lg:flex-row justify-start lg:justify-between gap-10" id="wrapper">
        <div class="text-[#1f1f1f] dark:text-[#e3e3e3] flex flex-col min-w-[25%]" id="left">
            <img src="{{ asset('assets/media/logo/onedrive.svg') }}" alt="{{ config('app.name') }}" class="w-[48px]">
            <h1 class="text-4xl font-normal mt-6 leading-8">
                {{ config('app.name') }}
            </h1>
            <p class="text-base mt-4">
                {{ __('message.auth.step2.title') }}
            </p>
        </div>
        <div class="text-[#444746] dark:text-[#c4c7c5] flex flex-col justify-between grow lg:grow-0" id="right">
            <div class="text-sm">
                <p class="text-red-400">
                    <i class="fa-regular fa-triangle-exclamation"></i>
                    {!! __('message.auth.step2.not_owner') !!}
                </p>
                <p class="mt-3">
                    {{ __('message.auth.step2.link_created') }}
                </p>
            </div>
            <div class="text-right mt-6">
                <a href="{{ $authUrl }}"
                    class="btn bg-[#0b57d0] dark:bg-[#a8c7fa] h-[40px] px-6 rounded-[24px] text-white dark:text-[#062e6f] hover:bg-[#1a73e8] dark:hover:bg-[#8ab4f8]">
                    Proccess to OAuth
                </a>
            </div>
        </div>
    </div>
@endsection

@push('footerStyles')
@endpush

@push('footerScripts')
    <script type="module" src="{{ asset('/assets/js/auth/step2.js') }}"></script>
@endpush
