@extends('layouts.home')

@section('title', __('Protected Folder'))

@section('content')
    <div id="content"
        class="rounded-[1rem] dark:bg-[#131314] bg-white grow h-full w-full text-[#1f1f1f] dark:text-[#e3e3e3] flex flex-col justify-center items-center">
        <img src="{{ asset('assets/media/webp/password.webp') }}" alt="Image password" class="w-[176px] lg:w-[256px]">
        <h1 class="mt-4 text-xl font-medium">{{ __('This folder is protected') }}</h1>
        <p class="mt-2 text-sm text-center">{{ __('Please enter the password to access the content') }}</p>
        <form action="{{ route('home.store-password') }}" method="POST" class="mt-4" id="form-password">
            @csrf
            <input type="password" name="password"
                class="w-full rounded-[0.5rem] dark:bg-[#1f1f1f] bg-[#f3f4f6] border-none p-2"
                placeholder="{{ __('Password') }}" required>
            <input type="hidden" name="path" value="{{ $path }}">
            <button type="submit"
                class="w-full mt-2 rounded-[0.5rem] dark:bg-[#1f1f1f] bg-[#3b82f6] text-white p-2">{{ __('Submit') }}</button>
        </form>
    </div>
@endsection

@push('footerScripts')
    <script type="module">
        const form = $('#form-password');
        const inputPassword = form.find('input[name="password"]');
        inputPassword.focus();
        let submitText = form.find('button[type="submit"]').text();
        let loadingText = '{{ __('Loading...') }}';
        const error = $('#errorModal');
        let isLoading = false;
        form.submit(function(e) {
            e.preventDefault();
            if (isLoading) {
                return;
            }
            form.find('button[type="submit"]').text(loadingText);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        // cet cookie value
                        document.cookie = `${response.cookie.name}=${response.cookie.value}; path=/`;
                        window.location.reload();
                    } else {
                        form.find('button[type="submit"]').text(submitText);
                        inputPassword.val('');
                        inputPassword.focus();
                        error.find('#error-message').text(response.message);
                        errorModal.showModal();
                    }
                },
                error: function(xhr, status, error) {
                    form.find('button[type="submit"]').text(submitText);
                    inputPassword.val('');
                    inputPassword.focus();
                    error.find('#error-message').text(xhr.responseJSON.message);
                    errorModal.showModal();
                }
            });
        });
    </script>
@endpush
