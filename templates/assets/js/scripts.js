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

	$('.slick-slide').on('click', function (e) {
		e.stopPropagation();
		
		var index = $(this).attr("data-slick-index");

		if ($('.slick-slider').slick('slickCurrentSlide') !== index) {
  			$('.slick-slider').slick('slickGoTo', index);
		}
	});

	// hit enter in search form
	$('form.simple-search-form input').keyup( function(e) {
		if(e.keyCode == 13)
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

	$('.searched-results .carousel-control').click(function(e){
    e.preventDefault();
    let selector = $(this).attr("href");
    $(selector).carousel( $(this).data() );
  });
})(jQuery);