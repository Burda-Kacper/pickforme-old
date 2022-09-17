$(".administrator-champion-search").on('keyup', function () {
    searchChampionByName($(this).val());
});

function searchChampionByName(name) {
    $(".administrator-champion-row").each(function () {
        if ($(this).data('champion-name').toLowerCase().includes(name.toLowerCase())) {
            $(this).removeClass("hidden");

            return;
        }

        $(this).addClass("hidden");
    });
}

$(".administrator-champion-row").on('click', function () {
    $.ajax({
        url: administratorChampionsDetailsPath + "/" + $(this).data('champion-id'),
        method: "POST"
    }).done(function (data) {
        if (data.success) {
            $('.administrator-champions-details-container').html(data.view).removeClass("hidden");
            $(".administrator-champion-search").val('');
            searchChampionByName('');
            $(".administrator-champions-container").addClass("hidden");
            $(".administrator-champion-search-container").addClass("hidden");
        } else {
            popupOpen(translations['administrator.champions.error'], translations[data.message]);
        }
    });
});

$(".administrator-champions-details-container").on('click', '.administrator-champions-details-back', function () {
    $('.administrator-champions-details-container').html('').addClass("hidden");
    $(".administrator-champions-container").removeClass("hidden");
    $(".administrator-champion-search-container").removeClass("hidden");
});

$(".administrator-champions-details-container").on('click', '.administrator-champions-details-tags-row', function () {
    $(this).toggleClass("active");
});

$(".administrator-champions-details-container").on('click', '.administrator-champions-details-save', function () {
    $.ajax({
        url: administratorChampionsDetailsSavePath,
        method: "POST",
        data: {
            championId: $(".administrator-champions-details-inner-container").data('champion-id'),
            tagsIds: generateActiveTagsIds()
        }
    }).done(function (data) {
        if (data.success) {
            popupOpen(translations['administrator.champions.saved'], translations[data.message]);
        } else {
            popupOpen(translations['administrator.champions.error'], translations[data.message]);
        }
    });
});

function generateActiveTagsIds() {
    var output = [];

    $(".administrator-champions-details-tags-row.active").each(function () {
        output.push($(this).data('tag-id'));
    })

    return output;
}

