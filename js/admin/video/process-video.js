/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
   	// VIDEO ADMIN
   	//---------------------------------------------------------

    //---------------------------------------------------------
   	// VALIDATE ADD GALLERY
   	//---------------------------------------------------------

        window.galleryFormValidator = new Validator("galleryAddVideo", {
            rules: {
                titleAdd_1: { required: true }
            },
            messages: {
                titleAdd_1: { required: "Veuillez renseigner un nom" },
            },
			allowSubmit: true
        });


    //---------------------------------------------------------
   	// VALIDATE ADD GALLERY
   	//---------------------------------------------------------

        window.galleryFormValidator = new Validator("galleryModVideo", {
            rules: {
                modTitleGallery_1: { required: true }
            },
            messages: {
                modTitleGallery_1: { required: "Veuillez renseigner un nom" },
            },
			allowSubmit: true
        });



    //---------------------------------------------------------
   	// INIT SORTABLE
   	//---------------------------------------------------------

       initSortable(
            "ol.sortable.gallery",
            prefixVideo+"gallery",
            "position",
            "id",
            "list",
            0
        );


    //---------------------------------------------------------
   	// FUNCTIONS
   	//---------------------------------------------------------

        const showFolder = async (id, idVideo = 0) => {
            try {

                const data = await ajax.post(ajaxPath+'video/process-gallery-video-display.php', { id: id, idCategory: idCategory });

                const container = document.getElementById("showfolder");
                container.innerHTML = data.videoContent;

                initSortable("ol.sortable.video", prefixVideo + "item", "position", "id", "list", 0);

                if (idVideo !== 0) {window.scrollTo({ top: 0, behavior: 'smooth' });}

            } catch (e) { console.error("Error:", e); }
        };


        const showFolderGlobal = async () => {
            try {
                const data = await ajax.post(ajaxPath+'video/process-video-global-display.php', { idCategory: idCategory });

                const container = document.getElementById("showFolderSort");
                container.innerHTML = data.videoContent;

                initSortable("ol.sortable.videoGlobal", prefixVideo + "item", "globalPosition", "id", "list", 0);

            } catch (e) { console.error("Error:", e); }
        };


    //---------------------------------------------------------
   	// ADD VIDEO FORM
   	//---------------------------------------------------------

        const addVideoForm = async (id, idCategory) => {
            try {
                const data = await ajax.post(ajaxPath+'video/process-gallery-video-add.php', { id: id, idCategory: idCategory });

                const container = document.getElementById("showVideoForm");
                container.innerHTML = data.formContent;

                document.querySelectorAll(".tag-container").forEach(container => {
                    new TagInput(container, {
                        autocompleteUrl: autocompledPath
                    });
                });

                slideDown(document.getElementById('showVideoForm'), 500);

                window.videoFormValidator = new Validator("videoAddForm", {
                    rules: {
                        url_video: { required: true }
                    },
                    messages: {
                        url_video: { required: "Numéro de la vidéo obligatoire" },
                    },
			        allowSubmit: true
                });  

            } catch (e) { console.error("Error:", e); }
        };


        document.addEventListener("submit", async function (event) {

            if (!event.target.matches("#videoAddForm")) return;

            const form = event.target;


            if (window.videoFormValidator && !window.videoFormValidator.validateForm()) {
                return;
            }

            event.preventDefault();

            const formData = new FormData(form);
            const idGallery = form.querySelector("#idGallery").value;

            try {
                const data = await ajax.post(ajaxPath+'video/process-gallery-video-add-db.php', formData );

                if (data.status==1){
                    const showForm = document.getElementById("showVideoForm");
                    showForm.style.display = "none";
                    showForm.innerHTML = "";

                    form.reset();

                    const button = this.querySelector('.modifAreaSelector');
					button.classList.toggle('closeArea');

                    slideUp(document.getElementById('showVideoForm'), 500);
                    showFolder(idGallery, 0);
                }

            } catch (e) { console.error("Error:", e); }

        });

    
    //---------------------------------------------------------
   	// MOD VIDEO FORM
   	//---------------------------------------------------------

        const modVideoForm = async (id) => {
            try {
                const data = await ajax.post(ajaxPath+'video/process-gallery-video-mod.php', { id: id, idCategory: idCategory });

                const container = document.getElementById("showVideoForm");
                container.innerHTML = data.formContent;

                slideDown(document.getElementById('showVideoForm'), 500);
                window.scrollTo({top: 0,left: 0,behavior: "smooth"});

                document.querySelectorAll(".tag-container").forEach(container => {
                    new TagInput(container, {
                        autocompleteUrl: autocompledPath
                    });
                });

                loadVideoImageForm(entityVideo, idCategory, id);

                window.videoFormValidator = new Validator("videoModForm", {
                    rules: {
                        modUrl: { required: true }
                    },
                    messages: {
                        modUrl: { required: "Numéro de la vidéo obligatoire" },
                    },
			        allowSubmit: true
                });             

            } catch (e) { console.error("Error:", e); }
        };


        async function loadVideoImageForm(entityVideo, idCategory, idVideo) {

            try {

                const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
                    entity:entityVideo,
                    idParent:idCategory,
                    id: idVideo,
                    field: "img",
                    target: "id",
                    folder: "",
                    label: "",
                    idSite:idSite,
                    type:1
                });

                const zone = document.getElementById("photoUploadAreaVideoMod");
                zone.innerHTML = data.formContent;

                zone.querySelectorAll("script").forEach(script => {
                    eval(script.textContent);
                });

                if (window.__uploaders) {
                    window.__uploaders.forEach(cfg => initFileUploader(cfg));
                    window.__uploaders = [];
                }

            } catch (e) { console.error("Error:", e); }

        }


        document.addEventListener("submit", async function (event) {

            if (!event.target.matches("#videoModForm")) return;

            const form = event.target;

            if (window.videoFormValidator && !window.videoFormValidator.validateForm()) {
                return;
            }

            event.preventDefault();

            const formData = new FormData(form);
            
            const idGallery = form.querySelector("#idGallery").value;
            const modId = form.querySelector("#modId").value;

            try {
                const data = await ajax.post(ajaxPath+'video/process-gallery-video-mod-db.php', formData);

                if (data.status == 1) {
                    const showForm = document.getElementById("showVideoForm");
                    showForm.style.display = "none";
                    showForm.innerHTML = "";

                    form.reset();

                    showFolder(idGallery, modId);
                }

            } catch (e) { console.error("Error:", e); }

        });


    //---------------------------------------------------------
   	// DELETE VIDEO
   	//---------------------------------------------------------

        document.addEventListener("click", async function (event) {
            const target = event.target.closest(".delvideo");
            if (!target) return;

            event.preventDefault();

            const id = target.id;

            try {

                const data = await ajax.post(ajaxPath+'video/process-gallery-video-delete.php', {
                    id: id,
                    idCategory: idCategory,
                    idgallery: modId
                });

                if (data.status == 1) {
                    const element = document.getElementById("list_video_" + data.idVideo);

                    if (element) {
                        element.style.transition = "opacity 0.2s ease, height 0.2s ease";
                        element.style.overflow = "hidden";
                        element.style.opacity = "0";
                        element.style.height = element.offsetHeight + "px";

                        requestAnimationFrame(() => {
                            element.style.height = "0";
                        });

                        setTimeout(() => element.remove(), 200);
                    }
                }


            } catch (e) { console.error("Error:", e); }

        });


    //---------------------------------------------------------
   	// PROCESS
   	//---------------------------------------------------------

        showFolderGlobal();

        if(modStatus==1){showFolder(modId,0);}


		document.querySelectorAll('.headerformtoggle').forEach(header => {
			header.addEventListener('click', function() {

				const content = this.nextElementSibling;
				const button = this.querySelector('.modifAreaSelector');

				content.classList.toggle('open');
					button.classList.toggle('closeArea');

					if (content.classList.contains('open')) {
						content.style.display = "block";
					} else {
						content.style.display = "none";
					}
				});
			});


        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('modVideo')) {
                let modId = e.target.id;
                modVideoForm(modId);
                e.preventDefault();
            }
        });

        document.body.addEventListener('click', function(e) {
            if (e.target.id === 'closeForm') {
                slideUp(showVideoForm, 200, () => {
                    showVideoForm.innerHTML = '';
                });
                e.preventDefault();
            }
        });

        document.addEventListener("click", async (event) => {
            const btnAdd = event.target.closest("#addVideoTarget");
            if (!btnAdd) return;

            event.preventDefault();

            addVideoForm(modId, idCategory);

            return false;

        });


    //---------------------------------------------------------
   	// PROCESS DELETE GALLERY
   	//---------------------------------------------------------

        document.addEventListener("click", async (event) => {
            const btn = event.target.closest(".deleteGallery");
            if (!btn) return;

            event.preventDefault();

            const id = btn.id;

            const result = await Swal.fire({
                title: "Suppression",
                text: "Voulez-vous vraiment supprimer cette galerie vidéo ?",
                icon: "warning",
                width:'450px',
                showCancelButton: true,
                confirmButtonColor: "#21c994",
                confirmButtonText: "Confirmer",
                cancelButtonText: "Annuler"
            });

            if (!result.isConfirmed) return;

            try {

                const data = await ajax.post(ajaxPath+'video/process-gallery-delete.php', {
                    id: id,
                    idCategory: idCategory,
                    time: Date.now()
                });

                if (data.status == 1) {
                    const element = document.querySelector("#list_gallery_" + data.id);

                    if (element) {
                        element.style.transition = "opacity 0.3s ease";
                        element.style.opacity = "0";
                        setTimeout(() => element.remove(), 300);
                    }

                    showWarningMessage("builderMsgUpdate", "Galerie vidéo supprimée", "#81B929");

                } else {
                    throw new Error("Suppression impossible");
                }


            } catch (e) { console.error("Error:", e); }

        });