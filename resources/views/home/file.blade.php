@extends('layouts.home')

@section('title', $file['name'])

@push('headerStyles')
    @if (isset($file['customPreview']))
        <style>
            media-player {
                height: 100%;
            }
        </style>
    @endif
@endpush

@push('headerScripts')
    @if (isset($file['customPreview']))
        <script type="module">
            const player = await VidstackPlayer.create({
                target: '#player',
                title: '{{ $file['name'] }}',
                poster: '{{ $file['thumbnail'] }}',
                layout: new VidstackPlayerLayout({}),
            });
        </script>
    @endif
@endpush

@section('content')
    <div id="content"
        class="rounded-[1rem] dark:bg-[#131314] bg-white grow h-full w-full text-[#1f1f1f] dark:text-[#e3e3e3] flex flex-col border border-[#13131440] dark:border-0">
        <div class="overflow-hidden grow" id="content-scroll">
            @if (isset($file['preview']))
                <iframe src="{{ $file['preview'] }}" frameborder="0" class="w-full h-full rounded-[1rem] !m-t-[48px]"
                    id="preview">
                </iframe>
            @elseif (isset($file['customPreview']))
                <div class="flex items-center justify-center w-full h-full">
                    <video id="player" playsinline controls crossorigin>
                        <source src="{{ $file['@microsoft.graph.downloadUrl'] }}" type="video/mp4" />
                        @if (isset($file['subTitle']))
                            <track kind="subtitles" src="{{ $file['subTitle']['downloadUrl'] }}" srclang="UN"
                                label="{{ $file['subTitle']['name'] }}" />
                        @endif
                    </video>
                </div>
            @else
                <div class="flex items-center justify-center w-full h-full">
                    {{ __('Preview not available.') }}
                </div>
            @endif
        </div>
    </div>
    <div id="sidebar"
        class="hidden lg:flex min-w-[350px] max-w-[350px] rounded-[1rem] dark:bg-[#131314] bg-white h-full text-[#1f1f1f] dark:text-[#e3e3e3] flex-col">
        <div class="flex justify-between gap-2 p-3 pb-0 sidebar-header">
            <a href="{{ route('home.folder', $file['parentReference']['id']) }}"
                class="flex items-center justify-center w-[30px] h-[30px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                <i class="fa-solid fa-arrow-left text-[#1f1f1f] dark:text-[#e3e3e3]"></i>
            </a>
            <div class="flex items-center justify-start grow max-w-[calc(100%-76px)]">
                <h3 class="text-base font-medium truncate " id="sidebar-title">
                    {{ $file['name'] }}
                </h3>
            </div>
            {{-- <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="m-1 btn">Click</div>
                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
                    <li><a>Item 1</a></li>
                    <li><a>Item 2</a></li>
                </ul>
            </div> --}}
            <div class="flex items-center justify-end gap-2">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button"
                        class="flex items-center justify-center w-[30px] h-[30px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                        <i class="fa-regular fa-ellipsis-vertical text-[15px]"></i>
                    </div>
                    <ul tabindex="0"
                        class="dropdown-content menu bg-white dark:bg-[#1e1f20] rounded-box z-[9999] w-52 p-2 shadow">
                        <li>
                            <a href="{{ route('home.download', $file['id']) }}" target="_blank">
                                {{ __('Download') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="max-h-full overflow-y-auto tab-info grow">
            <div
                class="flex border-b border-b-[#c7c7c7] dark:border-b-[#444746] pt-5 sticky top-0 z-[9000] dark:bg-[#131314] bg-white">
                <div class="w-[50%] flex justify-center">
                    <span class="text-center pb-4 border-b-[3px] font-semibold tab-title active text-[0.875rem]"
                        data-target-id="tab_1">{{ __('Information') }}</span>
                </div>
                <div class="w-[50%] flex justify-center">
                    <span class="text-center pb-4 border-b-[3px] font-semibold tab-title text-[0.875rem]"
                        data-target-id="tab_2" data-id="{{ $file['id'] }}">{{ __('Activity') }}</span>
                </div>
            </div>
            <div class="tab-data">
                <div class="flex gap-4 tab-pane" id="tab_1">
                    <div class="flex-col flex main grow text-[#c7c7c7] dark:text-[#444746]">
                        <div
                            class="flex flex-col justify-center w-full p-3 pb-4 border-b border-b-[#c7c7c7] dark:border-b-[#444746] ">
                            <div class="border rounded-md border-[#c7c7c7] dark:border-[#444746]">
                                <img src="{{ $file['thumbnail'] }}" alt="Preview"
                                    class="max-w-full m-auto preview min-h-[176px] rounded-md">
                            </div>
                            <div class="flex flex-col justify-center w-full mt-5">
                                <h2 class="text-base font-medium text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    {{ __('Uploaded by') }}</h2>
                                <div class="flex items-center justify-start gap-2 mt-2">
                                    <div class="w-[25px] h-[25px] rounded-full">
                                        <img src="{{ asset('assets/media/avatar/photo.jpg') }}" alt="Owner"
                                            class="w-full h-full rounded-full">
                                    </div>
                                    <span
                                        class="text-sm font-medium owner-name text-[#1f1f1f] dark:text-[#e3e3e3] opacity-80">
                                        {{ $file['createdBy']['user']['displayName'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col justify-center w-full p-3 pb-4">
                            <h2 class="text-base font-medium text-[#1f1f1f] dark:text-[#e3e3e3]">
                                {{ __('Details') }}</h2>
                            <div class="flex flex-col items-start justify-between gap-4 mt-3">
                                <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    <span class="text-xs font-semibold">{{ __('Drive type') }}</span>
                                    <span class="text-sm" id="drive_type">
                                        {{ $file['parentReference']['driveType'] }}
                                    </span>
                                </div>
                                <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    <span class="text-xs font-medium">{{ __('Location') }}</span>
                                    <a href="{{ route('home.folder', $file['parentReference']['id']) }}"
                                        class="text-sm py-1 px-2 border border-[#c7c7c7] dark:border-[#444746] rounded-md"
                                        id="drive_location">
                                        <i class="fa-solid fa-folder"></i>
                                        <span id="parent_name">
                                            {{ $file['parentReference']['name'] }}
                                        </span>
                                    </a>
                                </div>
                                <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    <span class="text-xs font-semibold">{{ __('Owner') }}</span>
                                    <span class="text-sm" id="owner">
                                        {{ $file['createdBy']['user']['displayName'] }}
                                    </span>
                                </div>
                                <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    <span class="text-xs font-semibold">{{ __('Last modified') }}</span>
                                    <div class="text-sm">
                                        <span id="last_modified">
                                            {{ $file['lastModifiedDateTime'] }}
                                        </span>
                                        {{ __('by') }}
                                        <span id="last_modified_by">
                                            {{ $file['lastModifiedBy']['user']['displayName'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    <span class="text-xs font-semibold">{{ __('Created at') }}</span>
                                    <span class="text-sm" id="created_at">
                                        {{ $file['createdDateTime'] }}
                                    </span>
                                </div>
                                <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    <span class="text-xs font-semibold">{{ __('Size') }}</span>
                                    <span class="text-sm" id="size">
                                        {{ $file['size'] }}
                                    </span>
                                </div>
                                <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    <span class="text-xs font-semibold">{{ __('Hash') }}</span>
                                    <span class="text-sm" id="hashes">
                                        {{ $file['file']['hashes']['quickXorHash'] }}
                                    </span>
                                </div>
                                <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    <span class="text-xs font-semibold">{{ __('Mimetype') }}</span>
                                    <span class="text-sm" id="mime_type">
                                        {{ $file['file']['mimeType'] }}
                                    </span>
                                </div>
                                @if (isset($file['image']))
                                    <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                        <span class="text-xs font-semibold">{{ __('Image') }}</span>
                                        <span class="text-sm" id="resolution">
                                            {{ $file['image']['width'] }} x {{ $file['image']['height'] }}
                                        </span>
                                    </div>
                                @endif
                                @if (isset($file['video']))
                                    <div class="flex flex-col gap-1 text-[#1f1f1f] dark:text-[#e3e3e3]">
                                        <span class="text-xs font-semibold">{{ __('Video') }}</span>
                                        <span class="text-sm" id="audioBitsPerSample">
                                            {{ __('Audio Bits Per Sample') }}: {{ $file['video']['audioBitsPerSample'] }}
                                        </span>
                                        <span class="text-sm" id="audioChannels">
                                            {{ __('Audio Channels') }}: {{ $file['video']['audioChannels'] }}
                                        </span>
                                        <span class="text-sm" id="audioFormat">
                                            {{ __('Audio Samples Per Second') }}:
                                            {{ $file['video']['audioSamplesPerSecond'] }}
                                        </span>
                                        <span class="text-sm" id="bitRate">
                                            {{ __('Bitrate') }}: {{ $file['video']['bitRate'] }}
                                        </span>
                                        <span class="text-sm" id="duration">
                                            {{ __('Duration') }}: {{ $file['video']['duration'] }}
                                        </span>
                                        <span class="text-sm" id="fourCC">
                                            {{ __('FourCC') }}: {{ $file['video']['fourCC'] }}
                                        </span>
                                        <span class="text-sm" id="frameRate">
                                            {{ __('Frame Rate') }}: {{ $file['video']['frameRate'] }}
                                        </span>
                                        <span class="text-sm" id="height">
                                            {{ __('Height') }}: {{ $file['video']['height'] }}
                                        </span>
                                        <span class="text-sm" id="width">
                                            {{ __('Width') }}: {{ $file['video']['width'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden max-w-full overflow-y-auto tab-pane" id="tab_2">
                    <div class="flex flex-col items-center justify-center grow default">
                        <img src="{{ asset('assets/media/svg/empty_state_details_v2.svg') }}" alt=""
                            class="w-[176px]">
                        <span id="info-text" class="text-[0.875rem]">
                            {{ __('Select an item to view details.') }}
                        </span>
                    </div>
                    <div class="items-center justify-center hidden loading-container grow min-h-[100px]">
                        <div class="loading"></div>
                    </div>
                    <div class="flex-col items-center justify-center hidden grow error">
                        <i class="fa-regular fa-circle-exclamation text-[48px] mt-5"></i>
                        <span class="text-[16px]">
                            {{ __('Something went wrong.') }}
                        </span>
                    </div>
                    <div class="flex-col hidden main grow text-[#c7c7c7] dark:text-[#444746]">
                        <!-- component -->
                        <div class="container p-5 mx-auto">
                            <!-- component -->
                            <div class="relative">
                                <div class="absolute top-0 h-full border-r-4 border-[#8f8f8f]"></div>
                                <ul class="p-0 m-0 list-none" id="timeline">
                                    <li class="hidden">
                                        <div class="flex items-center group ">
                                            <div
                                                class="z-10 w-5 h-5 bg-[#8f8f8f] border-4 border-[#8f8f8f] rounded-full group-hover:bg-white">
                                                <div class="items-center w-6 h-1 mt-1 ml-4 bg-[#8f8f8f]"></div>
                                            </div>
                                            <div class="z-10 flex-1 pb-3 ml-4">
                                                <div
                                                    class="order-1 p-3 space-y-2 rounded-lg shadow-only transition-ease bg-[#8f8f8f] max-w-[250px]">
                                                    <h4
                                                        class="mb-3 text-sm font-medium text-[#1f1f1f] text-ellipsis overflow-hidden">
                                                        Test
                                                    </h4>
                                                    <p class="m-0 text-xs font-normal text-[#444746]">
                                                        Test
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footerScripts')
    <script src="{{ asset('assets/js/home/file.js') }}" type="module"></script>
@endpush
