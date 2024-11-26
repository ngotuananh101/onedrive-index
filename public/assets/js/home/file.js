let tab1Btn = $('span[data-target-id="tab_1"]');
let tab2Btn = $('span[data-target-id="tab_2"]');

let tab2 = $('.tab-data #tab_2');
let tab2Default = tab2.find('.default');
let tab2Loading = tab2.find('.loading-container');
let tab2Error = tab2.find('.error');
let tab2Main = tab2.find('.main');
let tab2Timeline = tab2.find('#timeline');
let isLoaded = false;

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

tab2Btn.click(function (e) {
    if (!isLoaded) {
        isLoaded = true;
        getActivity(e.target.dataset.id);
    }
    setTabActive(tab2Btn);
});

tab1Btn.click(function (e) {
    setTabActive(tab1Btn);
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
                                        <div class="z-10 flex-1 ml-4 font-medium pb-3">
                                            <div
                                                class="order-1 p-3 space-y-2 rounded-lg shadow-only transition-ease bg-[#8f8f8f] max-w-[259px]">
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
