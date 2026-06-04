/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/

    //---------------------------------------------------------
    // FRONT GALLERY PHOTO
    //---------------------------------------------------------

        const loaderHTML = '<br/><div align="center"><img src="/img/mobile/loader/mEDEFEELoader.gif" height="20" width="20" /></div><br/>';

        let offsetPhotoList = 18;
        let idGalleryMore = null;

        function initFancybox() {
            if (window.Fancybox) {
                Fancybox.bind("[data-fancybox]", {});
            }
        }


    //---------------------------------------------------------
    // FRONT GALLERY DISPLAY
    //---------------------------------------------------------

        async function photoListDisplay(idGallery, idCategory) {

            const photoList = document.getElementById("photoList");
            const photoMenu = document.getElementById("photoMenu");

            photoList.innerHTML = loaderHTML;

            try {

                const data = await ajax.post(ajaxPath+'gallery/process-photo-list-display.php', {
                    idGallery: idGallery,
                    idCategory: idCategory,
                    time: Date.now()
                });

                photoList.innerHTML = data.photoContent;
                photoMenu.innerHTML = data.menuContent;

                offsetPhotoList = 18;
                idGalleryMore = data.idGallery;

                initFancybox();
                initInfiniteScroll();
                initLazyLoad();

            } catch (e) { console.error("Error:", e); }
        }

        // --- Click menu (delegation)
        document.addEventListener("click", function (e) {
            const link = e.target.closest("#photoMenu a");
            if (link) {
                e.preventDefault();
                photoListDisplay(link.id, idCategory);
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

                        const data = await ajax.post(ajaxPath+'gallery/process-photo-list-display-more.php', {
                            offset: offsetPhotoList,
                            idGallery: idGalleryMore,
                            idCategory: idCategory
                        });

                        if (!data.photoContent) return;

                        offsetPhotoList += 18;
                        idGalleryMore = data.idGallery;

                        const temp = document.createElement("div");
                        temp.innerHTML = data.photoContent;

                        const newElems = Array.from(temp.children);

                        newElems.forEach(el => {
                            el.style.display = "none";
                            document.getElementById("photoList").appendChild(el);

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

        photoListDisplay("", idCategory);