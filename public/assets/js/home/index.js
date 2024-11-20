// Handle scroll events
let content = $('#content-scroll');
let isLoading = false;
content.scroll(function () {
    let scroll = content.scrollTop();
    let height = content.height();
    let scrollHeight = content[0].scrollHeight;
    let scrollBottom = scroll + height;
    let scrollBottomOffset = 100;
    let nextPage = content.data('nextPage');
    if (scrollBottom + scrollBottomOffset >= scrollHeight && !isLoading && nextPage) {
        isLoading = true;
        $('#loading').removeClass('hidden');
        $.ajax({
            url: '/next-page',
            type: 'GET',
            data: {
                next_url: nextPage
            },
            success: function (response) {
                let items = response.data;
                let nextPage = response.next_url;
                let html = '';
                items.forEach(function (item) {
                    html += `<tr class="font-light text-[#1f1f1f] dark:text-[#e3e3e3]">
                        <td class="text-[16px]">
                            <a href="${item['link']}" class="flex items-center gap-2">
                                ${item['folder'] ? '<i class="fa-solid fa-folder text-[#f0b429] text-[25px]"></i>' : '<i class="fa-solid ' + item['icon'] + ' text-[25px]"></i>'}
                                <span class="font-medium">${item['name']}</span>
                            </a>
                        </td>
                        <td class="hidden lg:table-cell">
                            ${item['owner']}
                        </td>
                        <td class="hidden lg:table-cell">
                            ${item['lastModifiedDateTime']}
                        </td>
                        <td class="hidden lg:table-cell">
                            ${item['size']}
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button"
                                        class="flex items-center justify-center w-[30px] h-[30px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                                        <i class="fa-regular fa-ellipsis-vertical text-[15px]"></i>
                                    </div>
                                    <ul tabindex="0"
                                        class="dropdown-content menu bg-white dark:bg-[#1e1f20] rounded-box z-[1] w-52 p-2 shadow">
                                        ${item['downloadBtn'] ? '<li>' + item['downloadBtn'] + '</li>' : ''}
                                        <li>
                                        ${item['viewBtn']}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>`;
                });
                $('#data').append(html);
                content.data('nextPage', nextPage);
            },
            error: function (error) {
            },
            complete: function () {
                isLoading = false;
                $('#loading').addClass('hidden');
            }
        });

    }
});
