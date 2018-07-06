(function($) {
	$("form#sandicor-config").submit(function(e) {
		e.preventDefault();

		var formData = new FormData($(this)[0]);

		$.ajax({
			type: "post",
			url: "/wp-json/sandicor/update-config",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (data) {
				if (data) {
					window.location.reload();
				} else {
					alert("There's problem to save sandicor credentials. Please contact support team!")
				}
			}
		})
	});
})(jQuery);