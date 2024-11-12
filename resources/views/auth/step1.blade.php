@extends('layouts.auth')

@section('title', 'Step 1')

@push('headerStyles')
@endpush

@push('headerScripts')
@endpush

@section('body')
    <div class="w-full flex flex-col lg:flex-row justify-start lg:justify-between gap-10">
        <div class="text-[#1f1f1f] dark:text-[#e3e3e3] flex flex-col min-w-[30%]">
            <img src="{{ asset('assets/media/logo/onedrive.svg') }}" alt="{{ config('app.name') }}" class="w-[48px]">
            <h1 class="text-4xl font-normal mt-6 leading-8">
                {{ config('app.name') }}
            </h1>
            <p class="text-base mt-4">
                {{ __('message.auth.step1.title') }}
            </p>
        </div>
        <div class="text-[#444746] dark:text-[#c4c7c5] flex flex-col justify-between grow lg:grow-0">
            <div>
                <p class="text-sm text-orange-400">
                    <i class="fa-regular fa-triangle-exclamation"></i>
                    {!! __('message.auth.step1.recommendation') !!}
                </p>
                <p class="text-sm mt-3">
                    {{ __('message.auth.step1.description') }}
                </p>
                <div class="overflow-x-auto mt-3">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>CLIENT_ID</th>
                                <td>{{ config('onedrive.client_id') }}</td>
                            </tr>
                            <tr>
                                <th>CLIENT_SECRET</th>
                                <td>{{ config('onedrive.client_secret') }}</td>
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
                                <th> API Scope</th>
                                <td>{{ config('onedrive.scope') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-sm mt-3">
                    <i class="fa-regular fa-triangle-exclamation text-orange-400"></i>
                    {{ __('message.auth.step1.if_incorrect') }}
                </p>
            </div>
            <div class="text-right mt-6">
                <button
                    class="btn bg-[#0b57d0] dark:bg-[#a8c7fa] h-[40px] px-6 rounded-[24px] text-white dark:text-[#062e6f] hover:bg-[#1a73e8] dark:hover:bg-[#8ab4f8]">
                    Proccess to OAuth
                </button>
            </div>
        </div>
    </div>
@endsection

@push('footerStyles')
@endpush

@push('footerScripts')
    <script type="module" src="{{ asset('/assets/js/auth/step1.js') }}"></script>
@endpush