/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
   	// POST ADMIN
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
		// DATE SETUP
		//---------------------------------------------------------

			const options = {
				enableTime: true,
				dateFormat: "Y-m-d H:i",
				time_24hr: true,
				locale: "fr"
			};

			flatpickr("#datePost", options);
			flatpickr("#modDatePost", options);


        //---------------------------------------------------------
        // PROCESS DISPLAY POST MaIN IMG UPLOAD
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