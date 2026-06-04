  
  
  //---------------------------------------------------------
  // FRONT CONTACT JS
  //---------------------------------------------------------

    let map;
    let originPlace = null, destinationPlace = null;
    let markers = [];
    let currentPolyline = null;


//---------------------------------------------------------
// MARKER
//---------------------------------------------------------

  function markerContent(url) {
    const content = document.createElement("div");
    const img = document.createElement("img");
    img.src = url;
    img.style.width = "45px";
    img.style.height = "45px";
    content.appendChild(img);
    return { content, anchor: new google.maps.Point(22, 45) };
  }


//---------------------------------------------------------
// INIT MAP
//---------------------------------------------------------

  window.initialize = function () {

    const center = { lat: latHost, lng: lngHost };

    map = new google.maps.Map(document.getElementById("targetmapfront"), {
      zoom: 16,
      center: center,
      mapId: MAP_ID
    });

    // Marker principal
    const markerData = markerContent(imagehostUrl);

    const hostMarker = new google.maps.marker.AdvancedMarkerElement({
      map,
      position: center,
      title: "Destination",
      content: markerData.content
    });

    const infoWindow = new google.maps.InfoWindow({
      content: `<strong>${bulletxt}</strong>`
    });

    // Nouvelle syntaxe pour AdvancedMarkerElement
    hostMarker.addListener("gmp-click", () => {
      infoWindow.open({ map, anchor: hostMarker });
    });

    // AUTOCOMPLETE → PlaceAutocompleteElement
    const originInput = document.getElementById("origin");
    const destinationInput = document.getElementById("destination");

    const originAutocomplete = new google.maps.places.PlaceAutocompleteElement({
      componentRestrictions: { country: "fr" }
    });
    originAutocomplete.id = "origin-autocomplete";
    originAutocomplete.style.colorScheme = 'light';
    originAutocomplete.setAttribute('placeholder', 'Rechercher une adresse de départ...');
    originInput.replaceWith(originAutocomplete);

    const destinationAutocomplete = new google.maps.places.PlaceAutocompleteElement({
      componentRestrictions: { country: "fr" }
    });
    destinationAutocomplete.id = "destination-autocomplete";
    destinationAutocomplete.style.colorScheme = 'light';
    destinationAutocomplete.setAttribute('placeholder', 'Rechercher une adresse de destination...');
    destinationAutocomplete.value = destinationConfig;
    destinationInput.replaceWith(destinationAutocomplete);

    originAutocomplete.addEventListener("gmp-select", async (event) => {
      originPlace = event.placePrediction.toPlace();
      await originPlace.fetchFields({ fields: ["formattedAddress", "location"] });
      calculateRoute();
    });

    destinationAutocomplete.addEventListener("gmp-select", async (event) => {
      destinationPlace = event.placePrediction.toPlace();
      await destinationPlace.fetchFields({ fields: ["formattedAddress", "location"] });
      calculateRoute();
    });

    document.getElementById("direction").addEventListener("submit", e => {
      e.preventDefault();
      calculateRoute();
    });

    new google.maps.TrafficLayer().setMap(map);
  };


//---------------------------------------------------------
// CALCULATE ROUTE
//---------------------------------------------------------

  async function calculateRoute() {

    clearMarkers();
    if (currentPolyline) {
      currentPolyline.setMap(null);
      currentPolyline = null;
    }

    const originAddress =
      originPlace?.formattedAddress ||
      document.getElementById("origin-autocomplete")?.value;

    const destinationAddress =
      destinationPlace?.formattedAddress ||
      document.getElementById("destination-autocomplete")?.value ||
      destinationConfig;

    if (!originAddress || !destinationAddress) return;

    // Routes API (nouveau)
    const routesApiUrl = "https://routes.googleapis.com/directions/v2:computeRoutes";

    const body = {
      origin: { address: originAddress },
      destination: { address: destinationAddress },
      travelMode: "DRIVE",
      routingPreference: "TRAFFIC_AWARE",
      computeAlternativeRoutes: false,
      languageCode: "fr-FR",
      units: "METRIC"
    };

    try {
      const response = await fetch(routesApiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Goog-Api-Key": GOOGLE_MAPS_API_KEY,
          "X-Goog-FieldMask": "routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline,routes.legs"
        },
        body: JSON.stringify(body)
      });

      const data = await response.json();

      if (!data.routes || data.routes.length === 0) {
        console.warn("Aucun itinéraire trouvé", data);
        return;
      }

      const route = data.routes[0];
      const leg = route.legs[0];

      // Décodage et affichage du polyline
      const decodedPath = google.maps.geometry.encoding.decodePath(
        route.polyline.encodedPolyline
      );

      currentPolyline = new google.maps.Polyline({
        path: decodedPath,
        strokeColor: "#4A90D9",
        strokeOpacity: 0.9,
        strokeWeight: 5,
        map: map
      });

      // Ajuster la vue
      const bounds = new google.maps.LatLngBounds();
      decodedPath.forEach(point => bounds.extend(point));
      map.fitBounds(bounds);

      // Markers départ / arrivée
      const startLat = leg.startLocation.latLng.latitude;
      const startLng = leg.startLocation.latLng.longitude;
      const endLat = leg.endLocation.latLng.latitude;
      const endLng = leg.endLocation.latLng.longitude;

      addMarker({ lat: startLat, lng: startLng }, "/tmp/map/origin.svg", "Départ");
      addMarker({ lat: endLat, lng: endLng }, imagehostUrl, "Arrivée");

      // Infos distance / durée
      const distanceKm = (route.distanceMeters / 1000).toFixed(1);
      const durationSec = parseInt(route.duration.replace("s", ""));
      const durationMin = Math.round(durationSec / 60);
      const durationText = durationMin >= 60
        ? `${Math.floor(durationMin / 60)} h ${durationMin % 60} min`
        : `${durationMin} min`;

      document.getElementById("routeInfo").innerHTML =
        `Distance : ${distanceKm} km – Durée : ${durationText}`;

      // Panel itinéraire (si tu veux afficher les étapes)
      renderPanel(leg.steps);

    } catch (err) {
      console.error("Erreur Routes API :", err);
    }
  }


//---------------------------------------------------------
// PANEL ITINÉRAIRE (remplace DirectionsRenderer panel)
//---------------------------------------------------------

  function renderPanel(steps) {
    const panel = document.getElementById("panelcontent");
    if (!panel || !steps) return;

    panel.innerHTML = steps.map((step, i) => {
      const distM = step.distanceMeters || 0;
      const dist = distM >= 1000
        ? `${(distM / 1000).toFixed(1)} km`
        : `${distM} m`;
      // Les instructions HTML sont dans step.navigationInstruction.instructions
      const instruction = step.navigationInstruction?.instructions || "";
      return `<div style="padding:6px 0;border-bottom:1px solid #eee">
        <span>${i + 1}. ${instruction}</span>
        <small style="float:right;color:#888">${dist}</small>
      </div>`;
    }).join("");
  }


//---------------------------------------------------------
// ADD / CLEAR MARKERS
//---------------------------------------------------------

  function addMarker(position, iconUrl, title) {
    const content = document.createElement("div");
    const img = document.createElement("img");
    img.src = iconUrl;
    img.style.width = "45px";
    img.style.height = "45px";
    content.appendChild(img);

    const marker = new google.maps.marker.AdvancedMarkerElement({
      map,
      position,
      title,
      content
    });

    markers.push(marker);
  }

  function clearMarkers() {
    markers.forEach(m => m.map = null);
    markers = [];
  }