/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


  	//---------------------------------------------------------
   	// INIT
   	//---------------------------------------------------------

       initSortable(
            "ol.sortable",
            prefix+"user",
            "position",
            "id",
			"list",
			1
        );
        

    //---------------------------------------------------------
   	// PROCESS DELETE PAGE
   	//---------------------------------------------------------

        document.addEventListener("click", async (event) => {
            const btn = event.target.closest(".delete");
            if (!btn) return;

            event.preventDefault();

            const id = btn.id;

            const result = await Swal.fire({
                title: "Suppression",
                text: "Voulez vous vraiment supprimer ce collaborateur ?",
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

                const data = await ajax.post(ajaxPath+'owner/process-delete.php', {
					id: id,
                    time: Date.now()
				});

                if (data.status == 1) {

					const element = document.getElementById("list_owner_" + data.id);

                    if (element) {
                        element.style.transition = "opacity 0.3s ease";
                        element.style.opacity = "0";
                        setTimeout(() => element.remove(), 300);
                    }

                    showWarningMessage("builderMsgUpdate", "La profil a bien été supprimée", "#81B929");

                } else {
                    throw new Error("Suppression impossible");
                }

            } catch (error) {
                console.error(error);

                showWarningMessage("builderMsgUpdate", "La suppression a échouée.", "#dd0202");

            }
        });