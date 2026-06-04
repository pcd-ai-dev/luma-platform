/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
   	// IMAGE PAGE ADMIN
   	//---------------------------------------------------------

    //---------------------------------------------------------
   	// INTERFACE
   	//---------------------------------------------------------

		document.querySelectorAll('.headerformtoggle').forEach(header => {
			header.addEventListener('click', function() {

				const content = this.nextElementSibling;
				const button = this.querySelector('.modifAreaSelector');

				slideToggle(content, duration = 500)

				content.classList.toggle('open');
				button.classList.toggle('closeArea');
			});
		});


    //---------------------------------------------------------
   	// PROCESS DISPLAY GALLERY PAGE
   	//---------------------------------------------------------

        const showPagePhoto= async (id) => {
            try {
				const data = await ajax.post(ajaxPath+'action/process-image-gallery-display.php', {
					id: id,
                    idCategory:idCategory,
                	folder:'pages',
               		table:prefixPage+"pages_img",
                	field:"idCategory",
                	target:"position",
                	type:"1"
				});

                const container = document.getElementById("showPagePhotoFolder");
                container.innerHTML = data.galleryContent;

                XEditable.initAll('.editable', {
                    url: ajaxPath + 'action/process-image-gallery-info.php',
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    method: "POST",
                    onblur: "submit",
                    data: function(response, config) {
                        const el = config.element;
                        return {
                            id: el.dataset.id,
                            idLang: el.dataset.idLang,
                            idCategory: el.dataset.category,
                            table: el.dataset.table,
                            video: "0",
                            value: response
                        };
                    }
                });

				initSortable(
					"ol.sortable.imgDisplay",
					prefixPage+"pages_img",
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

      	showPagePhoto(idCategory);


    //---------------------------------------------------------
    // PROCESS DELETE PHOTO GALLERY
    //---------------------------------------------------------

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
					entity: entityPage,
                    id:idImg,
                    field: '',
                    target: '',
                    idParent:idCategory,
                    folder: 'gallery'
				});

				if (data.status == 1) {
					showPagePhoto(idCategory);
                }else{
					console.log(data.status);
				}

			} catch (e) { console.error("Error:", e); }

        });


    //---------------------------------------------------------
    // PROCESS IMAGE GALLERY
    //---------------------------------------------------------

        const uploader = new FileUploader({
            UId: pageUId,
            uploadUrl: ajaxPath + 'action/process-file-full-upload.php',

            accept: '.jpg,.jpeg,.png,.gif,.zip',

            formDataParams: {
                entity: entityPage,
                folder: 'gallery',
                id: idCategory,
                idParent: idCategory,
                type: 2,
                idSite: idSite,
                modId: idCategory
            },
            refreshCallback: (modId) => {
				showPagePhoto(modId);
            }
        });

