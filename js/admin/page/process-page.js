/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


   	//---------------------------------------------------------
   	// INIT
   	//---------------------------------------------------------

       initSortable(
            "ol.sortable",
            prefix+"category",
            "position",
            "id",
			"listRoot",
			4
        );

    //---------------------------------------------------------
   	// PROCESS DUPLICATE
   	//---------------------------------------------------------

        document.addEventListener("click", async function (event) {
            const target = event.target.closest(".gg-duplicate");
            if (!target) return;

            event.preventDefault();

            const idDuplicate = target.id;

            try {

                const data = await ajax.post(ajaxPath+'pages/process-duplicate.php', {
					idDuplicate:idDuplicate,
                    time: Date.now()
				});

                if(data.status==0){
				}else{
					const insertClone = document.getElementById("list_page_" + data.idCategory);
					insertClone.insertAdjacentHTML("beforeend", data.newPage);
					showWarningMessage("builderMsgUpdate", "Page dupliquée avec succès", "#81B929");
				}

            } catch (e) {
                showWarningMessage("builderMsgUpdate", "La suppression a échoué.", "#dd0202");
                console.error("Error:", e);
            }

        });
	


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
                text: "Voulez-vous vraiment supprimer cette page ?",
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

                const data = await ajax.post(ajaxPath+'pages/process-delete.php', {
					id: id,
                    time: Date.now()
				});

                if (data.status == 1) {

					const element = document.getElementById("list_page_" + data.idCategory);

                    if (element) {
                        element.style.transition = "opacity 0.3s ease";
                        element.style.opacity = "0";
                        setTimeout(() => element.remove(), 300);
                    }

                    showWarningMessage("builderMsgUpdate", "La page a bien été supprimée", "#81B929");

                } else {
                    throw new Error("Suppression impossible");
                }

            } catch (error) {
                console.error(error);

                showWarningMessage("builderMsgUpdate", "La suppression a échoué.", "#dd0202");

            }
        });