{{ html()->select('language', ['en' => __('en'), 'vi' => __('vi')], app()->getLocale())->class('select select-ghost text-[#444746] dark:text-[#c4c7c5] h-fit min-h-fit') }}

<script>
    const language = document.querySelector('#language');
    language.addEventListener('change', function() {
        const selectedLanguage = language.value;
        window.location.href = '/language/' + selectedLanguage;
    });
</script>
