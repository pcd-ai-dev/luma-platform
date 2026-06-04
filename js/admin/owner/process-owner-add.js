/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/

	//---------------------------------------------------------
	// PROCESS VALIDATE FORM
	//---------------------------------------------------------

		/* Main form */
		window.mainFormValidator = new Validator("addOwnerform", {
			rules: {
				first_name: { required: true },
				last_name: { required: true },
				emails: {
					required: true,
					email: true,
					remote: {
						url: ajaxPath + "owner/process-check-mailuser.php",
						data: {
							idSite: function () {
								return $idSite;
							}
						}
					}
				},
				pass: {
					required: true,
					minlength: 6,
					passwordStrength: true
				}
			},
			messages: {
				first_name: { required: "Veuillez renseigner un nom" },
				last_name: { required: "Veuillez renseigner un prénom" },
				emails: {
					required: "Renseignez votre email",
					email: "Votre email n'est pas valide",
					remote: "Un compte existe déjà avec ce mail"
				},
				pass: {
					required: "Renseignez votre mot de passe",
					minlength: "6 caractères minimum"
				}
			},
			allowSubmit: true
		});