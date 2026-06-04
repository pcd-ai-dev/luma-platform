/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/

    //---------------------------------------------------------
    // IMAGE ADD PROCESS
    //---------------------------------------------------------

        async function initFileUploader(config){

            const root = document.getElementById(config.uid);
            if (!root) return;

            const browseBtn = document.getElementById("browse_" + config.uid);
            const resetBtn = root.querySelector(".resetImageBtn_" + config.uid);
            const progress = root.querySelector(".progress");
            const percent = root.querySelector(".percent");
            let img = document.getElementById("img_update_" + config.uid);
            //root.querySelector("img");

            browseBtn.addEventListener("click", () => {
                const input = document.getElementById("fileInput_" + config.uid);
                input.type = "file";
                //input.accept = ".jpg,.jpeg,.png,.gif,.svg,.heic";

                input.onchange = e => handleFile(e.target.files[0]);
                input.click();
            });

            function handleFile(file){
                if (!file) return;

                if (file.size > 10 * 1024 * 1024){
                    alert("Max 10MB");
                    return;
                }

                upload(file);
            }

            function upload(file){
                const fd = new FormData();
                fd.append("file", file);

                Object.entries(config).forEach(([k,v])=>{
                    if(k !== "uid") fd.append(k,v);
                });

                const xhr = new XMLHttpRequest();
                xhr.open("POST", ajaxPath + "action/process-file-full-upload.php");
                xhr.setRequestHeader("X-CSRF-Token", document.querySelector('meta[name="csrf-token"]').content, "X-Requested-With", "XMLHttpRequest" );

                progress.style.display = "block";

                xhr.upload.onprogress = e=>{
                    if(e.lengthComputable){
                        percent.textContent = Math.round(e.loaded/e.total*100)+"%";
                    }
                };

                xhr.onload = ()=>{
                    const data = JSON.parse(xhr.responseText);
                    if(data.output=='img'){
                        if(config.field==="rec"){
                            img.src = data.rec + "?" + Math.random();
                        } else if(config.field==="imgMobile"){
                            img.src = data.mobile + "?" + Math.random();
                        }else{
                            img.src = data.square + "?" + Math.random();
                        }
                    }else{
                        showDocFolder(data.idParent);
                        showWarningMessage("builderMsgUpdate", "Fichier téléversé avec succès", "#81B929");
                    }
                    resetBtn.style.visibility = "visible";
                    progress.style.display = "none";
                };

                xhr.send(fd);
            }


            resetBtn.addEventListener("click", async () => {

                try {

                    const data = await ajax.post(ajaxPath + 'action/process-file-delete.php', {
                        entity: config.entity,
                        id: config.id,
                        field:config.field,
                        target: config.target,
                        idParent:config.idParent,
                        folder: config.folder,
                        idSite: config.idSite,
                    });

                    if (data.error) {
                        alert(data.message);
                        return;
                    }

                    if (img) {
                        img.src = config.defaultImg + "?" + Math.random();
                        resetBtn.style.visibility = "hidden";
                    }

                } catch (e) {console.error("Error:", e);}

            });

        }