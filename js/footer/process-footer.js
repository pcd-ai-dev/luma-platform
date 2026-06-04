/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
    // FRONT FOOTER JS
    //---------------------------------------------------------


    //---------------------------------------------------------
    // PARALLAX
    //---------------------------------------------------------

		document.addEventListener('DOMContentLoaded', () => {
			const parallaxImages = document.querySelectorAll('.img-parallax');

			if (parallaxImages.length === 0) return;

			function parallaxAll() {
				const winY = window.scrollY;
				const winH = window.innerHeight;
				const winBottom = winY + winH;

				parallaxImages.forEach((img) => {
				const imgParent = img.parentElement;
				const speed = parseFloat(img.getAttribute('data-speed')) || 0;
				const imgY = imgParent.offsetTop;
				const parentH = imgParent.offsetHeight;

				let imgPercent = 0;

				if (winBottom > imgY && winY < imgY + parentH) {
					const imgBottom = (winBottom - imgY) * speed;
					const imgTop = winH + parentH;
					imgPercent = (imgBottom / imgTop) * 100 + (50 - speed * 50);
				}

				img.style.top = imgPercent + '%';
				img.style.transform = `translate(-50%, -${imgPercent}%)`;
				});
			}

			let ticking = false;

			function onScroll() {
				if (!ticking) {
				window.requestAnimationFrame(() => {
					parallaxAll();
					ticking = false;
				});
				ticking = true;
				}
			}

			// Events
			window.addEventListener('scroll', onScroll);
			window.addEventListener('load', parallaxAll);
			window.addEventListener('resize', parallaxAll);
		});