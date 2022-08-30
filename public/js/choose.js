$(".homepage-button-choose").on('click', function () {
    slideOutHomepage();

    $.ajax({
        url: choosePath,
        method: "POST"
    }).done(function (data) {
        if (data.success) {
            $('.homepage-content').html(data.view);

            setTimeout(function () {
                $(".homepage-content").removeClass("hidden");
            }, 1000);
        } else {
            backToHomepage();
        }
    });
})