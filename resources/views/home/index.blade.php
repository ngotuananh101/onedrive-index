@extends('layouts.home')

@section('title', __('Home'))

@section('content')
    <div class="h-full overflow-x-auto">
        <table class="table table-sm table-pin-rows text-[#e3e3e3]">
            <thead class="text-[#e3e3e3]">
                <tr class="dark:bg-[#131314] bg-white text-[#1f1f1f] dark:text-[#e3e3e3]">
                    <td class="w-full max-w-full lg:w-[50%] lg:max-w-[50%]">{{ __('Name') }}</td>
                    <td class="hidden lg:table-cell">{{ __('Owner') }}</td>
                    <td class="hidden lg:table-cell">{{ __('Last Updated At') }}</td>
                    <td class="hidden lg:table-cell">{{ __('Size') }}</td>
                    <th></th>
                </tr>
            </thead>
            <tbody class="">
                @foreach ($data as $d)
                    <tr class="font-light text-[#1f1f1f] dark:text-[#e3e3e3]">
                        <td>
                            <a href="{{ route('home.index') }}" class="flex items-center gap-2">
                                <i class="fa-solid fa-folder text-[#f0b429]"></i>
                                <span class="font-medium">{{ $d['name'] }}</span>
                            </a>
                        </td>
                        <td class="hidden lg:table-cell">
                            @if ($d['owner'])
                                <div class="flex items-center avatar">
                                    <div class="w-[25px] h-[25px] rounded-full">
                                        <img src="{{ $d['owner']['photo'] }}" />
                                    </div>
                                    <span class="ml-2">{{ $d['owner']['displayName'] }}</span>
                                </div>
                            @else
                                <span class="text-[#f0b429]">{{ __('You') }}</span>
                            @endif
                        </td>
                        <td class="hidden lg:table-cell">{{ $d['last_modified'] }}</td>
                        <td class="hidden lg:table-cell">{{ $d['size'] }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button"
                                        class="flex items-center justify-center w-[30px] h-[30px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                                        <i class="fa-regular fa-ellipsis-vertical text-[15px]"></i>
                                    </div>
                                    <ul tabindex="0"
                                        class="dropdown-content menu bg-white dark:bg-[#1e1f20] rounded-box z-[1] w-52 p-2 shadow">
                                        @if ($d['download'])
                                            <li>
                                                <a href="{{ $d['download'] }}" target="_blank">{{ __('Download') }}</a>
                                            </li>
                                        @endif
                                        <li><a>Item 2</a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
