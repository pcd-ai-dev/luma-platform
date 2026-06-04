/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
   	// STATUS ACTION
   	//---------------------------------------------------------

        document.addEventListener('click', async function(event) {
            const target = event.target.closest('[id^="checkStatus"]');
            if (!target) return;

            event.preventDefault();

            const id = target.dataset.id;
            const table = target.dataset.table;
            const field = target.dataset.field || 'status';
            const targetId = target.dataset.targetId || 'id';
            const output = target.dataset.output || 'green';

            if (!id || !table) return;

            try {

                const data = await ajax.post(ajaxPath + 'action/process-status.php', {
                    id: id,
                    table: table,
                    field: field,
                    target: targetId,
                    output: output
                });

                if (data.status !== undefined) {
                    if(data.output==='red'){
                        const img = target.querySelector('img');
                        if(img){img.src = data.outputRender;}
                    } else if(data.output==='light'){
                        const span = target.querySelector('span');
                        if(span){span.innerHTML='<span '+data.outputRender+' />&#9728;</span>';}
                    } else if(data.output==='pen'){
                        const span = target.querySelector('span');
                        if(span){span.innerHTML='<span '+data.outputRender+' />&#x2710;</span>';}
                    } else {
                        const img = target.querySelector('img');
                        if(img){img.src = data.outputRender;}
                    }
                }

            } catch (e) {console.error("Error:", e);}

        });