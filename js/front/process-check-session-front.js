/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/

    //---------------------------------------------------------
    // FRONT SESSION CHEKCK
    //---------------------------------------------------------

		(function () {

			let sessionLostDisplayed = false;

			async function checkFrontSession() {

				if (sessionLostDisplayed) {
					return false;
				}

				const meta = document.querySelector('meta[name="csrf-token"]');

				if (!meta) {
					return false;
				}

				const token = meta.getAttribute('content');

				try {

					const data = await ajax.post(ajaxPath + "session/process-check-session-front.php", {});

					// session expirée ou token différent
					if (!data.valid) {

						sessionLostDisplayed = true;

						showWarningMessage("msgFrontUpdate", "Session perdue, veuillez recharger la page", "#81B929", 60000);

						return false;
					}

					return true;

				} catch (error) {

					console.error(error);

					showWarningMessage("msgFrontUpdate", "Erreur de connexion", "#d9534f", 60000);

					return false;
				}
			}

			checkFrontSession();

			setInterval(checkFrontSession, 30000);

		})();