/*
$ = jQuery;
$.each($(".metadata_details_fields.opened td:nth-child(2)"), function(idx, obj) { 
if (idx != 0) cc[$(obj).text()] = $($(".metadata_details_fields.opened td:nth-child(3)")[idx]).text()
});
*/
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

	$("select.per-page").change(function() {
		var resource = $(this).attr("type");
		if (resource == 'property')
			window.location.href = "/wp-admin/admin.php?page=sandicor&perPage=" + $(this).val();
	});

	$("form#sandicor-update").submit(function(e) {
		e.preventDefault();

		var formData = new FormData($(this)[0]);

		$.ajax({
			type: "post",
			url: "/wp-json/sandicor/add-new-" + $("#resource_type").val(),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function(data) {
				if(data)
					window.location.href = "/wp-admin/admin.php?page=sandicor";
				else
					alert("there's problem to add/update new query.")
			}
		})
	});
})(jQuery);