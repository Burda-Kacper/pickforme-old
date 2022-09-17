$(".popup-container").on('click', '.popup-close', function () {
    popupClose($(this).closest('.popup-entry'))
});

function popupOpen(title, content) {
    var popup = $(`       
       <div class="popup-entry popup-fade-in">
            <div class="popup-top">
                <p class="popup-title">` + title + `</p>
                <div class="popup-close"><i class="fa-solid fa-xmark"></i></div>
            </div>
            <p class="popup-content">` + content + `</p>
        </div>
    `)

    $(".popup-container").append(popup);

    setTimeout(function () {
        popupClose(popup);
    }, 7500);
}

function popupClose(popup) {
    popup.removeClass("popup-fade-in").addClass("popup-fade-out");

    setTimeout(function () {
        popup.remove();
    }, 1000);
}