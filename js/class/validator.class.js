/*
* @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright 2003-2026 Lumaprod
*/

//---------------------------------------------------------
// VALIDATOR CLASS
//---------------------------------------------------------

    class Validator {

        constructor(form, options = {}) {

            //-------------------------------------------------
            // FORM
            //-------------------------------------------------

            this.form = typeof form === "string"
                ? document.getElementById(form)
                : form;

            if (!this.form) {
                //console.warn("Validator: formulaire introuvable");
                return;
            }

            //-------------------------------------------------
            // OPTIONS
            //-------------------------------------------------

            this.rules = options.rules || {};
            this.messages = options.messages || {};

            // dépendances dynamiques
            this.dependencies = options.dependencies || {};

            this.onSuccess = options.onSuccess || null;

            this.allowSubmit = options.allowSubmit || false;

            this.showErrors = options.showErrors !== false;
            this.showErrorStyles = options.showErrorStyles !== false;
            this.showErrorMessages = options.showErrorMessages !== false;

            //-------------------------------------------------
            // INTERNALS
            //-------------------------------------------------

            this.remoteCache = {};
            this.debounceTimers = {};

            //-------------------------------------------------
            // INIT
            //-------------------------------------------------

            this._init();
        }

        //---------------------------------------------------------
        // INIT
        //---------------------------------------------------------

        _init() {

            //-------------------------------------------------
            // SUBMIT
            //-------------------------------------------------

            this.form.addEventListener("submit", async (e) => {

                if (!this.allowSubmit) {
                    e.preventDefault();
                }

                const valid = await this.validateForm();

                if (!valid) {

                    e.preventDefault();

                    //-------------------------------------------------
                    // SCROLL TO FIRST ERROR
                    //-------------------------------------------------

                    this._scrollToFirstError();

                    return;
                }

                if (
                    !this.allowSubmit &&
                    typeof this.onSuccess === "function"
                ) {
                    this.onSuccess(this.form);
                }

            });

            //-------------------------------------------------
            // LIVE VALIDATION
            //-------------------------------------------------

            Object.keys(this.rules).forEach((key) => {

                //-------------------------------------------------
                // FIELD BY ID
                //-------------------------------------------------

                let fields = [];

                const fieldById = this.form.querySelector("#" + key);

                if (fieldById) {

                    fields.push(fieldById);

                } else {

                    //-------------------------------------------------
                    // FIELD BY NAME
                    //-------------------------------------------------

                    fields = Array.from(
                        this.form.querySelectorAll(`[name="${key}"]`)
                    );
                }

                if (!fields.length) return;

                //-------------------------------------------------
                // EVENTS
                //-------------------------------------------------

                fields.forEach((field) => {

                    const eventType = (
                        field.type === "checkbox" ||
                        field.type === "radio" ||
                        field.type === "file"
                    )
                        ? "change"
                        : "input";

                    field.addEventListener(eventType, () => {

                        clearTimeout(this.debounceTimers[field.name || field.id]);

                        this.debounceTimers[field.name || field.id] = setTimeout(() => {

                            //-------------------------------------------------
                            // RADIO GROUP
                            //-------------------------------------------------

                            if (field.type === "radio") {

                                this._validateRadioGroup(field.name);

                            } else {

                                //-------------------------------------------------
                                // STANDARD FIELD
                                //-------------------------------------------------

                                this.validateField(field);
                            }

                            //-------------------------------------------------
                            // DEPENDENCIES
                            //-------------------------------------------------

                            this._triggerDependencies(field);

                        }, 100);

                    });

                });

            });

        }

        //---------------------------------------------------------
        // VALIDATE FORM
        //---------------------------------------------------------

        async validateForm() {

            let valid = true;

            const validatedRadioGroups = [];

            for (const key of Object.keys(this.rules)) {

                //-------------------------------------------------
                // FIELD
                //-------------------------------------------------

                let field = this.form.querySelector("#" + key);

                //-------------------------------------------------
                // FIELD BY NAME
                //-------------------------------------------------

                if (!field) {

                    field = this.form.querySelector(
                        `[name="${key}"]`
                    );
                }

                if (!field) continue;

                //-------------------------------------------------
                // RADIO GROUP
                //-------------------------------------------------

                if (field.type === "radio") {

                    if (validatedRadioGroups.includes(field.name)) {
                        continue;
                    }

                    validatedRadioGroups.push(field.name);

                    const radioValid = this._validateRadioGroup(field.name);

                    if (!radioValid) {
                        valid = false;
                    }

                    continue;
                }

                //-------------------------------------------------
                // STANDARD FIELD
                //-------------------------------------------------

                const result = await this.validateField(field);

                if (!result) {
                    valid = false;
                }

            }

            return valid;
        }

        //---------------------------------------------------------
        // VALIDATE FIELD
        //---------------------------------------------------------

        async validateField(field) {

            if (!field) return false;

            //-------------------------------------------------
            // RULES
            //-------------------------------------------------

            const rules =
                this.rules[field.id] ||
                this.rules[field.name] ||
                {};

            //-------------------------------------------------
            // VALUE
            //-------------------------------------------------

            const value = this._getValue(field);

            let error = "";

            const required = this._isRequired(field, rules);

            //-------------------------------------------------
            // REQUIRED
            //-------------------------------------------------

            if (required) {

                //-------------------------------------------------
                // CHECKBOX
                //-------------------------------------------------

                if (field.type === "checkbox" && !field.checked) {

                    error = this._msg(
                        field,
                        "required",
                        "Champ requis"
                    );
                }

                //-------------------------------------------------
                // STANDARD
                //-------------------------------------------------

                else if (
                    field.type !== "checkbox" &&
                    !value
                ) {

                    error = this._msg(
                        field,
                        "required",
                        "Champ requis"
                    );
                }
            }

            //-------------------------------------------------
            // EMAIL
            //-------------------------------------------------

            if (
                !error &&
                rules.email &&
                value &&
                !this._validEmail(value)
            ) {

                error = this._msg(
                    field,
                    "email",
                    "Email invalide"
                );
            }

            //-------------------------------------------------
            // MIN LENGTH
            //-------------------------------------------------

            if (
                !error &&
                rules.minlength &&
                typeof value === "string" &&
                value.length < rules.minlength
            ) {

                error = this._msg(
                    field,
                    "minlength",
                    "Minimum " + rules.minlength + " caractères"
                );
            }

            //-------------------------------------------------
            // EQUAL TO
            //-------------------------------------------------

            if (!error && rules.equalTo) {

                const other = this.form.querySelector(
                    "#" + rules.equalTo
                );

                if (
                    other &&
                    value !== this._getValue(other)
                ) {

                    error = this._msg(
                        field,
                        "equalTo",
                        "Les champs doivent être identiques"
                    );
                }
            }

            //-------------------------------------------------
            // NUMBER
            //-------------------------------------------------

            if (
                !error &&
                rules.number &&
                value &&
                isNaN(value)
            ) {

                error = this._msg(
                    field,
                    "number",
                    "Nombre invalide"
                );
            }

            //-------------------------------------------------
            // MIN
            //-------------------------------------------------

            if (
                !error &&
                rules.min &&
                value &&
                parseFloat(value) < rules.min
            ) {

                error = this._msg(
                    field,
                    "min",
                    "Valeur trop petite"
                );
            }

            //-------------------------------------------------
            // MAX
            //-------------------------------------------------

            if (
                !error &&
                rules.max &&
                value &&
                parseFloat(value) > rules.max
            ) {

                error = this._msg(
                    field,
                    "max",
                    "Valeur trop grande"
                );
            }

            //-------------------------------------------------
            // FILE SIZE
            //-------------------------------------------------

            if (
                !error &&
                rules.fileSize &&
                field.files?.[0]
            ) {

                if (field.files[0].size > rules.fileSize) {

                    error = this._msg(
                        field,
                        "fileSize",
                        "Fichier trop volumineux"
                    );
                }
            }

            //-------------------------------------------------
            // FILE TYPE
            //-------------------------------------------------

            if (
                !error &&
                rules.fileType &&
                field.files?.[0]
            ) {

                if (
                    !rules.fileType.includes(
                        field.files[0].type
                    )
                ) {

                    error = this._msg(
                        field,
                        "fileType",
                        "Type de fichier invalide"
                    );
                }
            }

            //-------------------------------------------------
            // PASSWORD STRENGTH
            //-------------------------------------------------

            if (
                !error &&
                rules.passwordStrength &&
                value
            ) {

                const strength = this._passwordStrength(value);

                this._displayStrength(field, strength);
            }

            //-------------------------------------------------
            // REMOTE
            //-------------------------------------------------

            if (
                !error &&
                rules.remote &&
                value
            ) {

                const valid = await this._remoteCheck(
                    field,
                    rules.remote
                );

                if (!valid) {

                    error = this._msg(
                        field,
                        "remote",
                        "Valeur déjà utilisée"
                    );
                }
            }

            //-------------------------------------------------
            // DISPLAY ERROR
            //-------------------------------------------------

            this._displayError(field, error);

            return !error;
        }

        //---------------------------------------------------------
        // SCROLL TO FIRST ERROR
        //---------------------------------------------------------

        _scrollToFirstError() {

            const firstError = this.form.querySelector(
                ".error"
            );

            if (!firstError) {
                return;
            }

            //-------------------------------------------------
            // POSITION
            //-------------------------------------------------

            const offset = 120;

            const top =
                firstError.getBoundingClientRect().top +
                window.pageYOffset -
                offset;

            //-------------------------------------------------
            // SMOOTH SCROLL
            //-------------------------------------------------

            window.scrollTo({
                top,
                behavior: "smooth"
            });

            //-------------------------------------------------
            // FOCUS
            //-------------------------------------------------

            setTimeout(() => {

                firstError.focus?.({
                    preventScroll: true
                });

                //-------------------------------------------------
                // SHAKE EFFECT
                //-------------------------------------------------

                firstError.classList.add(
                    "error-focus"
                );

                setTimeout(() => {

                    firstError.classList.remove(
                        "error-focus"
                    );

                }, 700);

            }, 500);
        }

        //---------------------------------------------------------
        // RADIO GROUP VALIDATION
        //---------------------------------------------------------

        _validateRadioGroup(name) {

            const radios = this._getRadioGroup(name);

            if (!radios.length) return true;

            const first = radios[0];

            //-------------------------------------------------
            // RULES
            //-------------------------------------------------

            const rules =
                this.rules[first.name] ||
                this.rules[first.id] ||
                {};

            const required = this._isRequired(first, rules);

            const checked = radios.some(r => r.checked);

            let error = "";

            if (required && !checked) {

                error = this._msg(
                    first,
                    "required",
                    "Champ requis"
                );
            }

            this._displayRadioGroupError(
                radios,
                error,
                first
            );

            return !error;
        }

        //---------------------------------------------------------
        // DEPENDENCIES
        //---------------------------------------------------------

        _triggerDependencies(field) {

            const deps =
                this.dependencies[field.name] ||
                this.dependencies[field.id];

            if (!deps) return;

            deps.forEach((targetId) => {

                const target = this.form.querySelector(
                    "#" + targetId
                );

                if (target) {
                    this.validateField(target);
                }

            });
        }

        //---------------------------------------------------------
        // GET RADIO GROUP
        //---------------------------------------------------------

        _getRadioGroup(name) {

            return Array.from(

                this.form.querySelectorAll(
                    `input[type="radio"][name="${name}"]`
                )

            );
        }

        //---------------------------------------------------------
        // DISPLAY RADIO GROUP ERROR
        //---------------------------------------------------------

        _displayRadioGroupError(radios, error, refField) {

            const groupId = `group_error_${refField.name}`;

            let el = this.form.querySelector("#" + groupId);

            //-------------------------------------------------
            // REMOVE ERROR
            //-------------------------------------------------

            if (!error) {

                if (el) {
                    el.remove();
                }

                radios.forEach((radio) => {

                    if (this.showErrorStyles) {
                        radio.classList.remove("error");
                    }

                });

                return;
            }

            //-------------------------------------------------
            // ERROR STYLE
            //-------------------------------------------------

            radios.forEach((radio) => {

                if (this.showErrorStyles) {
                    radio.classList.add("error");
                }

            });

            //-------------------------------------------------
            // DISPLAY MESSAGE
            //-------------------------------------------------

            if (
                !this.showErrors ||
                !this.showErrorMessages
            ) {
                return;
            }

            //-------------------------------------------------
            // CREATE MESSAGE
            //-------------------------------------------------

            if (!el) {

                el = document.createElement("div");

                el.id = groupId;
                el.className = "error-message radio-group-error";

                const lastRadio = radios[radios.length - 1];

                const container =
                    lastRadio.closest("label") || lastRadio;

                container.after(el);
            }

            el.textContent = error;
        }

        //---------------------------------------------------------
        // GET VALUE
        //---------------------------------------------------------

        _getValue(field) {

            if (!field) return "";

            //-------------------------------------------------
            // CHECKBOX
            //-------------------------------------------------

            if (field.type === "checkbox") {
                return field.checked;
            }

            //-------------------------------------------------
            // RADIO
            //-------------------------------------------------

            if (field.type === "radio") {

                const checked = this.form.querySelector(
                    `input[name="${field.name}"]:checked`
                );

                return checked
                    ? checked.value
                    : "";
            }

            //-------------------------------------------------
            // STANDARD
            //-------------------------------------------------

            return field.value?.trim?.() || "";
        }

        //---------------------------------------------------------
        // REQUIRED
        //---------------------------------------------------------

        _isRequired(field, rules) {

            if (!rules.required) {
                return false;
            }

            //-------------------------------------------------
            // FUNCTION
            //-------------------------------------------------

            if (typeof rules.required === "function") {
                return rules.required(field, this.form);
            }

            return true;
        }

        //---------------------------------------------------------
        // DISPLAY ERROR
        //---------------------------------------------------------

        _displayError(field, error) {

            //-------------------------------------------------
            // STYLE
            //-------------------------------------------------

            if (this.showErrorStyles) {

                field.classList.toggle(
                    "error",
                    !!error
                );
            }

            //-------------------------------------------------
            // NO DISPLAY
            //-------------------------------------------------

            if (
                !this.showErrors ||
                !this.showErrorMessages
            ) {
                return;
            }

            //-------------------------------------------------
            // FIND ERROR ELEMENT
            //-------------------------------------------------

            let el = this.form.querySelector(
                "#" + field.id + "_error"
            );

            //-------------------------------------------------
            // REMOVE ERROR
            //-------------------------------------------------

            if (!error) {

                if (el) {
                    el.remove();
                }

                return;
            }

            //-------------------------------------------------
            // CREATE
            //-------------------------------------------------

            if (!el) {

                el = document.createElement("div");

                el.className = "error-message";
                el.id = field.id + "_error";

                field.after(el);
            }

            //-------------------------------------------------
            // MESSAGE
            //-------------------------------------------------

            el.textContent = error;
        }

        //---------------------------------------------------------
        // MESSAGE
        //---------------------------------------------------------

        _msg(field, rule, def) {

            const msg =
                this.messages[field.id] ||
                this.messages[field.name];

            //-------------------------------------------------
            // STRING
            //-------------------------------------------------

            if (typeof msg === "string") {
                return msg;
            }

            //-------------------------------------------------
            // OBJECT
            //-------------------------------------------------

            if (
                typeof msg === "object" &&
                msg?.[rule]
            ) {

                return msg[rule];
            }

            return def;
        }

        //---------------------------------------------------------
        // EMAIL VALIDATION
        //---------------------------------------------------------

        _validEmail(value) {

            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }

        //---------------------------------------------------------
        // PASSWORD STRENGTH
        //---------------------------------------------------------

        _passwordStrength(password) {

            let s = 0;

            if (password.length >= 6) s++;
            if (/[A-Z]/.test(password)) s++;
            if (/[a-z]/.test(password)) s++;
            if (/[0-9]/.test(password)) s++;
            if (/[!@#$%^&*]/.test(password)) s++;

            if (s <= 2) return "faible";
            if (s <= 3) return "moyenne";

            return "forte";
        }

        //---------------------------------------------------------
        // DISPLAY PASSWORD STRENGTH
        //---------------------------------------------------------

        _displayStrength(field, strength) {

            let el = this.form.querySelector(
                "#" + field.id + "_strength"
            );

            if (!el) {

                el = document.createElement("div");

                el.id = field.id + "_strength";
                el.className = "password-strength";

                field.after(el);
            }

            el.textContent =
                "Complexité : " + strength;

            el.className =
                "password-strength " + strength;
        }

        //---------------------------------------------------------
        // REMOTE CHECK
        //---------------------------------------------------------

        async _remoteCheck(field, options) {

            const value = this._getValue(field);

            const key = field.id + ":" + value;

            //-------------------------------------------------
            // CACHE
            //-------------------------------------------------

            if (
                this.remoteCache[key] !== undefined
            ) {

                return this.remoteCache[key];
            }

            //-------------------------------------------------
            // PAYLOAD
            //-------------------------------------------------

            const payload = {
                [field.id]: value
            };

            //-------------------------------------------------
            // EXTRA DATA
            //-------------------------------------------------

            if (options.data) {

                for (let k in options.data) {

                    let v = options.data[k];

                    if (typeof v === "function") {
                        v = v();
                    }

                    payload[k] = v;
                }
            }

            //-------------------------------------------------
            // AJAX
            //-------------------------------------------------

            const json = await ajax.post(
                options.url,
                payload
            );

            const valid = (
                json.status === true ||
                json.status === 1
            );

            //-------------------------------------------------
            // CACHE
            //-------------------------------------------------

            this.remoteCache[key] = valid;

            return valid;
        }

        //---------------------------------------------------------
        // PUBLIC API
        //---------------------------------------------------------

        validate() {
            return this.validateForm();
        }

    }