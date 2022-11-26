var chooseConfig = null;

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

$(".homepage-container").on('click', '.choose-tag', function () {
    if ($('.choose-submit').data('locked')) {
        return;
    }

    const pointsState = chooseConfig.points;
    const previousActive = $(this).parent().find(".choose-tag.active");

    if (previousActive.length) {
        chooseConfig.points += previousActive.data('cost');
    }

    if (chooseConfig.points - $(this).data('cost') >= 0) {
        $(this).parent().find(".choose-tag").removeClass('active');
        $(this).addClass("active");
        chooseConfig.points -= $(this).data('cost');
        updatePointsCounter();

        return;
    }

    chooseConfig.points = pointsState;
    popupOpen(translations['choose.popup.error'], translations['choose.popup.too-little-points']);
});

$(".homepage-container").on('click', '.choose-submit', function () {
    if ($(this).data('locked')) {
        return;
    }

    $(this).data('locked', 'true');

    if ($(".choose-tag.active").length < 4) {
        popupOpen(translations['choose.popup.error'], translations['choose.popup.select-all-groups']);

        return;
    }

    let submitTags = [];

    $(".choose-tag.active").each(function () {
        submitTags.push($(this).data('id'));
    });

    $(".choose-wrapper").addClass("choose-wrapper-out");
    setTimeout(function () {
        $(".choose-wrapper").addClass('hidden');
        $(".choose-loading").removeClass("hidden");
        $(".choose-loading").addClass("choose-loading-in");
    }, 2000);

    setTimeout(function () {
        $(".choose-loading").addClass("choose-loading-ready");
    }, 5750)

    let chooseReadyInterval = null;

    $.ajax({
        url: chooseSubmitPath,
        method: "POST",
        data: {
            tags: submitTags
        }
    }).done(function (data) {
        const imageUrl = chooseImagePlaceholder.replace("REPLACEME", data.champion.codename);
        $(".choose-result-img").attr('src', imageUrl);
        $(".choose-result-name").text(data.champion.name);

        chooseReadyInterval = setInterval(function () {
            if ($(".choose-loading").hasClass("choose-loading-ready")) {
                $(".choose-loading").removeClass("choose-loading-in").addClass("choose-loading-out");

                setTimeout(function () {
                    $(".choose-loading").addClass("hidden");
                    $(".choose-result").removeClass("hidden");
                }, 1500);

                clearInterval(chooseReadyInterval);
            }
        }, 500);
    });
});

function initChooseConfig() {
    chooseConfig = {
        points: 8
    };

    updatePointsCounter(translations['choose.']);
}

function updatePointsCounter() {
    $(".choose-points").text(chooseConfig.points);
}

$(".homepage-container").on('click', '.choose-button-back', function () {
    backToHomepage();
});