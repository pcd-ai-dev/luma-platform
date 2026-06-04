/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


//---------------------------------------------------------
// AUTOCOMPLETE CLASS
//---------------------------------------------------------


    class Bootcomplete {
        constructor(input, options = {}) {
            if (!input) throw new Error("Bootcomplete: input required");

            this.input = input;

            this.settings = Object.assign({
                url: null,
                method: "GET",
                wrapperClass: "bc-wrapper",
                menuClass: "bc-menu",
                idField: true,
                idFieldName: input.name + "_id",
                minLength: 3,
                dataParams: {}
            }, options);

            if (!this.settings.url) {
                throw new Error("Bootcomplete: url is required");
            }

            this.init();
        }

        init() {
            this.input.setAttribute("autocomplete", "off");

            const wrapper = document.createElement("div");
            wrapper.className = this.settings.wrapperClass;
            this.input.parentNode.insertBefore(wrapper, this.input);
            wrapper.appendChild(this.input);

            // hidden field
            if (this.settings.idField) {
                let hidden = document.querySelector(`input[name="${this.settings.idFieldName}"]`);
                if (!hidden) {
                    hidden = document.createElement("input");
                    hidden.type = "hidden";
                    hidden.name = this.settings.idFieldName;
                    wrapper.insertBefore(hidden, this.input);
                }
            }

            // menu
            this.menu = document.createElement("div");
            this.menu.className = `${this.settings.menuClass} list-group`;
            this.menu.style.display = "none";
            wrapper.appendChild(this.menu);

            this.input.addEventListener("keyup", () => this.search());
            this.input.addEventListener("blur", () => this.hide());

            this.abortController = null;
        }

        hide() {
            setTimeout(() => {
                if (!this.menu.matches(':hover')) {
                    this.menu.style.display = "none";
                }
            }, 150);
        }

        async search() {
            const query = this.input.value.trim();

            if (query.length < this.settings.minLength) {
                this.menu.innerHTML = "";
                this.menu.style.display = "none";
                return;
            }

            if (this.abortController) {
                this.abortController.abort();
            }

            this.abortController = new AbortController();

            const params = new URLSearchParams({
                search: query,
                ...this.settings.dataParams
            });

            try {
                const response = await fetch(this.settings.url, {
                    method: this.settings.method,
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: this.settings.method === "POST" ? params : null,
                    signal: this.abortController.signal
                });

                const json = await response.json();

                // 🔥 NORMALISATION ICI
                let results = [];

                if (Array.isArray(json)) {
                    results = json;
                } else if (json && Array.isArray(json.data)) {
                    results = json.data;
                }

                this.renderResults(results);

            } catch (e) {
                if (e.name !== "AbortError") {
                    console.error("Bootcomplete error:", e);
                    this.menu.style.display = "none";
                }
            }
        }

        renderResults(results) {
            this.menu.innerHTML = "";

            if (!results || results.length === 0) {
                const a = document.createElement("a");
                a.href = "#";
                a.className = "list-group-item";
                a.textContent = "Cliquez ici, pour créer ce dossier.";

                a.addEventListener("click", (e) => {
                    e.preventDefault();
                    this.addProject();
                });

                this.menu.appendChild(a);

                document.querySelectorAll('.folderStatus')
                    .forEach(el => el.style.display = "none");

            } else {
                results.forEach(item => {
                    const a = document.createElement("a");
                    a.href = "#";
                    a.className = "list-group-item";
                    a.textContent = item.label;
                    a.dataset.id = item.id;

                    a.addEventListener("click", (e) => {
                        e.preventDefault();
                        this.select(item);
                    });

                    this.menu.appendChild(a);
                });
            }

            this.menu.style.display = "block";
        }

        select(item) {
            this.input.value = item.label;

            if (this.settings.idField) {
                const hidden = document.querySelector(`input[name="${this.settings.idFieldName}"]`);
                if (hidden) {
                    hidden.value = item.id;
                    hidden.dispatchEvent(new Event("change"));
                }
            }

            this.menu.style.display = "none";

            document.querySelectorAll('.folderStatus')
                .forEach(el => el.style.display = "block");

            const folderUrl = `/tree/project_page-admin_project-param_add-${item.id}-2-2-1-0.php`;
            document.querySelector('#innerTrombone a')?.setAttribute("href", folderUrl);
        }

        async addProject() {
            const projectName = this.input.value.trim();
            if (!projectName) return;

            try {
                const response = await fetch('/includes/ajax/scheduler/process-addProject.php', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: new URLSearchParams({ projectName })
                });

                const data = await response.json();

                if (data && !data.error && Number(data.status) === 1 && data.idProject) {
                    const hidden = document.querySelector(`input[name="${this.settings.idFieldName}"]`);
                    if (hidden) {
                        hidden.value = data.idProject;
                        hidden.dispatchEvent(new Event("change"));
                    }

                    this.menu.style.display = "none";

                    document.querySelectorAll('.folderStatus')
                        .forEach(el => el.style.display = "block");

                    const folderUrl = `/manager${window.varLink || ''}/admin_project/param_main/${data.idProject}/1/0/0/`;
                    document.querySelector('#innerTrombone a')?.setAttribute("href", folderUrl);

                } else {
                    this.menu.innerHTML = "Oups, une erreur s'est produite !";
                }

            } catch (e) {
                console.error("Add project error:", e);
            }
        }
    }