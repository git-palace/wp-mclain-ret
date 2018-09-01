/*
$ = jQuery;
$.each($(".metadata_details_fields.opened td:nth-child(2)"), function(idx, obj) { 
if (idx != 0) cc[$(obj).text()] = $($(".metadata_details_fields.opened td:nth-child(3)")[idx]).text()
});
*/
(function($) {
	// submit configuration form
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

	// pagination
	$("select.per-page").change(function() {
		var resource = $(this).attr("type");
		if (resource == 'property')
			window.location.href = "/wp-admin/admin.php?page=sandicor&perPage=" + $(this).val();
	});

	// single-property update form
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

	// remove picture
	$(".remove-picture").click(function() {
		$(this).parent("li.picture").css('-webkit-animation', 'fadeOut 500ms');

		$(this).parent("li.picture").bind('webkitAnimationEnd',function() {
			$(this).remove();
		});
	});

	// upload image
	$('#upload_new_image').click(function() {
		frame = wp.media({
			title: 'Add images to property',
			button: { text: 'Add Images' },
			align: false,
			multiple: true
		});

		frame.on('select', function() {
			let attachments = frame.state().get('selection').toJSON();

			$(attachments).each(function(idx, attachment) {
				let id = new Date().getTime().toString(36);

				let html = `
					<li class="picture d-flex flex-column ` + idx + id + `">
						<a class="remove-picture d-none text-center"><span class="m-auto">&times;</span></a>
						<img class="img-fluid m-auto" src="` + attachment.url + `" />
						<input type="hidden" name="sandicor[pictures][` + idx + id + `][url]" value="` + attachment.url + `">
						<div class="summary d-flex align-items-center mt-auto">
							<label class="mr-auto" for="">Description : </label>
							<input class="ml-auto" type="text" name="sandicor[pictures][` + idx + id + `][desc]" value="` + attachment.alt + `" />
						</div>
					</li>
				`;

				$("#property_pictures").append(html);

				$("#property_pictures li." + id + " a.remove-picture").click(function() {
					$(this).parent("li.picture").css('-webkit-animation', 'fadeOut 500ms');

					$(this).parent("li.picture").bind('webkitAnimationEnd',function() {
						$(this).remove();
					});
				});

			});
		});

		frame.open();

		return false;
	});
	
})(jQuery);