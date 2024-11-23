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
                    html += `<tr class="font-light text-[#1f1f1f] dark:text-[#e3e3e3] border-b-[#c7c7c7] dark:border-b-[#444746]">
                        <td class="text-[14px]">
                            <a href="${item['link']}" class="flex items-center gap-2 item-name">
                                ${item['folder'] ? '<i class="fa-solid fa-folder text-[#f0b429] text-[22px] item-name"></i>' : '<i class="fa-solid ' + item['icon'] + ' text-[22px] item-name"></i>'}
                                <span class="font-semibold item-name">${item['name']}</span>
                            </a>
                        </td>
                        <td class="hidden font-medium lg:table-cell">
                            ${item['owner']}
                        </td>
                        <td class="hidden font-medium lg:table-cell">
                            ${item['lastModifiedDateTime']}
                        </td>
                        <td class="hidden font-medium lg:table-cell">
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
    $(item).click(function (e) {
        let id = $(this).data('id');
        // check target do not have class item-name
        console.log(e.target);

        if (!$(e.target).hasClass('item-name')) {
            getInfo(id);
        }
    });
});

let tab1 = $('.tab-data #tab_1');
let tab1Default = tab1.find('.default');
let tab1Loading = tab1.find('.loading-container');
let tab1Error = tab1.find('.error');
let tab1Main = tab1.find('.main');
let tab2 = $('.tab-data #tab_2');
let tab2Default = tab2.find('.default');
let tab2Loading = tab2.find('.loading-container');
let tab2Error = tab2.find('.error');
let tab2Main = tab2.find('.main');
let tab2Timeline = tab2.find('#timeline');

let currentId = null;
function getInfo(id) {
    currentId = id;
    // set tab active is first tab
    setTabActive($('.tab-title').first().trigger('click'));
    hideItemFlex(tab1Default);
    showItemFlex(tab1Loading);
    hideItemFlex(tab1Error);
    hideItemFlex(tab1Main);
    $.ajax({
        url: '/info/' + id,
        type: 'GET',
        success: function (response) {
            let objectLength = Object.keys(response).length;
            if (objectLength > 0) {
                $('#sidebar-title').text(response['name']);
                tab1Main.find('.preview').attr('src', response['thumbnail']);
                tab1Main.find('.owner-name').text(response['createdBy']['user']['displayName']);
                tab1Main.find('#drive_type').text(response['parentReference']['driveType']);
                tab1Main.find('#drive_location').attr('href', "/folder/" + response['parentReference']['id']);
                tab1Main.find('#parent_name').text(response['parentReference']['name']);
                tab1Main.find('#owner').text(response['createdBy']['user']['displayName']);
                tab1Main.find('#last_modified').text(response['lastModifiedDateTime']);
                tab1Main.find('#last_modified_by').text(response['lastModifiedBy']['user']['displayName']);
                tab1Main.find('#created_at').text(response['createdDateTime']);
                tab1Main.find('#size').text(response['size']);
            }
            showItemFlex(tab1Main);
        },
        error: function (error) {
            showItemFlex(tab1Error);
        },
        complete: function () {
            hideItemFlex(tab1Loading);
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

function setTabActive(tab) {
    $('.tab-title').removeClass('active');
    tab.addClass('active');
    $('.tab-pane').each(function () {
        hideItemFlex($(this));
    });
    showItemFlex($('#' + tab.attr('data-target-id')));
}

$('.tab-title').on('click', function () {
    setTabActive($(this));
    // if data-target-id="tab_2" is set, then get activity
    $(this).attr('data-target-id') == 'tab_2' ? getActivity(currentId) : '';
});

function getActivity(id) {
    if (id) {
        hideItemFlex(tab2Default);
        showItemFlex(tab2Loading);
        hideItemFlex(tab2Error);
        hideItemFlex(tab2Main);
        if (id) {
            tab2Timeline.html('');
            $.ajax({
                url: '/activity/' + id,
                type: 'GET',
                success: function (data) {
                    data.forEach(function (item) {
                        let html = `<li class="mb-5 ">
                                    <div class="flex items-center group ">
                                        <div
                                            class="z-10 w-5 h-5 bg-[#8f8f8f] border-4 border-[#8f8f8f] rounded-full group-hover:bg-white">
                                            <div class="items-center w-6 h-1 mt-1 ml-4 bg-[#8f8f8f]"></div>
                                        </div>
                                        <div class="z-10 flex-1 ml-4 font-medium">
                                            <div
                                                class="order-1 p-3 space-y-2 rounded-lg shadow-only transition-ease bg-[#8f8f8f]" style="max-width: 222px;">
                                                <h4 class="mb-3 text-sm font-medium text-[#1f1f1f] text-ellipsis overflow-hidden">
                                                    ${item.action}
                                                </h4>
                                                <p class="text-xs text-[#444746] m-0 font-normal">
                                                    ${item.time}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>`;
                        tab2Timeline.append(html);
                    });
                    showItemFlex(tab2Main);
                },
                error: function (data) {
                    showItemFlex(tab2Error);
                },
                complete: function () {
                    hideItemFlex(tab2Loading);
                }
            });
        }
    }
}
