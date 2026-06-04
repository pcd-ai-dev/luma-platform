/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


//---------------------------------------------------------
// TAGS INPUT CLASS
//---------------------------------------------------------

    class TagInput {
        constructor(container, options = {}) {
            this.container = container;
            this.autocompleteUrl = options.autocompleteUrl || "";

            this.input = container.querySelector(".tag-input");
            this.tagsDiv = container.querySelector(".tags");
            this.hiddenInput = container.querySelector(".tags-hidden");
            this.suggestionsBox = container.querySelector(".suggestions");

            if (!this.input || !this.tagsDiv || !this.hiddenInput) return;

            this.tags = [];
            this.debounceTimer = null;

            this.init();
        }

        init() {
            this.loadInitialTags();
            this.bindEvents();
            this.renderTags();
        }

        // 🔹 Ajouter tag
        addTag(tag) {
            tag = tag.trim();
            if (!tag || this.tags.includes(tag)) return;

            this.tags.push(tag);
            this.renderTags();
            this.input.value = "";
            this.clearSuggestions();
        }

        // 🔹 Supprimer tag
        removeTag(tag) {
            const el = [...this.tagsDiv.querySelectorAll(".tag")]
                .find(t => t.firstChild.textContent === tag);

            if (el) {
                el.classList.add("removing");
                setTimeout(() => {
                    this.tags = this.tags.filter(t => t !== tag);
                    this.renderTags();
                }, 150);
            }
        }

        // 🔹 Render
        renderTags() {
            this.tagsDiv.innerHTML = "";

            this.tags.forEach(tag => {
                const tagEl = document.createElement("div");
                tagEl.className = "tag";

                const text = document.createElement("span");
                text.textContent = tag;

                const removeBtn = document.createElement("span");
                removeBtn.className = "tag-remove";
                removeBtn.innerHTML = `
                    <svg viewBox="0 0 12 12" width="12" height="12">
                        <path fill="#FFFFFF" d="M6.37,3.58L9.45.52l2.03,2.03-3.41,3.46 3.42,3.44-2.04,2.03-3.45-3.43-3.44,3.43-2.04-2.04 3.43-3.45L.52,2.56 2.56.52l3.4,3.4z"/>
                    </svg>
                `;

                removeBtn.onclick = () => this.removeTag(tag);

                tagEl.appendChild(text);
                tagEl.appendChild(removeBtn);
                this.tagsDiv.appendChild(tagEl);
            });

            this.hiddenInput.value = this.tags.join(",");
        }

        // 🔹 Suggestions
        async fetchSuggestions(query) {
            if (!this.autocompleteUrl || query.length < 2) {
                this.clearSuggestions();
                return;
            }

            try {

                const data = await ajax.post(this.autocompleteUrl, {
                    term: query,
					time: Date.now()
                });

                this.suggestionsBox.innerHTML = "";

                data.forEach(item => {
                    if (this.tags.includes(item.value)) return;

                    const div = document.createElement("div");
                    div.className = "suggestion";
                    div.textContent = item.label;

                    div.onclick = () => this.addTag(item.value);

                    this.suggestionsBox.appendChild(div);
                });

            } catch (e) {console.error("Error:", e);}

        }

        debounceFetch(query) {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.fetchSuggestions(query), 250);
        }

        clearSuggestions() {
            this.suggestionsBox.innerHTML = "";
        }

        // 🔹 Events
        bindEvents() {
            this.input.addEventListener("keydown", e => {
                if (e.key === "Enter" || e.key === ",") {
                    e.preventDefault();
                    this.addTag(this.input.value.replace(",", ""));
                }

                if (e.key === "Backspace" && this.input.value === "" && this.tags.length) {
                    this.removeTag(this.tags[this.tags.length - 1]);
                }
            });

            this.input.addEventListener("keyup", () => {
                this.debounceFetch(this.input.value.trim());
            });

            this.container.addEventListener("click", () => this.input.focus());

            document.addEventListener("click", e => {
                if (!this.container.contains(e.target)) {
                    this.clearSuggestions();
                }
            });
        }

        // 🔹 Initialisation
        loadInitialTags() {
            if (!this.hiddenInput.value) return;

            this.hiddenInput.value.split(",").forEach(tag => {
                tag = tag.trim();
                if (tag && !this.tags.includes(tag)) {
                    this.tags.push(tag);
                }
            });
        }

        // 🔹 API publique (optionnel)
        getTags() {
            return this.tags;
        }

        setTags(tagsArray) {
            this.tags = [...new Set(tagsArray.map(t => t.trim()))];
            this.renderTags();
        }

        clear() {
            this.tags = [];
            this.renderTags();
        }
    }