<!-- You can open the modal using ID.showModal() method -->
<button onclick="search.showModal()"
    class="flex items-center justify-center w-[40px] h-[40px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
    <i class="fa-solid fa-magnifying-glass text-[20px]"></i>
</button>
<dialog id="search" class="modal">
    <div
        class="p-0 modal-box min-h-[30vh] w-[600px] max-w-[90vw] dark:bg-[#131314] bg-white border-[1px] border-[#e3e3e3] dark:border-[#e3e3e326]">
        <div
            class="flex items-center justify-between header border-b-[1px] border-b-[#e3e3e3] dark:border-b-[#e3e3e326] pl-3 pr-1 py-2">
            <i class="fa-solid fa-magnifying-glass text-[16px] mr-2"></i>
            <input type="text" placeholder="{{ __('Find in drive') }}"
                class="w-full bg-transparent border-0 outline-none grow text-[14px] search-input" />
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost">✕</button>
            </form>
        </div>
        <div class="result">
            <div class="flex flex-col items-center justify-center grow default">
                <img src="{{ asset('assets/media/svg/empty_state_details_v2.svg') }}" alt="" class="w-[176px]">
                <span id="info-text" class="text-[0.875rem]">
                    {{ __('Enter keywords to start search') }}
                </span>
            </div>
            <div class="items-center justify-center flex loading-container grow min-h-[100px]">
                <div class="loading"></div>
            </div>
            <div class="items-center justify-center flex no-result grow min-h-[100px]">
                <span class="text-[0.875rem]">
                    {{ __('No results found') }}
                </span>
            </div>
            <div class="min-h-[100px] max-h-[50vh] flex grow">
                <div
                    class="items-start justify-start flex flex-col result-container grow overflow-y-auto overflow-x-hidden text-[#1f1f1f] dark:text-[#e3e3e3]">
                    <a href="${item.link}"
                        class="flex items-center justify-start p-3 border-b-[1px] border-b-[#e3e3e3] dark:border-b-[#e3e3e326] w-full">
                        <i class="fa-solid ${item['icon']} text-[18px] mr-3"></i>
                        <div class="item grow">
                            <h5 class="text-base font-semibold truncate w-[500px] max-w-[70vw]">${item['name']}</h5>
                            <div
                                class="flex justify-between text-xs text-[#444746] dark:text-[#c4c7c5] flex-col md:flex-row">
                                <span class="truncate max-w-[45%]">
                                    ${item['path']}
                                </span>
                                <span class="max-w-[45%]">
                                    ${item['size']}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
