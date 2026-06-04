/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
   	// INIT SORTABLE
   	//---------------------------------------------------------

       initSortable(
            "ol.sortable.site",
            prefix+"site",
            "position",
            "id",
            "list",
            0
        );


    //---------------------------------------------------------
   	// PROCESS DELETE SITE
   	//---------------------------------------------------------

        document.addEventListener("click", async (event) => {
            const btn = event.target.closest(".deleteSite");
            if (!btn) return;

            event.preventDefault();

            const id = btn.id;

            const result = await Swal.fire({
                title: "Suppression",
                text: "Voulez-vous vraiment supprimer ce site ?",
                icon: "warning",
                width:'450px',
                showCancelButton: true,
                confirmButtonColor: adminColor,
                confirmButtonText: "Confirmer",
                cancelButtonText: "Annuler"
            });

            if (!result.isConfirmed) return;

            try {

                const data = await ajax.post(ajaxPath+'root/process-delete.php', {
                    id: id,
					time: Date.now()
                });

                if (data.status == 1) {
                    const element = document.querySelector("#list_site_" + data.id);

                    if (element) {
                        element.style.transition = "opacity 0.3s ease";
                        element.style.opacity = "0";
                        setTimeout(() => element.remove(), 300);
                    }

                    showWarningMessage("builderMsgUpdate", "Le site a bien été supprimé", "#81B929");

                } else {
                    throw new Error("Suppression impossible");
                }

            } catch (error) {
                console.error(error);

                showWarningMessage("builderMsgUpdate", "La suppression a échouée.", "#dd0202");

            }
        });