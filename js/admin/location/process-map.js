/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


   	//---------------------------------------------------------
	// ADMIN MAP
   	//---------------------------------------------------------


	if(countMap===0){


		//---------------------------------------------------------
		// VALIDATE ADD MAP
		//---------------------------------------------------------

			window.photoFormValidator = new Validator("addMapForm", {
				rules: {
					address: { required: true }
				},
				messages: {
					address: { required: "Veuillez renseigner un nom ou une adresse." },
				},
				allowSubmit: true
			});


	    //---------------------------------------------------------
        // PROCESS AUTOCOMPLETE GOOGLE ADD
        //---------------------------------------------------------

		function placeAddLoc(status) {
			if (status == 0) {} else {
				const input = document.getElementById('address');
				const options = {};
				const autocomplete = new google.maps.places.Autocomplete(input, options);
				google.maps.event.addListener(autocomplete, 'place_changed', function() {
					const place = autocomplete.getPlace();
					if (typeof place.international_phone_number === 'undefined') {
						const phoneNumber = '';
					} else {
						const phoneNumber = place.international_phone_number;
					}

					if (typeof place.address_components[1].short_name === 'undefined') {
						const addressSet = '';
					} else {
						const addressSet = place.address_components[0].short_name + ' ' + place.address_components[1].short_name + ' ' + place.address_components[2].short_name;
					}

					const lat = place.geometry.location.lat();
					const lng = place.geometry.location.lng();
					document.getElementById('lattitude').value = lat;
					document.getElementById('longitude').value = lng;
				});
			}
		}

		placeAddLoc(1);

	}else{

		//---------------------------------------------------------
		// VALIDATE MOD PAD
		//---------------------------------------------------------

			window.photoFormValidator = new Validator("addMapForm", {
				rules: {
					modAdress: { required: true }
				},
				messages: {
					modAdress: { required: "Veuillez renseigner un nom ou une adresse." },
				},
				allowSubmit: true
			});

        //---------------------------------------------------------
        // PROCESS IMAGE SQUARE
        //---------------------------------------------------------

			const loadMapImageForm= async (id) => {
				try {

					const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
						entity:entityLocation,
						idParent:idCategory,
						id: id,
						field: "img",
						target: "id",
						folder: "img",
						label: "",
                    	type:1
					});

					const zone = document.getElementById("iconUploadArea");
					zone.innerHTML = data.formContent;

					zone.querySelectorAll("script").forEach(script => {
						eval(script.textContent);
					});

					if (window.__uploaders) {
						window.__uploaders.forEach(cfg => initFileUploader(cfg));
						window.__uploaders = [];
					}

				} catch (e) { console.error("Error:", e); }
			};

			loadMapImageForm(idMap);



	    //---------------------------------------------------------
        // MAP DISPLAY
        //---------------------------------------------------------
				
			const markers = [];

			async function initialize() {

				const latLng = new google.maps.LatLng(latHost, lngHost);

				const myOptions = {
					zoom: 16,
					center: latLng,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					maxZoom: 20,
					mapId: "YOUR_MAP_ID"  // ← depuis Google Cloud Console
				};

				const map = new google.maps.Map(document.getElementById('targetMap'), myOptions);

				// Import de la librairie marker
				const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

				// Icône personnalisée
				const markerImg = document.createElement('img');
				markerImg.src    = imgMapHost;
				markerImg.style.width  = '40px';
				markerImg.style.height = '40px';

				const marker = new AdvancedMarkerElement({
					position: latLng,
					map: map,
					title: "SITE",
					content: markerImg
				});

				const trafficLayer = new google.maps.TrafficLayer();
				trafficLayer.setMap(map);
			}



	    //---------------------------------------------------------
        // PROCESS AUTOCOMPLETE GOOGLE MOD
        //---------------------------------------------------------

			function placeModLoc(status) {
				if (status == 0) return;

				const input = document.getElementById('modAddress');

				function getComponent(components, type) {
					const found = components.find(c => c.types.includes(type));
					return found ? found.shortText : '';
				}

				const autocomplete = new google.maps.places.PlaceAutocompleteElement();
				autocomplete.style.colorScheme = 'light';
				autocomplete.setAttribute('placeholder', 'Rechercher une adresse...');
				autocomplete.value = modAddress;

				const hiddenAddress = document.createElement('input');
				hiddenAddress.type = 'hidden';
				hiddenAddress.id   = 'modAddress';
				hiddenAddress.name = input.name ?? '';

				input.parentNode.replaceChild(autocomplete, input);
				autocomplete.parentNode.insertBefore(hiddenAddress, autocomplete.nextSibling);

				autocomplete.addEventListener('gmp-select', async function(event) {
					const place = event.placePrediction.toPlace();
					await place.fetchFields({
						fields: [
							'displayName', 'location', 'addressComponents',
							'formattedAddress', 'nationalPhoneNumber',
							'websiteURI', 'googleMapsURI'
						]
					});

					const components = place.addressComponents ?? [];
					const streetNum  = getComponent(components, 'street_number');
					const route      = getComponent(components, 'route');
					const city       = getComponent(components, 'locality');
					const cp         = getComponent(components, 'postal_code');
					const addressSet = [streetNum, route, cp, city].filter(Boolean).join(' ');

					document.getElementById('modLat').value = place.location.lat();
					document.getElementById('modLng').value = place.location.lng();
					document.getElementById('modAddress').value   = addressSet;
				});
			}


			window.initMap = function() {
				placeModLoc(1);
				initialize();
			};

	}