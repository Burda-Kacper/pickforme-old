var battleConfig = null;

function initBattleConfig() {
    battleConfig = {
        activeTiles: null,
        initialTiles: null
    };
}

function initBattleRound() {
    if ($(".battle-champions .battle-champion").length <= 8) {
        initFinalRound();

        return;
    }

    $('.battle-arena').removeClass("battle-in").removeClass('battle-out').removeClass('blocked');
    $(".battle-container").removeClass('battle-fade').addClass('battle-fade-in');
    $(".battle-background").removeClass('hidden');

    for (let index = 0; index < 8; index++) {
        let championsLeft = $(".battle-champions .battle-champion").length;
        let randomIndex = Math.floor(Math.random() * championsLeft);
        $(".battle-left").append($(".battle-champions .battle-champion").eq(randomIndex));
    }

    for (let index = 0; index < 8; index++) {
        let championsLeft = $(".battle-champions .battle-champion").length;
        let randomIndex = Math.floor(Math.random() * championsLeft);
        $(".battle-right").append($(".battle-champions .battle-champion").eq(randomIndex));
    }
}

function initFinalRound() {
    $(".battle-container").addClass('hidden');
    $(".battle-hint").addClass("hidden");
    $(".battle-hint-final").removeClass('hidden');
    $('.battle-final-container').removeClass("hidden").append($(".battle-champion"));
    $(".battle-champion").addClass('battle-champion-final');
}

$(".homepage-button-battle").on('click', function () {
    if ($(this).data('blocked') === 'true') {
        return;
    }

    $(".homepage-button").data('blocked', 'true');
    slideOutHomepage();

    $.ajax({
        url: battlePath,
        method: "POST"
    }).done(function (data) {
        if (data.success) {
            $('.homepage-content').html(data.view);

            battleConfig.initialTiles = $('.battle-image').length;
            let loadedImages = 0;

            $('.battle-image').on('load', function () {
                loadedImages++;

                if (battleConfig.initialTiles === loadedImages) {
                    setTimeout(function () {
                        $(".homepage-content").removeClass("hidden");
                        initBattleRound();
                    }, 1000);
                }
            });
        } else {
            backToHomepage();
        }
    });
})

$(".homepage-container").on('click', '.battle-button-back', function () {
    initBattleConfig();
    backToHomepage();
});


$('.homepage-container').on('click', '.battle-arena', function () {
    if ($(this).hasClass('blocked')) {
        return;
    }

    $(this).addClass('active');

    $(".battle-arena").each(function () {
        var that = $(this);
        that.addClass('blocked');
        that.find('.battle-background').addClass("hidden");

        if (that.hasClass("active")) {
            that.addClass('battle-in');
            that.removeClass('active');
        } else {
            that.addClass('battle-out');
        }
    });

    setTimeout(function () {
        $(".battle-container").removeClass('battle-fade-in').addClass('battle-fade');
    }, 2000);

    setTimeout(function () {
        $(".battle-champions").append($(".battle-in .battle-champion"));
        $('.battle-out').find(".battle-champion").remove();
        initBattleRound();
    }, 4000);
});

$('.homepage-container').on('click', '.battle-champion-final', function () {
    if ($(this).hasClass('blocked') || $(this).hasClass("battle-champion-out")) {
        return;
    }

    $(".battle-champion-final").addClass("blocked");
    $(this).addClass('battle-champion-out');
    var waitTime = Math.floor((Math.random() * 1250) + 1250);

    setTimeout(function () {
        finalAIRemoveChampion();
    }, waitTime);
});

function finalAIRemoveChampion() {
    let championsLeft = $(".battle-champion-final").length;
    let randomIndex = Math.floor(Math.random() * championsLeft);
    let chosenChampion = $(".battle-champion-final").eq(randomIndex);

    if (chosenChampion.hasClass("battle-champion-out")) {
        finalAIRemoveChampion();
    } else {
        chosenChampion.addClass("battle-champion-out");
        let outChampions = $(".battle-champion-out").length;
        if (championsLeft - outChampions > 2) {
            $(".battle-champion-final").removeClass('blocked');
        } else {
            setTimeout(function () {
                initLastRound();
            }, 1000);
        }
    }
}

function initLastRound() {
    $(".battle-final-container").addClass('battle-fade');

    setTimeout(function () {
        $(".battle-champion-final").each(function () {
            if ($(this).hasClass('battle-champion-out')) {
                return;
            }

            $('.battle-last-container').append($(this));
        });

        $(".battle-final-container").addClass('hidden');
        $(".battle-last-container").removeClass("hidden");
        $(".battle-hint").addClass('hidden');
        $(".battle-hint-last").removeClass("hidden");
    }, 2000);

    setTimeout(function () {
        $(".battle-last-cursor").removeClass("hidden");
        let flashCount = Math.floor(Math.random() * 10) + 11;
        flashCursor(flashCount);
    }, 3000);
}

function flashCursor(flashCount) {
    let waitTime = 1000;
    let totalTime = 0;

    for (let flash = 0; flash < flashCount; flash++) {
        totalTime = totalTime + waitTime;

        if (waitTime > 500) {
            waitTime = waitTime - 100;
        } else if (waitTime > 200) {
            waitTime = waitTime - 75;
        } else if (waitTime > 100) {
            waitTime = waitTime - 50;
        } else {
            waitTime = 50;
        }

        setTimeout(function () {
            $(".battle-last-cursor").toggleClass('reversed');
        }, totalTime);
    }

    setTimeout(function () {
        initFinallyFinalFinish();
    }, totalTime + 1000);
}

function initFinallyFinalFinish() {
    $(".battle-hint").addClass("battle-fade");
    $(".battle-last-container").addClass("battle-fade");

    let championImage, championName;

    if ($(".battle-last-cursor").hasClass('reversed')) {
        championImage = $(".battle-last-container .battle-champion").eq(1).data('result');
        championName = $(".battle-last-container .battle-champion").eq(1).data('name');
    } else {
        championImage = $(".battle-last-container .battle-champion").eq(0).data('result');
        championName = $(".battle-last-container .battle-champion").eq(0).data('name');
    }

    $(".battle-tada-img").attr('src', championImage);
    $(".battle-tada-name").text(championName);

    setTimeout(function () {
        $(".battle-last-container").addClass('hidden');
        $(".battle-tada").removeClass("hidden");
    }, 2000)
}