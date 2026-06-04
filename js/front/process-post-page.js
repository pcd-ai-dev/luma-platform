/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/

    //---------------------------------------------------------
    // FRONT POST PAGE
    //---------------------------------------------------------

		function waitForMedia(container, callback) {
			const imgs = container.querySelectorAll('img');
			const videos = container.querySelectorAll('video');
			const total = imgs.length + videos.length;
			let loaded = 0;

			if (total === 0) return callback();

			function check() {
				loaded++;
				if (loaded === total) callback();
			}

			imgs.forEach(img => {
				if (img.complete) {
					check();
				} else {
					img.addEventListener('load', check);
					img.addEventListener('error', check);
				}
			});

			videos.forEach(video => {
				if (video.readyState >= 2) {
					check();
				} else {
					video.addEventListener('loadeddata', check);
					video.addEventListener('error', check);
				}
			});
		}

		document.addEventListener('DOMContentLoaded', function () {

			const container = document.querySelector('#photoContainer');

			// Init Isotope
			const iso = new Isotope(container, {
				itemSelector: '.item',
				layoutMode: 'masonry',
				masonry: {
					columnWidth: 110
				},
				cellsByRow: {
					columnWidth: 220,
					rowHeight: 220
				},
				masonryHorizontal: {
					rowHeight: 110
				},
				cellsByColumn: {
					columnWidth: 220,
					rowHeight: 220
				}
			});

			// imagesLoaded
			imagesLoaded(container).on('progress', function () {
				iso.layout();
			});

			// attendre images + vidéos
			waitForMedia(container, function () {
				iso.layout();
			});

			// Fancybox (version JS moderne)
			Fancybox.bind('.fancybox-video', {});
			Fancybox.bind('[data-fancybox="gallery"]', {});

		});