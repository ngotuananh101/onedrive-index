let loginUrlElement = $("#loginUrl");
loginUrlElement.on("click", function () {
    window.open(loginUrlElement.text());
});

function resize(obj) {
    let viewWidth = $(window).width();
    if (viewWidth >= 1024) {
        let wrapper = $("#wrapper");
        let leftSide = $("#left");
        let rightSide = $("#right");
        // set right side width = 100% - left side width
        rightSide.width(wrapper.width() - leftSide.width() - 40);
    } else {
        $("#right").width("100%");
    }
}

$(document).ready(function () {
    resize();
});

$(window).resize(function () {
    resize();
});
