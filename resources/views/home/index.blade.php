@extends('layouts.home')

@section('title')
    @if (count($data['breadcrumbs']) == 0)
        {{ __('Home') }}
    @endif
    @if (count($data['breadcrumbs']) > 0)
        {{ $data['breadcrumbs'][count($data['breadcrumbs']) - 1]['name'] }}
    @endif
@endsection

@section('content')
    <div class="overflow-x-auto grow" id="content-scroll">
        <table class="table table-sm table-pin-rows text-[#442424]">
            <thead class="text-[#e3e3e3]">
                <tr
                    class="dark:bg-[#131314] bg-white text-[#1f1f1f] dark:text-[#e3e3e3] text-[14px] border-b-[#c7c7c7] dark:border-b-[#444746]">
                    <td class="w-full max-w-full lg:w-[50%] lg:max-w-[50%]">{{ __('Name') }}</td>
                    <td class="hidden lg:table-cell">{{ __('Owner') }}</td>
                    <td class="hidden lg:table-cell">{{ __('Last Updated At') }}</td>
                    <td class="hidden lg:table-cell">{{ __('Size') }}</td>
                    <th></th>
                </tr>
            </thead>
            <tbody class="border-b border-b-[#c7c7c7] dark:border-b-[#444746]" id="data">
                @foreach ($data['items'] as $item)
                    @php
                        $controller = new App\Http\Controllers\OneDriveController();
                    @endphp
                    <tr
                        class="font-light text-[#1f1f1f] dark:text-[#e3e3e3] hover:bg-[#f0f1f1] dark:hover:bg-[#212122] border-b-[#c7c7c7] dark:border-b-[#444746]">
                        <td class="text-[15px]">
                            <a href="{{ isset($item['folder']) ? route('home.folder', ['id' => $item['id']]) : route('home.file', ['id' => $item['id']]) }}"
                                class="flex items-center gap-2">

                                @if (isset($item['folder']))
                                    <i class="fa-solid fa-folder text-[#f0b429] text-[25px]"></i>
                                @else
                                    <i class="fa-solid {{ $controller->getFileIcon($item['name']) }} text-[25px]"></i>
                                @endif
                                <span class="font-medium">{{ $item['name'] }}</span>
                            </a>
                        </td>
                        <td class="hidden lg:table-cell">
                            @if (isset($data['owner']))
                                <div class="flex items-center avatar">
                                    <div class="w-[25px] h-[25px] rounded-full">
                                        <img src="{{ $data['owner']['photo'] }}" />
                                    </div>
                                    <span class="ml-2">{{ $data['owner']['displayName'] }}</span>
                                </div>
                            @else
                                <span class="text-[#f0b429]">{{ __('You') }}</span>
                            @endif
                        </td>
                        <td class="hidden lg:table-cell">
                            {{ Carbon\Carbon::parse($item['lastModifiedDateTime'])->format(config('onedrive.date_format')) }}
                        </td>
                        <td class="hidden lg:table-cell">{{ $controller->convertSize($item['size']) }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button"
                                        class="flex items-center justify-center w-[30px] h-[30px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                                        <i class="fa-regular fa-ellipsis-vertical text-[15px]"></i>
                                    </div>
                                    <ul tabindex="0"
                                        class="dropdown-content menu bg-white dark:bg-[#1e1f20] rounded-box z-[1] w-52 p-2 shadow">
                                        @if (isset($item['@microsoft.graph.downloadUrl']))
                                            <li>
                                                <a href="{{ route('home.download', $item['id']) }}" target="_blank">
                                                    {{ __('Download') }}
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a
                                                href="{{ isset($item['folder']) ? route('home.folder', ['id' => $item['id']]) : route('home.file', ['id' => $item['id']]) }}">
                                                {{ __('View') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="hidden" id="loading">
                <tr class="bg-transparent">
                    <td colspan="5" class="h-[60px]">
                        <div class="flex items-end justify-center h-full">
                            <div class="loading-container">
                                <div class="ball"></div>
                                <div class="ball"></div>
                                <div class="ball"></div>
                                <div class="ball"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('footerScripts')
    <script type="module">
        $('#content-scroll').data('nextPage', '{{ $data['next_url'] }}');
    </script>
    <script type="module" src="{{ asset('/assets/js/home/index.js') }}"></script>
@endpush
