/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/

    //---------------------------------------------------------
    // FRONT WEB TV
    //---------------------------------------------------------

        const loaderHTML = '<br/><div align="center"><img src="/img/mobile/loader/mEDEFEELoader.gif" height="20" width="20" /></div><br/>';

        let offsetVideoList = 18;
        let idGalleryMore = null;
        let videoHeight = 400;

        function initFancybox() {
            if (window.Fancybox) {
                Fancybox.bind("[data-fancybox]", {});
            }
        }


    //---------------------------------------------------------
    // FRONT VIDEO DISPLAY
    //---------------------------------------------------------

        async function videoListDisplay(idGallery, idCategory) {

            const videoList = document.getElementById("videoList");
            const videoMenu = document.getElementById("videoMenu");

            videoList.innerHTML = loaderHTML;

            try {

                const data = await ajax.post(ajaxPath+'video/process-video-list-display.php', {
                    idGallery: idGallery,
                    idCategory: idCategory,
                    time: Date.now()
                });

                videoList.innerHTML = data.videoContent;
                videoMenu.innerHTML = data.menuContent;

                offsetVideoList = 18;
                idGalleryMore = data.idGallery;

                initFancybox();
                initInfiniteScroll();

            } catch (e) { console.error("Error:", e); }

        }

        // --- Click menu (delegation)
        document.addEventListener("click", function (e) {
            const link = e.target.closest("#videoMenu a");
            if (link) {
                e.preventDefault();
                videoListDisplay(link.id, idCategory);
            }
        });


    //---------------------------------------------------------
    // INFINITE SCROLL
    //---------------------------------------------------------

        async function initInfiniteScroll() {
            let ajaxReady = true;

            async function onScroll() {
                if (!ajaxReady) return;

                const scrollTop = window.scrollY;
                const windowHeight = window.innerHeight;
                const docHeight = document.documentElement.scrollHeight;

                if (scrollTop + windowHeight + 200 >= docHeight) {
                    ajaxReady = false;

                     try {

                        const data = await ajax.post(ajaxPath+'video/process-video-list-display-more.php', {
                            offset: offsetVideoList,
                            idGallery: idGalleryMore,
                            idCategory: idCategory
                        });

                        if (!data.videoContent) return;

                        offsetVideoList += 18;
                        idGalleryMore = data.idGallery;

                        const temp = document.createElement("div");
                        temp.innerHTML = data.videoContent;

                        const newElems = Array.from(temp.children);

                        newElems.forEach(el => {
                            el.style.display = "none";
                            document.getElementById("videoList").appendChild(el);

                            requestAnimationFrame(() => {
                                el.style.transition = "opacity 0.3s";
                                el.style.opacity = 0;
                                el.style.display = "";
                                requestAnimationFrame(() => {
                                    el.style.opacity = 1;
                                });
                            });
                        });

                        initFancybox();
                        ajaxReady = true;

                    } catch (e) { console.error("Error:", e); }

                }
            }

            window.removeEventListener("scroll", onScroll);
            window.addEventListener("scroll", onScroll);
        }


    //---------------------------------------------------------
    // PROCESS
    //---------------------------------------------------------

        videoListDisplay("", idCategory);