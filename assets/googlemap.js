
(function () {

	"use strict";

	var wpmaps = document.querySelectorAll('.wpmaps');
	var googleMaps = [];

	for (var i = 0; i < wpmaps.length; i++) {

		var dataId = wpmaps[i].getAttribute('data-id');
		var dataName = 'googlemaps_' + dataId;
		var googleMap = {
			container: '.wpmaps--' + dataId,
			zoom: window[dataName].zoom,
			markers: window[dataName].markers,
			map: '',
			bounds: new google.maps.LatLngBounds(),
			mapOptions: {
				mapTypeId: 'roadmap',
				scrollwheel: false,
				navigationControl: false,
				mapTypeControl: false,
				scaleControl: false,
				draggable: true,
				scrollwheel: false,
				fullscreenControl: true,
				streetViewControl: true,
				clickableIcons: false,
			},
			init: function () {
				this.map = new google.maps.Map(document.querySelector(this.container), this.mapOptions);
				this.placeMarkers();
			},
			centerMap: function () {
				this.map.fitBounds(this.bounds);
			},
			placeMarkers: function () {
				for (i = 0; i < this.markers.length; i++) {

					var lat = this.markers[i][0];
					var lng = this.markers[i][1];
					var img = this.markers[i][2];
					var content = this.markers[i][3];
					var icon = this.markers[i][4];

					var position = new google.maps.LatLng(lat, lng);
					var marker = new google.maps.Marker({
						map: this.map,
						position: position,
						icon: icon,
						clickable: false
					});

					// Add popup only if there is content or image
					if (content.length || img.length) {

						marker.clickable = true;

						// Popup window
						var contentBox = document.createElement('div');
						contentBox.classList.add('popup');
						if (img) {
							contentBox.innerHTML = '<img class="popup__img" src=" ' + img + '">';
						}
						if (content) {
							contentBox.innerHTML += '<div class="popup__content">' + content + '</div>';
						}

						var infobox = new InfoBox({
							pixelOffset: new google.maps.Size(45, -100), // Popup position (x, y), I couldn't set dynamic based on contentBox height
							content: contentBox,
							infoBoxClearance: new google.maps.Size(1, 1),
						});
						// Show popup by default?
						//infobox.open(this.map, marker);

						// On marker click show popup
						google.maps.event.addListener(marker, 'click', (function (marker) {
							return function () {
								infobox.open(this.map, this);
							}
						})(marker));

					}

					// Center the map according to markers
					this.bounds.extend(position);
					this.centerMap();
				}
				// Zoom - doesn't work well with bounds so it is used only when there is only one marker
				if (this.markers.length < 2) {
					// Default zoom if it is not set
					var zoom = this.zoom ? this.zoom : 10;
					var boundsListener = google.maps.event.addListenerOnce(this.map, 'idle', function () {
						this.setZoom(parseInt(zoom));
					});
				}

			}

		}

		googleMaps.push(googleMap);

	}

	// Initiate each instance
	google.maps.event.addDomListener(window, 'load', function () {
		for (var i = 0; i < googleMaps.length; i++) {
			googleMaps[i].init();
		}
	});

})();