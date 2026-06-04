/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/

    //---------------------------------------------------------
   	// INIT SORTABLE
   	//---------------------------------------------------------

       initSortable(
            "ol.sortable.language",
            prefix+"lang",
            "position",
            "id",
            "list",
            0
        );


    //---------------------------------------------------------
    // PROCESS VALIDATE FORM
    //---------------------------------------------------------

		/* Main form */
		window.mainFormValidator = new Validator("modRootForm", {
			rules: {
				name: { required: true }
			},
			messages: {
				name: { required: "Veuillez renseigner un nom" },
			},
			allowSubmit: true
		});

		/* Language form Add */
		window.mainFormValidator = new Validator("modLangForm", {
			rules: {
				modNameLanguage: { required: true }
			},
			messages: {
				modNameLanguage: { required: "Veuillez renseigner un nom" },
			},
			allowSubmit: true
		});

		/* Language form Mod */
		window.mainFormValidator = new Validator("addLangForm", {
			rules: {
				nameAdd: { required: true }
			},
			messages: {
				nameAdd: { required: "Veuillez renseigner un nom" },
			},
			allowSubmit: true
		});



    //---------------------------------------------------------
    // PROCESS IMAGE REC
    //---------------------------------------------------------

		const loadImageRecForm= async (id) => {
			try {

                const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
					entity:entitySite,
					idParent:idSite,
					id: id,
					field: "img",
					target: "id",
					folder: "img",
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

		loadImageRecForm(idSite);



    //---------------------------------------------------------
    // PROCESS IMAGE SQUARE
    //---------------------------------------------------------

		const loadImageSquareForm= async (id) => {
			try {

                const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
					entity:entitySite,
					idParent:idSite,
					id: id,
					field: "imgSquare",
					target: "id",
					folder: "img",
					label: "",
					idSite:idSite,
					type:1
                });

				const zone = document.getElementById("photoSquareUploadArea");
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

		loadImageSquareForm(idSite);


    //---------------------------------------------------------
    // PROCESS IMAGE SQUARE
    //---------------------------------------------------------

		const loadImageMobileForm= async (id) => {
			try {

                const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
					entity:entitySite,
					idParent:idSite,
					id: id,
					field: "imgMobile",
					target: "id",
					folder: "img",
					label: "",
					idSite:idSite,
					type:1
                });

				const zone = document.getElementById("photoMobileUploadArea");
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

		loadImageMobileForm(idSite);


    //---------------------------------------------------------
    // PROCESS IMAGE LANGUAGE
    //---------------------------------------------------------

		const loadImageLanguageForm= async (id) => {
			try {

                const data = await ajax.post(ajaxPath+'action/process-file-add.php', {
					entity:entityLang,
					idParent:id,
					id: id,
					field: "img",
					target: "id",
					folder: "img",
					label: "",
					idSite:idSite,
					type:1
                });

				const zone = document.getElementById("imgUploadArea");
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

		if(modStatus==1){
			loadImageLanguageForm(modId);
		}