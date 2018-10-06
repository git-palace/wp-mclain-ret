(function($) {
    $('.property-slide').slick({
        centerMode: true,
        centerPadding: '0px',
        slidesToShow: 3,
        responsive: [{
            breakpoint: 768,
            settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 3
            }
        }, {
            breakpoint: 480,
            settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 1
            }
        }]
    });

    $('.slick-slide').on('click', function(e) {
        e.stopPropagation();

        var index = $(this).attr("data-slick-index");

        if ($('.slick-slider').slick('slickCurrentSlide') !== index) {
            $('.slick-slider').slick('slickGoTo', index);
        }
    });

    // hit enter in search form
    $('form.simple-search-form input').keyup(function(e) {
        if (e.keyCode == 13)
            $('form.simple-search-form').submit();
    });

    // click search button in the form
    $('form.simple-search-form #search-btn').on('click', function() {
        $('form.simple-search-form').submit();
    })

    // hook when form is submitted
    $('form.simple-search-form').submit(function(e) {
        e.preventDefault();

        let keyword = $('form.simple-search-form input').val();

        if (keyword && keyword.length)
            window.location.href = "search-results/" + keyword;
    });

    $('.searched-results .carousel-control').click(function(e) {
        e.preventDefault();
        let selector = $(this).attr("data-target");
        $(selector).carousel($(this).data());
    });

    $("h1.sr-title a").click(function() {
        let criteria = $(this).attr("keyword");

        $.post(
            ajax_obj.url, {
                'action': 'save_search_criteria',
                'criteria': criteria
            },
            function(data) {
                if ($("h1.sr-title a").hasClass('saved'))
                    $("h1.sr-title a").removeClass('saved');
                else
                    $("h1.sr-title a").addClass('saved');
            }
        );
    });

    $("table.keywords td a.delete").click(function(e) { keywordDelete(e) });

    var keywordDelete = function(e) {
        let index = $(e.target).attr('k-index');
        let criteria = $(e.target).attr('keyword');

        $.post(
            ajax_obj.url, {
                'action': 'delete_key_word',
                'c_idx': index,
                'criteria': criteria
            },
            function(data) {
                console.log(data);
                let res = JSON.parse(data);

                if (res.failed)
                    $("table.keywords tbody").html('')
                else
                    $("table.keywords tbody").html(res.html);

                $("table.keywords td a.delete").click(function(e) { keywordDelete(e) });
            }
        )
    }

    $("#change-password form").submit(function(e) {

        let oldPWD = $("form input#oldPWD").val();
        let newPWD = $("form input#newPWD").val();

        if (!oldPWD || !newPWD)
            alert("Type Passwords!");
        else {
            $.post(
                ajax_obj.url, {
                    'action': 'change_password',
                    'oldPWD': oldPWD,
                    'newPWD': newPWD
                },
                function(data) {
                    let res = JSON.parse(data);

                    if (res.failed) {
                        alert(res.msg);
                    } else {
                        alert("Changed Successfully!");
                    }
                }
            );
        }
        e.preventDefault();
    });
})(jQuery);