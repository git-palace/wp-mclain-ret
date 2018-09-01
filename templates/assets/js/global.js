var init_search_result_map = function() {
	let search_result_map = new google.maps.Map(document.getElementById('search-result-map'), {
		zoom: 13,
		center: new google.maps.LatLng(parseFloat( marker_list[0]['lat'] ), parseFloat( marker_list[0]['lng'] )),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});

	let infowindow = new google.maps.InfoWindow();

	let marker, i;

	for (i = 0; i < marker_list.length; i++) {  
		marker = new google.maps.Marker({
			position: new google.maps.LatLng( parseFloat( marker_list[i]['lat'] ), parseFloat( marker_list[i]['lng'] ) ),
			map: search_result_map,
		});

		google.maps.event.addListener( marker, 'click', (function(marker, i) {
			return function() {				
				infowindow.setContent( marker_list[i]['address'] );
				infowindow.open( search_result_map, marker );
			}
		} ) ( marker, i) );
	}
}