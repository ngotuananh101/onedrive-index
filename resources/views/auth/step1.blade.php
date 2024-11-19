@extends('layouts.auth')

@section('title', __('message.auth.step1.title'))

@section('body')
    <div class="flex flex-col justify-start w-full gap-10 lg:flex-row lg:justify-between">
        <div class="text-[#1f1f1f] dark:text-[#e3e3e3] flex flex-col min-w-[25%]">
            <img src="{{ asset('assets/media/logo/onedrive.svg') }}" alt="{{ config('app.name') }}" class="w-[48px]">
            <h1 class="mt-6 text-4xl font-normal leading-8">
                {{ config('app.name') }}
            </h1>
            <p class="mt-4 text-base">
                {{ __('message.auth.step1.title') }}
            </p>
        </div>
        <div class="text-[#444746] dark:text-[#c4c7c5] flex flex-col justify-between grow lg:grow-0">
            <div>
                <p class="text-sm text-orange-400">
                    <i class="fa-regular fa-triangle-exclamation"></i>
                    {!! __('message.auth.step1.recommendation') !!}
                </p>
                <p class="mt-3 text-sm">
                    {{ __('message.auth.step1.description') }}
                </p>
                <div class="mt-3 overflow-x-auto">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>CLIENT_ID</th>
                                <td>{{ config('onedrive.client_id') }}</td>
                            </tr>
                            <tr>
                                <th>CLIENT_SECRET</th>
                                <td>***********************************</td>
                            </tr>
                            <tr>
                                <th>REDIRECT_URI</th>
                                <td>{{ config('onedrive.redirect_uri') }}</td>
                            </tr>
                            <tr>
                                <th>AUTH API URL</th>
                                <td>{{ config('onedrive.auth_api_url') }}</td>
                            </tr>
                            <tr>
                                <th>DRIVE API URL</th>
                                <td>{{ config('onedrive.drive_api_url') }}</td>
                            </tr>
                            <tr>
                                <th> API SCOPE</th>
                                <td>{{ config('onedrive.scope') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-sm">
                    <i class="text-orange-400 fa-regular fa-triangle-exclamation"></i>
                    {{ __('message.auth.step1.if_incorrect') }}
                </p>
            </div>
            <div class="mt-6 text-right">
                <a href="{{ route('auth.step2') }}"
                    class="btn bg-[#0b57d0] dark:bg-[#a8c7fa] h-[40px] px-6 rounded-[24px] text-white dark:text-[#062e6f] hover:bg-[#1a73e8] dark:hover:bg-[#8ab4f8]">
                    {{ __('message.auth.step1.process_to_oauth') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@push('footerScripts')
    <script type="module" src="{{ asset('/assets/js/auth/step1.js') }}"></script>
@endpush
