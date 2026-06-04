/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
   	// NEWS ADMIN
   	//---------------------------------------------------------

    //---------------------------------------------------------
   	// VALIDATE ADD POST
   	//---------------------------------------------------------

        window.postAddFormValidator = new Validator("postAddForm", {
            rules: {
                titleAdd_1: { required: true }
            },
            messages: {
                titleAdd_1: { required: "Veuillez renseigner un nom" },
            },
			allowSubmit: true
        });


    //---------------------------------------------------------
   	// VALIDATE ADD POST
   	//---------------------------------------------------------

        window.postModFormValidator = new Validator("postModForm", {
            rules: {
                modTitlePost_1: { required: true }
            },
            messages: {
                modTitlePost_1: { required: "Veuillez renseigner un nom" },
            },
			allowSubmit: true
        });


   	//---------------------------------------------------------
	// POST DISPLAY
   	//---------------------------------------------------------

       const showPost = async (id) => {
            try {

				const data = await ajax.post(ajaxPath+'post/process-search.php', {
					idCategory: id,
					time: Date.now()
				});

                const container = document.getElementById("results");

                if(!container){return;}

                container.innerHTML = data.searchContent;

            } catch (e) {
                console.error("Error:", e);
            }
        };

	
		showPost(idCategory);


   	//---------------------------------------------------------
	// SEARCH PROCESS
   	//---------------------------------------------------------

		const inputSearch = document.getElementById('q');
		const clearBtnSearch = document.querySelector('.clear-search');
		const resultsSearch = document.getElementById('results');
		const formSearch= document.querySelector('.form');
		const resultItem = document.getElementById('results');

		async function performSearch(query){

			resultsSearch.innerHTML = '';
			resultItem.style.display = 'block';

			try {

				const data = await ajax.post(ajaxPath+'post/process-search.php', {
					q: query,
					idCategory: idCategory
				});

				resultsSearch.innerHTML = data.searchContent;

			} catch (e) { console.error("Error:", e); }

		}

		inputSearch.addEventListener('keyup', function (event) {
			event.preventDefault();
			performSearch(inputSearch.value);
		});

		inputSearch.addEventListener('input', function () {
			resultsSearch.style.display = inputSearch.value ? 'flex' : 'none';
		});

		clearBtnSearch.addEventListener('click', function () {
			inputSearch.value = '';
			inputSearch.focus();
			clearBtnSearch.style.display = 'none';
			performSearch('');
		});


		//---------------------------------------------------------
		// DATE SETUP ADD
		//---------------------------------------------------------

			const options = {
				enableTime: true,
				dateFormat: "Y-m-d H:i",
				time_24hr: true,
				locale: "fr"
			};

			flatpickr("#datePost", options);



    //---------------------------------------------------------
   	// MOD PROCESS
   	//---------------------------------------------------------

		if(modStatus==1){

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
		// DATE SETUP MOD
		//---------------------------------------------------------

			flatpickr("#modDate", options);
			flatpickr("#modStartDate", options);
			flatpickr("#modEndDate", options);


        //---------------------------------------------------------
        // PROCESS DISPLAY POST MAIN IMG UPLOAD
        //---------------------------------------------------------

			const loadPostImageForm= async (id) => {
				try {

					const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
						entity:entityPost,
						idParent:idCategory,
						id: id,
						field: "img",
						target: "id",
						folder: "img",
						label: "",
						idSite:idSite,
                    	type:1
					});

					const container = document.getElementById("photoUploadAreaPost");
					container.innerHTML = data.formContent;

					container.querySelectorAll("script").forEach(script => {
						eval(script.textContent);
					});

					if (window.__uploaders) {
						window.__uploaders.forEach(cfg => initFileUploader(cfg));
						window.__uploaders = [];
					}

				} catch (e) { console.error("Error:", e); }
			};

			loadPostImageForm(modId);


        //---------------------------------------------------------
        // PROCESS DISPLAY POST FILE UPLOAD
        //---------------------------------------------------------

			const loadPostFileForm= async (id) => {
				try {

					const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
						entity:entityPost,
						idParent:idCategory,
						id: id,
						field: "url",
						target: "id",
						folder: "files",
						label: "",
						idSite:idSite,
						type:3
					});

					const container = document.getElementById("fileUploadArea");
					container.innerHTML = data.formContent;

					container.querySelectorAll("script").forEach(script => {
						eval(script.textContent);
					});

					if (window.__uploaders) {
						window.__uploaders.forEach(cfg => initFileUploader(cfg));
						window.__uploaders = [];
					}

				} catch (e) { console.error("Error:", e); }
			};

			loadPostFileForm(modId);



        //---------------------------------------------------------
        // PROCESS DISPLAY POST IMG GALLERY
        //---------------------------------------------------------

			const showFolderImgPost= async (id) => {
				try {

					const data = await ajax.post(ajaxPath+'action/process-image-gallery-display.php', {
						entity:entityPost,
						id: id,
						idCategory:idCategory,
						folder:'post',
						table:prefixPost+"img",
						field:"idPost",
						target:"position",
						type:"1"
					});

					const container = document.getElementById("showFolderArea");
					container.innerHTML = data.galleryContent;
					
					container.querySelectorAll("script").forEach(script => {
						eval(script.textContent);
					});

					if (window.__uploaders_Posters) {
						window.__uploaders_Posters.forEach(cfg => initFileUploader(cfg));
						window.__uploaders_Posters = [];
					}


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

					initSortable("ol.sortable.imgDisplay", prefixPost+"img", "position", "id", "img", 0);

					Fancybox.bind("[data-fancybox='gallery']", {Thumbs: true,Toolbar: true});

				} catch (e) { console.error("Error:", e); }
			};

			showFolderImgPost(modId);


        //---------------------------------------------------------
        // PROCESS UPLOAD VIDEO
        //---------------------------------------------------------

			const loadPostVideoForm= async (id) => {
				try {

					const data = await ajax.post(ajaxPath+'post/process-add-video.php', {
						idCategory:idCategory,
						time: Date.now()
					});

					const container = document.getElementById("videoArea");
					container.innerHTML = data.formContent;

				} catch (e) { console.error("Error:", e); }
			};

			loadPostVideoForm(modId);


		//---------------------------------------------------------
		// PROCESS VIDEO FORM TREATMENT
		//---------------------------------------------------------

			document.addEventListener("click", async (event) => {
				const btn = event.target.closest("#videoForm a");
				if (!btn) return;

				event.preventDefault();

				const videoCode = document.getElementById("videoCode").value;

				if (videoCode == "") {} else {

					try {

						const data = await ajax.post(ajaxPath+'post/process-add-video-DB.php', {
							idPost: modId,
							idCategory: idCategory,
							videoCode: videoCode,
							time: Date.now()
						});

						if (data.status == 1) {

							showFolderImgPost(modId);
							document.getElementById("videoCode").value = "";

							showWarningMessage("builderMsgUpdate", "La vidéo a bien été ajoutée", "#81B929");
						} else {
							throw new Error("Suppression impossible");
						}
					} catch (error) {
						console.error(error);
						showWarningMessage("builderMsgUpdate", "L'ajout a échoué", "#dd0202");
					}

				}

			});


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
						entity: entityPost,
                        id:idImg,
                        field: '',
                        target: '',
                        idParent:idCategory,
                        folder: 'gallery'
					});

					if (data.status == 1) {
                        showFolderImgPost(modId)
                    }else{
						console.log(data.status);
					}

				} catch (e) { console.error("Error:", e); }

            });


        //---------------------------------------------------------
        // PROCESS IMAGE GALLERY
        //---------------------------------------------------------

            const uploader = new FileUploader({
                UId: postUId,
                uploadUrl: ajaxPath + 'action/process-file-full-upload.php',

                accept: '.jpg,.jpeg,.png,.gif,.zip',

                formDataParams: {
                    entity: entityPost,
                    folder: 'gallery',
                    id: modId,
                    idParent: idCategory,
                    type: 2,
					idSite: idSite,
                    modId: modId
                },
                refreshCallback: (modId) => {
                    showFolderImgPost(modId);
                }
            });


        //---------------------------------------------------------
        // PROCESS GOOGLE PLACE MOD
        //---------------------------------------------------------

			function initMap() {
				placeLoc(1);
			}

			function placeLoc(status) {
				if (status == 0) return;

				const input = document.getElementById('modAddress');

				function getComponent(components, type) {
					const found = components.find(c => c.types.includes(type));
					return found ? found.shortText : '';
				}

				const autocomplete = new google.maps.places.PlaceAutocompleteElement();
				autocomplete.style.colorScheme = 'light';
				autocomplete.setAttribute('placeholder', 'Rechercher une adresse...');
				autocomplete.value = modAddress;
				input.parentNode.replaceChild(autocomplete, input);

				autocomplete.addEventListener('gmp-select', async function(event) {
					const place = event.placePrediction.toPlace();

					await place.fetchFields({
						fields: ['displayName', 'location', 'addressComponents',
								'formattedAddress', 'nationalPhoneNumber', 'websiteURI', 'googleMapsURI']
					});

					const components = place.addressComponents ?? [];
					const streetNum  = getComponent(components, 'street_number');
					const route      = getComponent(components, 'route');
					const city       = getComponent(components, 'locality');
					const cp         = getComponent(components, 'postal_code');
					const addressSet = [streetNum, route].filter(Boolean).join(' ');

					document.getElementById('modPlace').value     = place.displayName         ?? '';
					document.getElementById('modLattitude').value = place.location.lat();
					document.getElementById('modLongitude').value = place.location.lng();
					document.getElementById('modAddress').value   = addressSet;
					document.getElementById('modCity').value      = city;
					document.getElementById('modCp').value        = cp;
					document.getElementById('modPhone').value     = place.nationalPhoneNumber ?? '';
					document.getElementById('modUrl').value       = place.websiteURI          ?? '';
					document.getElementById('modLink').value      = place.googleMapsURI       ?? '';
				});
			}
	}



    //---------------------------------------------------------
   	// PROCESS POST
   	//---------------------------------------------------------

        document.addEventListener("click", async (event) => {
            const btn = event.target.closest(".deletePost");
            if (!btn) return;

            event.preventDefault();

            const id = btn.id;

            const result = await Swal.fire({
                title: "Suppression",
                text: "Voulez-vous vraiment supprimer ce post ?",
                icon: "warning",
                width:'450px',
                showCancelButton: true,
                confirmButtonColor: "#21c994",
                confirmButtonText: "Confirmer",
                cancelButtonText: "Annuler"
            });

            if (!result.isConfirmed) return;

            try {

				const data = await ajax.post(ajaxPath+'post/process-delete.php', {
					id: id,
					idCategory: idCategory,
                    time: Date.now()
				});

                if (data.status == 1) {
                    const element = document.querySelector("#list_post_" + data.id);

                    if (element) {
                        element.style.transition = "opacity 0.3s ease";
                        element.style.opacity = "0";
                        setTimeout(() => element.remove(), 300);
                    }

                    showWarningMessage("builderMsgUpdate", "Enregistrement supprimée", "#81B929");

                } else {
                    throw new Error("Suppression impossible");
                }

            } catch (error) {
                console.error(error);

                showWarningMessage("builderMsgUpdate", "La suppression a échouée.", "#dd0202");

            }
        });