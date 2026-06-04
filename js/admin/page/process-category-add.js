/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/

        //---------------------------------------------------------
        // PROCESS VALIDATE 
        //---------------------------------------------------------

            window.pageFormValidator = new Validator("addpageform", {
                rules: {
                    name_1: { required: true },
                    type: { required: true }
                },
                messages: {
                    name_1: { required: "Veuillez renseigner un nom" },
                    type: { required: "Vous devez sélectionner un type de page" }
                },
                allowSubmit: true
            });