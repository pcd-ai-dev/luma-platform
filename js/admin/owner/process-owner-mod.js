/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2012 Lumaprod - Pierre Cosmao Dumanoir
*/


	//---------------------------------------------------------
	// PROCESS VALIDATE FORM
	//---------------------------------------------------------

		/* Main form */
		window.mainFormValidator = new Validator("modOwnerform", {
			rules: {
				first_name: { required: true },
				last_name: { required: true },
				emails: {
					required: true,
					email: true
				},
				pass: {
					passwordStrength: true
				}
			},
			messages: {
				first_name: { required: "Veuillez renseigner un nom" },
				last_name: { required: "Veuillez renseigner un prénom" },
				emails: {
					required: "Renseignez votre email",
					email: "Votre email n'est pas valide"
				},
				pass: {
				}
			},
			allowSubmit: true

		});


    //---------------------------------------------------------
    // PROCESS IMAGE SQUARE
    //---------------------------------------------------------

		const loadOwnerImageForm= async (id) => {
			try {

				const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
					entity:entityAdmin,
					idParent:id,
					id: id,
					field: "img",
					target: "id",
					folder: "",
					label: "",
					idSite:idSite,
                    type:1
				});

				const zone = document.getElementById("photoUploadArea");
				zone.innerHTML = data.formContent;

				zone.querySelectorAll("script").forEach(script => {
					eval(script.textContent);
				});

				if (window.__uploaders) {
					window.__uploaders.forEach(cfg => initFileUploader(cfg));
					window.__uploaders = [];
				}

			} catch (e) { console.error("Error:", e); }
		};

		loadOwnerImageForm(adminId);


	//---------------------------------------------------------
	// OPEN SCHEDULER PDF
	//---------------------------------------------------------

		document.addEventListener("click", function (event) {
			const target = event.target.closest(".paysage");
			if (!target) return;

			event.preventDefault();

			const idMonth = target.dataset.month;
			const idYear = target.dataset.year;

			window.open("/owner_planning_pdf/"+adminId+"/"+idMonth+"/"+idYear+"/", "_blank");
		});


	//---------------------------------------------------------
	// DISPLAY SCHEDULER FUNCTION
	//---------------------------------------------------------
		
		async function showScheduler(ownerId, idMonth, idYear) {

			const idData = "" + idMonth + idYear;

			try {

				const data = await ajax.post(ajaxPath+'owner/process-ownerScheduler.php', {
					ownerId: ownerId,
					idMonth: idMonth,
					idYear: idYear,
					time: new Date().getTime()
				});

				const contentArea = document.getElementById("dataArea_" + idData);

				if (data.status === 0) {
					contentArea.innerHTML = "<br/>Une erreur s'est produite.<br/>";
				} else {
					contentArea.innerHTML = data.dataContent;
					slideDown(contentArea);

					XEditable.initAll('.editable_status', {
						url: ajaxPath+'owner/process-ownerStatus.php',
						headers: { "Content-Type": "application/x-www-form-urlencoded" },
						type: "select",
						onblur: "submit",
						ajaxOptions: {
							type: "POST",
							contentType: "application/json"
						},
						source: async function() {
							return await ajax.post(ajaxPath+'owner/process-selectOwnerStatus.php', {});
						},
						select2: {
							minimumResultsForSearch: Infinity
						},
						data: function(value, config) {
						const el = config.element;

							return {
								id: el.dataset.id,
								value: value,
								idLang: "1",
								status: "1"
							};
						},
						success: function(response, config) {
							const target=document.getElementById("target_"+response.id);
							target.style.backgroundColor = response.colorStatus;	
						}
					});

					XEditable.initAll('.editable_type', {
						url: ajaxPath+'owner/process-ownerType.php',
						headers: { "Content-Type": "application/x-www-form-urlencoded" },
						type: "select",
						onblur: "submit",
						ajaxOptions: {
							type: "POST",
							contentType: "application/json"
						},
						source: async function() {
							return await ajax.post(ajaxPath+'owner/process-selectOwnerType.php', {});
						},
						select2: {
							minimumResultsForSearch: Infinity
						},
						data: function(value, config) {
							const el = config.element;

							return {
								id: el.dataset.id,
								value: value,
								idLang: "1",
								status: "1"
							};
						},
						success: function(response, config) {
						}
					});

				}

			} catch (e) { console.error("Error:", e); }

		}


	//---------------------------------------------------------
	// EXPAND / COLLAPSE
	//---------------------------------------------------------

		const isUExpandedData = {};

		document.addEventListener("click", function (e) {
			const target = e.target.closest(".dataExpand");
			if (!target) return;

			e.preventDefault();

			const idData = target.id;
			const idMonth = target.dataset.month;
			const idYear = target.dataset.year;
			const ownerId = adminId;

			if (isUExpandedData[idData] === undefined || isUExpandedData[idData] === null) {
				isUExpandedData[idData] = true;
			}

			const dataArea = document.getElementById("dataArea_" + idData);

			if (isUExpandedData[idData]) {
				target.innerHTML = "&#x2212;";
				dataArea.innerHTML = "";

				showScheduler(ownerId, idMonth, idYear);

				isUExpandedData[idData] = false;
			} else {
				target.innerHTML = "&#x2b;";
				slideUp(dataArea);

				isUExpandedData[idData] = true;
			}
		});