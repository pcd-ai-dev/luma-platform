/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/


    //---------------------------------------------------------
    // INIT FANCYBOX
    //---------------------------------------------------------

        function initFancybox() {
            if (window.Fancybox) {
                Fancybox.bind("[data-fancybox]", {});
            }
        }

		initFancybox();

    //---------------------------------------------------------
    // FRONT POST LIST
    //---------------------------------------------------------

		let offsetPost = "10";
		let offsetJob = "10";

		document.addEventListener("DOMContentLoaded", function () {

			// Bouton JOB
			document.addEventListener("click", function (event) {
				if (event.target.closest("#jobBtn")) {
					document.getElementById("newsBtn").classList.add("btnTitleOff");
					document.getElementById("jobBtn").classList.add("btnTitleBlueOn");

					//document.getElementById("viewNewsMore").style.display = "none";
					//document.getElementById("viewJobMore").style.display = "block";

					const postArea = document.getElementById("postArea");
					const jobArea = document.getElementById("jobArea");

					fadeOut(postArea, function () {
						fadeIn(jobArea);
					});

					event.preventDefault();
				}
			});


    //---------------------------------------------------------
    // SWITCH POST
    //---------------------------------------------------------

			document.addEventListener("click", function (event) {
				if (event.target.closest("#newsBtn")) {
					document.getElementById("newsBtn").classList.remove("btnTitleOff");
					document.getElementById("jobBtn").classList.remove("btnTitleBlueOn");

					//document.getElementById("viewNewsMore").style.display = "block";
					//document.getElementById("viewJobMore").style.display = "none";

					const jobArea = document.getElementById("jobArea");
					const postArea = document.getElementById("postArea");

					fadeOut(jobArea, function () {
						fadeIn(postArea);
					});

					event.preventDefault();
				}
			});


    //---------------------------------------------------------
    // MORE POST
    //---------------------------------------------------------

			document.addEventListener("click", async function (event) {
				if (event.target.closest("#viewNewsMore")) {

					const btn = event.target.closest("#viewNewsMore");
					const idCatPost = btn.getAttribute("data-id");

					try {
						const data = await ajax.post(ajaxPath+'post/process-post-list-more.php', {
							offsetPost: offsetPost,
							type: 1,
							idCategory: idCatPost,
							time: new Date().getTime()
						});

						if (data.postContent !== "") {
							window.ajaxready = true;

							const tempDiv = document.createElement("div");
							tempDiv.innerHTML = data.postContent;

							const postArea = document.getElementById("postArea");
							while (tempDiv.firstChild) {
								postArea.appendChild(tempDiv.firstChild);
							}
						}

					} catch (e) { console.error("Error:", e); }

					event.preventDefault();
				}
			});

		});
		

    //---------------------------------------------------------
    // HIDE / DISPLAY FUNCTIONS
    //---------------------------------------------------------
	
		function fadeOut(element, callback) {
			element.style.opacity = 1;

			let fadeEffect = setInterval(() => {
				if (element.style.opacity > 0) {
					element.style.opacity -= 0.1;
				} else {
					clearInterval(fadeEffect);
					element.style.display = "none";
					if (callback) callback();
				}
			}, 30);
		}

		function fadeIn(element) {
			element.style.display = "block";
			element.style.opacity = 0;

			let fadeEffect = setInterval(() => {
				let opacity = parseFloat(element.style.opacity);
				if (opacity < 1) {
					element.style.opacity = opacity + 0.1;
				} else {
					clearInterval(fadeEffect);
				}
			}, 30);
		}