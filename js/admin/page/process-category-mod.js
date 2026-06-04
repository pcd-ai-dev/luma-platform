/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/



        //---------------------------------------------------------
        // PROCESS VALIDATE 
        //---------------------------------------------------------

			/* Main form */
			window.mainFormValidator = new Validator("mainForm", {
				rules: {
					name_1: { required: true },
					type: { required: true }
				},
				messages: {
					name_1: { required: "Veuillez renseigner un nom" },
					type: { required: ">Vous devez sélectionner un type de page" }
				},
				allowSubmit: true
			});

			/* header form */
			window.headerFormValidator = new Validator("headerForm", {
				rules: {
					title_1: { required: true }
				},
				messages: {
					title_1: { required: "Veuillez renseigner un titre de page" }
				},
				allowSubmit: true
			});


        //---------------------------------------------------------
        // PROCESS IMAGE SQUARE
        //---------------------------------------------------------

			const loadPageImageForm= async (id) => {
				try {

					const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
						entity:entityPage,
						idParent:idCategory,
						id: id,
						field: "img",
						target: "id",
						folder: "header",
						label: "",
						idSite:idSite,
                    	type:1
					});

					const zone = document.getElementById("photoUploadPageArea");
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

			loadPageImageForm(idCategory);