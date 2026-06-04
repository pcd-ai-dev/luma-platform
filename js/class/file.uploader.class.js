/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


//---------------------------------------------------------
// FILE UPLOADER CLASS
//---------------------------------------------------------

    class FileUploader {
        constructor(options) {
            this.UId = options.UId;

            // DOM
            this.uploadForm = document.getElementById('uploadform_' + this.UId);
            this.addFileBtn = document.getElementById('addFileBtn_' + this.UId);
            this.startUploadBtn = document.getElementById('startUpload_' + this.UId);
            this.dropZone = document.getElementById('dropZone_' + this.UId);
            this.filePreview = document.getElementById('filePreview_' + this.UId);
            this.progressWrapper = document.getElementById('progressWrapper_' + this.UId);
            this.progressBar = document.getElementById('progressBar_' + this.UId);
            this.progressPercent = document.getElementById('progressPercent_' + this.UId);

            // Config
            this.uploadUrl = options.uploadUrl;
            this.accept = options.accept || '.jpg,.jpeg,.png,.gif,.zip';

            // Callback
            this.refreshCallback = options.refreshCallback; // ex: showFolder

            // FormData params dynamiques
            this.formDataParams = options.formDataParams || {};

            this.filesToUpload = [];

            this.init();
        }

        init() {
            this.addFileBtn.addEventListener('click', () => this.openFilePicker());
            this.dropZone.addEventListener('dragover', (e) => this.onDragOver(e));
            this.dropZone.addEventListener('dragleave', (e) => this.onDragLeave(e));
            this.dropZone.addEventListener('drop', (e) => this.onDrop(e));
            this.startUploadBtn.addEventListener('click', () => this.uploadFiles());
        }

        // --- File picker ---
        openFilePicker() {
            const input = document.createElement('input');
            input.type = 'file';
            input.multiple = true;
            input.accept = this.accept;

            input.onchange = (e) => this.handleFiles(e.target.files);
            input.click();
        }

        // --- Files handling ---
        handleFiles(files) {
            for (const file of files) {
                if (!/\.(jpe?g|png|gif|zip)$/i.test(file.name)) continue;

                if (this.filesToUpload.some(f => f.name === file.name && f.size === file.size)) continue;

                this.filesToUpload.push(file);

                const container = this.createPreview(file);
                this.filePreview.appendChild(container);
            }

            if (this.filesToUpload.length) {
                this.startUploadBtn.disabled = false;
            }
        }

        createPreview(file) {
            const container = document.createElement('div');
            container.style.position = 'relative';
            container.style.width = '100px';
            container.style.height = '100px';
            container.style.border = '1px solid #ccc';
            container.style.overflow = 'hidden';

            if (/image/i.test(file.type)) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                container.textContent = file.name;
                container.style.fontSize = '12px';
                container.style.display = 'flex';
                container.style.alignItems = 'center';
                container.style.justifyContent = 'center';
                container.style.textAlign = 'center';
            }

            const removeBtn = document.createElement('span');
            removeBtn.textContent = '✖';
            removeBtn.style.position = 'absolute';
            removeBtn.style.top = '2px';
            removeBtn.style.right = '2px';
            removeBtn.style.cursor = 'pointer';
            removeBtn.style.background = 'rgba(0,0,0,0.5)';
            removeBtn.style.color = 'white';
            removeBtn.style.borderRadius = '50%';
            removeBtn.style.padding = '2px 5px';

            removeBtn.onclick = () => {
                container.remove();
                this.filesToUpload = this.filesToUpload.filter(f => f !== file);

                if (!this.filesToUpload.length) {
                    this.startUploadBtn.disabled = true;
                }
            };

            container.appendChild(removeBtn);

            return container;
        }

        // --- Drag & Drop ---
        onDragOver(e) {
            e.preventDefault();
            this.dropZone.style.background = '#f0f0f0';
        }

        onDragLeave(e) {
            e.preventDefault();
            this.dropZone.style.background = '';
        }

        onDrop(e) {
            e.preventDefault();
            this.dropZone.style.background = '';
            this.handleFiles(e.dataTransfer.files);
        }

        // --- Upload ---
        async uploadFiles() {
            if (!this.filesToUpload.length) {
                alert('Veuillez sélectionner au moins un fichier.');
                return;
            }

            this.progressWrapper.style.display = 'block';

            let uploaded = 0;

            for (const file of this.filesToUpload) {
                const formData = new FormData();

                // fichier
                formData.append('file', file);

                // paramètres dynamiques
                for (const key in this.formDataParams) {
                    formData.append(key, this.formDataParams[key]);
                }

                try {
                    const res = await fetch(this.uploadUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-Token': csrfToken },
                        body: formData
                    });

                    if (!res.ok) throw new Error('Upload failed: ' + file.name);

                    uploaded++;

                    const percent = Math.round((uploaded / this.filesToUpload.length) * 100);
                    this.progressBar.style.width = percent + '%';
                    this.progressPercent.textContent = percent + '%';

                } catch (err) {
                    console.error(err);
                    alert('Erreur upload fichier: ' + file.name);
                }
            }

            // message global
            if (typeof showWarningMessage === 'function') {
                showWarningMessage("builderMsgUpdate", "Les photos ont bien été chargées.", "#81B929");
            }

            this.reset();

            // callback dynamique
            if (typeof this.refreshCallback === 'function') {
                this.refreshCallback(this.formDataParams.modId);
            }
        }

        reset() {
            this.filesToUpload = [];
            this.filePreview.innerHTML = '';
            this.startUploadBtn.disabled = true;

            this.progressWrapper.style.display = 'none';
            this.progressBar.style.width = '0%';
            this.progressPercent.textContent = '0%';
        }
    }