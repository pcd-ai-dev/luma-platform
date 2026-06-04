/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/

    //---------------------------------------------------------
    // FRONT HEADER JS
    //---------------------------------------------------------

		document.addEventListener("DOMContentLoaded", function () {

			const body = document.body;
			const menuIcon = document.querySelectorAll('.menu-icon');
			const hLogoMobile = document.getElementById('hLogoMobile');
			const menu = document.getElementById('menu');
			const menuRight = document.getElementById('menuRight');
			const toggleMenu = document.getElementById('toggle-menu');
			const global = document.getElementById('global');
			const hLogo = document.getElementById('hLogo');
			const rsocialHeader = document.getElementById('rsocialHeader');
			const headerLogoCollapse = document.getElementById('headerLogoCollapse');
			const nav = document.querySelector("ul#nav");

			let closedRight = false;

			function getWidth() {
				return body.clientWidth;
			}

			function show(el) {
				if (!el) return;
				if (el.length) el.forEach(e => e.style.display = "block");
				else el.style.display = "block";
			}

			function hide(el) {
				if (!el) return;
				if (el.length) el.forEach(e => e.style.display = "none");
				else el.style.display = "none";
			}

			function toggleMenuIcon() {
				if (getWidth() <= 768) {
					show(menuIcon);
				} else {
					hide(menuIcon);
				}
			}

			function expandCollapseMenu() {
				if (getWidth() < 768) {
					menuIcon.forEach(el => el.classList.remove('hideMenu'));
					menu.classList.add('hideMenu');
				} else {
					menuIcon.forEach(el => el.classList.add('hideMenu'));
					menu.classList.remove('hideMenu');
					menu.style.display = "block";
				}
			}

			function handleScroll() {
				const WWidth = getWidth();
				const HScroll = WWidth / 3.65;

				if (window.scrollY >= HScroll) {
					Object.assign(menu.style, {
						position: 'fixed',
						top: '0px',
						marginTop: '0px',
						zIndex: '25',
						backgroundColor: 'rgba(255,255,255,1)',
						boxShadow: '10px 10px 20px -3px rgba(0,0,0,0.29)',
						transition: '.3s ease-in-out'
					});

					if (nav) nav.style.marginLeft = "270px";

					if (getWidth() <= 768) {
						hide(headerLogoCollapse);
					} else {
						show(headerLogoCollapse);
					}

				} else {
					if (window.typeShowMap === 2) {
						Object.assign(menu.style, {
							position: 'fixed',
							top: '0px',
							marginTop: '0px',
							zIndex: '25',
							backgroundColor: 'rgba(255,255,255,1)',
							boxShadow: '10px 10px 20px -3px rgba(0,0,0,0.29)',
							transition: '.3s ease-in-out'
						});

						if (nav) nav.style.marginLeft = "270px";

						if (getWidth() <= 768) {
							hide(headerLogoCollapse);
						} else {
							show(headerLogoCollapse);
						}

					} else {
						Object.assign(menu.style, {
							position: 'relative',
							top: '0px',
							zIndex: '25',
							backgroundColor: 'rgba(255,255,255,0)',
							boxShadow: 'none'
						});

						const w = getWidth();
						menu.style.marginTop = '0px';

						if (nav) nav.style.marginLeft = "18vw";
						hide(headerLogoCollapse);
					}
				}
			}

			function animateMenuRight() {
				const target = (!closedRight ? 100 : 0);
				menuRight.style.transition = "margin-right 0.09s";
				menuRight.style.marginRight = target + "%";

				if (closedRight) {
					body.classList.remove('noScroll');
					show(hLogo);
					show(rsocialHeader);
					hide(hLogoMobile);
				} else {
					body.classList.add('noScroll');
					hide(hLogo);
					hide(rsocialHeader);
					show(hLogoMobile);
				}

				closedRight = !closedRight;
			}

			// INIT
			toggleMenuIcon();
			expandCollapseMenu();

			// EVENTS
			window.addEventListener('resize', () => {
				toggleMenuIcon();
				expandCollapseMenu();
				handleScroll();
			});

			window.addEventListener('scroll', handleScroll);
			window.addEventListener('orientationchange', handleScroll);

			if (toggleMenu) {
				toggleMenu.addEventListener('change', function (e) {
					e.preventDefault();
					animateMenuRight();
				});
			}

		});