(function($) {
	$("form#mclain-rets-config").submit(function(e) {
		e.preventDefault();

		var formData = new FormData($(this)[0]);

		$.ajax({
			type: "post",
			url: "/wp-json/mclain-rets/update-config",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (data) {
				console.log(data);
			}
		})
	});
})(jQuery);