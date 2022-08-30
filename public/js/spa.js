function backToHomepage() {
    $(".homepage-content").removeClass('anim-slide-in-right').addClass("anim-slide-out-right");

    setTimeout(function () {
        $('.homepage-content').addClass("hidden").html("");
        $(".homepage-base").removeClass("anim-slide-out-left").removeClass('hidden').addClass("anim-slide-in-left");
        $(".homepage-button-random").data('blocked', null);
    }, 1000);
}

function slideOutHomepage() {
    $(".homepage-base").removeClass("anim-slide-in-left").addClass("anim-slide-out-left");
    $(".homepage-header").removeClass("anim-slide-in-up");
    $(".homepage-intro").removeClass("anim-slide-in-up").removeClass("anim-delay-04");
    $(".homepage-button-container").removeClass("anim-slide-in-up").removeClass("anim-delay-08");
    $(".homepage-content").removeClass("anim-slide-out-right").addClass("anim-slide-in-right");

    setTimeout(function () {
        $(".homepage-base").addClass("hidden");
    }, 1000);
}