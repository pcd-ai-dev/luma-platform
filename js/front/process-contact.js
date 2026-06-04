/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/

    //---------------------------------------------------------
    // FRONT CONTACT JS
    //---------------------------------------------------------

    //---------------------------------------------------------
   	// VALIDATE EMAIL FORM
   	//---------------------------------------------------------

        window.contactFormValidator = new Validator("contactFormData", {
            rules: {
                email: {
					required: true,
					email:true
				},
				name: {required: true},
				cgv: {required: true}
            },
            messages: {
                email: {
					required : emailRequired,
					email : emailFormat
				},
				name: nameRequired,
				cgv: cgvRequired
            },
			allowSubmit: true
        });