@extends('layouts.auth')

@section('title', __('message.auth.step3.title'))

@push('headerStyles')
@endpush

@push('headerScripts')
@endpush

@section('body')
    <div class="flex flex-col justify-start w-full gap-10 lg:flex-row lg:justify-between" id="wrapper">
        <div class="text-[#1f1f1f] dark:text-[#e3e3e3] flex flex-col min-w-[25%]" id="left">
            <img src="{{ asset('assets/media/logo/onedrive.svg') }}" alt="{{ config('app.name') }}" class="w-[48px]">
            <h1 class="mt-6 text-4xl font-normal leading-8">
                {{ config('app.name') }}
            </h1>
            <p class="mt-4 text-base">
                {{ __('message.auth.step3.title') }}
            </p>
        </div>
        <div class="text-[#444746] dark:text-[#c4c7c5] flex flex-col justify-between grow lg:grow-0" id="right">
            <div class="text-sm">
                <div class="flex justify-center">
                    <img src="{{ asset('assets/media/png/success.png') }}" alt="Success image"
                        class="max-w-full lg:max-w-[60%]">
                </div>
                <p class="mt-3 text-green-400">
                    {{ __('message.auth.step3.success') }}
                </p>
            </div>
            <div class="mt-6 text-right">
                <a href="{{ route('home.index') }}"
                    class="btn bg-[#0b57d0] dark:bg-[#a8c7fa] h-[40px] px-6 rounded-[24px] text-white dark:text-[#062e6f] hover:bg-[#1a73e8] dark:hover:bg-[#8ab4f8]">
                    {{ __('message.auth.step3.redirect_to_homepage') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@push('footerStyles')
@endpush

@push('footerScripts')
@endpush
