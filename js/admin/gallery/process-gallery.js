/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
   	// GALLERY ADMIN
   	//---------------------------------------------------------


    //---------------------------------------------------------
   	// VALIDATE ADD GALLERY
   	//---------------------------------------------------------

        window.photoFormValidator = new Validator("galleryAddPhoto", {
            rules: {
                titleAdd_1: { required: true }
            },
            messages: {
                titleAdd_1: { required: "Veuillez renseigner un nom" },
            },
			allowSubmit: true
        });


    //---------------------------------------------------------
   	// VALIDATE MOD GALLERY
   	//---------------------------------------------------------

        window.photoFormValidator = new Validator("galleryModPhoto", {
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
            prefixImg+"item",
            "position",
            "id",
            "list",
            0
        );


    //---------------------------------------------------------
   	// INTERFACE TOOGLE
   	//---------------------------------------------------------

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


    //---------------------------------------------------------
   	// FUNCTIONS
   	//---------------------------------------------------------

        const showFolder= async (id) => {
            try {

                const data = await ajax.post(ajaxPath+'action/process-image-gallery-display.php', {
                    id: id,
                    idCategory:idCategory,
                    folder:'gallery',
                   	table:prefixImg+"img",
                    field:"idGallery",
                    target:"position",
                    type:"1"
                });

                const container = document.getElementById("showFolder");
                container.innerHTML = data.galleryContent;

                XEditable.initAll('.editable', {
                    url: ajaxPath + 'action/process-image-gallery-info.php',
                    method: "POST",
                    onblur: "submit",

                    data: function(response, config) {

                        const el = config.element;

                        return {
							id: el.dataset.id,
							idLang: el.dataset.idLang,
							idCategory: el.dataset.category,
							table: el.dataset.table,
							field: el.dataset.field,
							idField : el.dataset.idField,
							video: "0",
							value: response
                        };
                    }
                });

				initSortable(
					"ol.sortable.imgDisplay",
					prefixImg+"img",
					"position",
					"id",
					"img",
					0
				);

				Fancybox.bind("[data-fancybox='gallery']", {
					Thumbs: true,
					Toolbar: true
				});

            } catch (e) { console.error("Error:", e); }
        };


        const showFolderGlobal = async (id) => {
            try {

                const data = await ajax.post(ajaxPath+'action/process-image-gallery-display.php', {
                    id: id,
                    idCategory: idCategory,
                    folder: 'gallery',
                    table: prefixImg + "img",
                    field: "idCategory",
                    target: "globalPosition",
                    type: "0"
                });

                const container = document.getElementById("showFolderSort");

                if (!container) {
                    return;
                }

                container.innerHTML = data.galleryContent;

                initSortable(
                    "ol.sortable.globalDisplay",
                    prefixImg + "img",
                    "globalPosition",
                    "id",
                    "img",
                    0
                );

                if (typeof Fancybox !== "undefined") {
                    Fancybox.bind("[data-fancybox='gallery']", {
                        Thumbs: true,
                        Toolbar: true
                    });
                }

            } catch (e) {
                console.error("Error:", e);
            }
        };


        const photoHeaderGallery= async (id) => {
            try {

                const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
                    entity:entityImg,
                    idParent:idCategory,
                    id: id,
                    field: "img",
                    target: "id",
                    folder: "img",
                    label: "",
                    idSite:idSite,
                    type:1
                });

                const zone = document.getElementById("photoUploadareaGallery");
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



    //---------------------------------------------------------
   	// PROCESS FRONT
   	//---------------------------------------------------------

        showFolderGlobal(idCategory);


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
                text: "Voulez-vous vraiment supprimer cette galerie photo ?",
                icon: "warning",
                width:'500px',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonColor: adminColor,
                confirmButtonText: "Confirmer",
                cancelButtonText: "Annuler"
            });

            if (!result.isConfirmed) return;

            try {

                const data = await ajax.post(ajaxPath+'gallery/process-gallery-delete.php', {
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
                    showWarningMessage("builderMsgUpdate", "Galerie images supprimée", "#81B929");
                } else {
                    throw new Error("Suppression impossible");
                }
            } catch (error) {
                console.error(error);
                showWarningMessage("builderMsgUpdate", "La suppression a échoué.", "#dd0202");
            }
        }); 


    //---------------------------------------------------------
   	// PROCESS MOD
   	//---------------------------------------------------------

        if(modId!=""){

            showFolder(modId);
            photoHeaderGallery(modId);


        //---------------------------------------------------------
        // PROCESS DELETE PHOTO GALLERY
        //---------------------------------------------------------

            document.addEventListener("click", async function (event) {

                const target = event.target.closest(".deleteImg");
                if (!target) return;

                event.preventDefault();

                const idImg = target.id;

                try {
                    const data = await ajax.post(ajaxPath+'action/process-file-delete.php', {
                        entity: entityImg,
                        id:idImg,
                        field: '',
                        target: '',
                        idParent:idCategory,
                        folder: 'gallery'
                    });

                    if (data.status == 1) {
                        showFolder(modId)
                    }else{console.log(data.status);}

                } catch (e) { console.error("Error:", e); }

            });

        //---------------------------------------------------------
        // PROCESS IMAGE GALLERY
        //---------------------------------------------------------

            const uploader = new FileUploader({
                UId: photoUId,
                uploadUrl: ajaxPath + 'action/process-file-full-upload.php',

                accept: '.jpg,.jpeg,.png,.gif,.zip',

                formDataParams: {
                    entity: entityImg,
                    folder: 'gallery',
                    id: modId,
                    idParent: idCategory,
                    type: 2,
                    idSite: idSite,
                    modId: modId
                },
                refreshCallback: (modId) => {
                    showFolder(modId);
                }
            });

    }