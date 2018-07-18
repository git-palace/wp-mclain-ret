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

	$('.slick-slider').on('click', '.slick-slide', function (e) {
		e.stopPropagation();
		
		var index = $(this).attr("data-slick-index");

		if ($('.slick-slider').slick('slickCurrentSlide') !== index) {
  			$('.slick-slider').slick('slickGoTo', index);
		}
	});
})(jQuery);