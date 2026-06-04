/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
    // PROCESS VALIDATE FORM
    //---------------------------------------------------------

		/* Main form */
		window.mainFormValidator = new Validator("addSiteform", {
			rules: {
				name: { required: true }
			},
			messages: {
				name: { required: "Veuillez renseigner un nom" },
			},
			allowSubmit: true
		});