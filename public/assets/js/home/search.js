let searchModal = $('#search');
let searchInput = searchModal.find('.search-input');
let searchDefault = searchModal.find('.default');
let searchLoading = searchModal.find('.loading-container');
let noResults = searchModal.find('.no-result');
let searchResults = searchModal.find('.result-container');
let searchResultFolder = searchResults.find('.result-list-folder');
let searchResultFile = searchResults.find('.result-list-file');
let searchInterval;

searchDefault.show();
// searchDefault.hide();
searchLoading.hide();
noResults.hide();
searchResults.hide();
// searchResults.show();
searchResults.empty();

searchInput.on('input', function () {
    clearTimeout(searchInterval);
    searchInterval = setTimeout(function () {
        let query = searchInput.val();
        if (query.length > 0) {
            searchDefault.hide();
            searchLoading.show();
            noResults.hide();
            searchResults.hide();
            searchResults.empty();
            search(query);
        }
    }, 500);
});

function search(query) {
    $.ajax({
        url: '/search',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            query: query
        },
        success: function (data) {
            if (data.total > 0) {
                let results = data.data_result;
                for (let i = 0; i < results.length; i++) {
                    let item = results[i];
                    let html = `<a href="${item.link}"
                        class="flex items-center justify-start p-3 border-b-[1px] border-b-[#e3e3e3] dark:border-b-[#e3e3e326] w-full">
                        <i class="fa-solid ${item['icon']} text-[18px] mr-3"></i>
                        <div class="item grow">
                            <h5 class="text-base font-semibold truncate w-[500px] max-w-[70vw]">${item['name']}</h5>
                            <div class="flex justify-between text-xs text-[#444746] dark:text-[#c4c7c5] flex-col md:flex-row">
                                <span class="truncate max-w-[45%]">
                                ${item['path']}
                                </span>
                                <span class="max-w-[45%]">
                                ${item['size']}
                                </span>
                            </div>
                        </div>
                    </a>`;
                    searchResults.append(html);
                }
                searchResults.show();
            } else {
                noResults.show();
            }
        }, error: function (data) {
            noResults.show();
        }, complete: function (data) {
            searchLoading.hide();
        }
    });
}
