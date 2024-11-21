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
                                ${item['folder'] ? '<i class="fa-solid fa-folder text-[#f0b429] text-[22px]"></i>' : '<i class="fa-solid ' + item['icon'] + ' text-[22px]"></i>'}
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


let items = $('.item-row');

items.each(function (index, item) {
    $(item).click(function () {
        let id = $(this).data('id');
        getInfo(id);
    });
});

let tab1 = $('.tab-data #tab_1');
let tab1Default = tab1.find('.default');
let tab1Loading = tab1.find('.loading-container');
let tab1Main = tab1.find('.main');
let tab2 = $('.tab-data #tab_2');
function getInfo(id) {
    hideItemFlex(tab1Default);
    showItemFlex(tab1Loading);
    $.ajax({
        url: '/info/' + id,
        type: 'GET',
        data: {
            id: id
        },
        success: function (response) {
            let objectLength = Object.keys(response).length;
            if (objectLength > 0) {
                tab1Main.find('.preview').attr('src', response['thumbnail']);
                tab1Main.find('.owner-name').text(response['createdBy']['user']['displayName']);
            }
            hideItemFlex(tab1Loading);
            showItemFlex(tab1Main);
        },
        error: function (error) {
        },
        complete: function () {
        }
    });
}

function showItemFlex(item) {
    if (item.hasClass('hidden')) {
        item.removeClass('hidden').addClass('flex');
    }
}

function hideItemFlex(item) {
    if (item.hasClass('flex')) {
        item.removeClass('flex').addClass('hidden');
    }
}
