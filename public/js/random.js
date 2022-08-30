var randomConfig = null;

function initRandomConfig() {
    randomConfig = {
        activeTiles: null,
        initialTiles: null,
        decayedAmount: 1,
        stopDecay: false,
        resultDelay: 1200
    };
}


$(".homepage-button-random").on('click', function () {
    if ($(this).data('blocked') === 'true') {
        return;
    }

    $(this).data('blocked', "true");
    slideOutHomepage();

    $.ajax({
        url: randomPath,
        method: "POST"
    }).done(function (data) {
        if (data.success) {
            $('.homepage-content').html(data.view);

            randomConfig.initialTiles = $('.random-image').length;
            let loadedImages = 0;

            $('.random-image').on('load', function () {
                loadedImages++;

                if (randomConfig.initialTiles === loadedImages) {
                    setTimeout(function () {
                        $(".homepage-content").removeClass("hidden");
                    }, 1000);

                    setTimeout(function () {
                        randomConfig.activeTiles = $(".random-champion.active").length;
                        randomDecay();
                    }, 2500);
                }
            });
        } else {
            backToHomepage();
        }
    });
})

function randomDecay() {
    var decayDelay = 0;

    if (randomConfig.stopDecay) {
        $('.random-champion').addClass('random-decayed-instant');
        randomConfig.decayedAmount = randomConfig.initialTiles;
    }

    for (let iteration = 0; iteration < randomConfig.decayedAmount; iteration++) {
        let randomPicked = Math.floor(Math.random() * randomConfig.activeTiles);
        $('.random-champion.active').eq(randomPicked).removeClass('active').addClass("random-decayed").addClass('random-decayed-delay-' + decayDelay);
        decayDelay++;
        randomConfig.activeTiles--;

        if (randomConfig.activeTiles <= 1) {
            setTimeout(function () {
                displayRandomResult();
            }, randomConfig.resultDelay);

            break;
        }
    }

    if (randomConfig.activeTiles < 90) {
        randomConfig.decayedAmount = 4;
    } else if (randomConfig.activeTiles < 110) {
        randomConfig.decayedAmount = 3;
    } else if (randomConfig.activeTiles < 150) {
        randomConfig.decayedAmount = 2;
    }

    if (randomConfig.activeTiles > 1) {
        setTimeout(function () {
            randomDecay();
        }, 250);
    }
}

function displayRandomResult() {
    $(".random-result-image").on('load', function () {
        $(".random-result-container").removeClass("hidden");
        $(".random-button-skip").addClass('hidden');
        $(".random-button-back").removeClass("hidden");

        setTimeout(function () {
            $(".random-result-image").addClass('random-result-image-after');
            $(".random-result-name").addClass('random-result-name-after');
        }, 500);
    })

    $(".random-result-image").attr('src', $('.random-champion.active').data('result'));
    $(".random-result-name").text($('.random-champion.active').data('name'));
}

$(".homepage-container").on('click', '.random-button-skip', function () {
    randomConfig.stopDecay = true;
    randomConfig.resultDelay = 500;
});

$(".homepage-container").on('click', '.random-button-back', function () {
    initRandomConfig();
    backToHomepage();
});
