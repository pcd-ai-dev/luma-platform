/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/



    //---------------------------------------------------------
   	// CONTACT ADMIN
   	//---------------------------------------------------------

    //---------------------------------------------------------
   	// VALIDATE ADD GALLERY
   	//---------------------------------------------------------

        window.photoFormValidator = new Validator("contactAddForm", {
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

        window.photoFormValidator = new Validator("contactModForm", {
            rules: {
                modTitleContact_1: { required: true }
            },
            messages: {
                modTitleContact_1: { required: "Veuillez renseigner un nom" },
            },
			allowSubmit: true
        });



    //---------------------------------------------------------
   	// INIT SORTABLE
   	//---------------------------------------------------------

       initSortable(
            "ol.sortable.contact",
            prefixContact+"item",
            "position",
            "id",
            "list",
            0
        );

    //---------------------------------------------------------
   	// DELETE CONTACT
   	//---------------------------------------------------------

        document.addEventListener("click", async (event) => {
            const btn = event.target.closest(".deleteContact");
            if (!btn) return;

            event.preventDefault();

            const id = btn.id;

            const result = await Swal.fire({
                title: "Suppression",
                text: "Voulez-vous vraiment supprimer ce contact ?",
                icon: "warning",
                width:'450px',
                showCancelButton: true,
                confirmButtonColor: "#21c994",
                confirmButtonText: "Confirmer",
                cancelButtonText: "Annuler"
            });

            if (!result.isConfirmed) return;

            try {

                const data = await ajax.post(ajaxPath+'contact/process-delete.php', {
                    id: id,
                    idCategory: idCategory,
                    time: Date.now()
                });

                if (data.status == 1) {
                    const element = document.querySelector("#list_contact_" + data.id);

                    if (element) {
                        element.style.transition = "opacity 0.3s ease";
                        element.style.opacity = "0";
                        setTimeout(() => element.remove(), 300);
                    }

                    showWarningMessage("builderMsgUpdate", "Galerie vidéo supprimée", "#81B929");

                } else {
                    throw new Error("Suppression impossible");
                }

            } catch (error) {
                console.error(error);
                showWarningMessage("builderMsgUpdate", "La suppression a échouée.", "#dd0202");

            }
        });
