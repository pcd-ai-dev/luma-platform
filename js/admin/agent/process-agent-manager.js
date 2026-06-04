/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
    // PROCESS ASSISTANT WINDOW
    //---------------------------------------------------------

        const btn = document.getElementById('ai-btn');
        const chat = document.getElementById('ai-chat');
        const closeBtn = document.getElementById('ai-close');

        const input = document.getElementById('ai-input');
        const sendBtn = document.getElementById('ai-send');
        const messages = document.getElementById('ai-messages');

        btn.onclick = () => {
            chat.style.display = chat.style.display === 'flex' ? 'none' : 'flex';
        };

        closeBtn.onclick = () => {
            chat.style.display = 'none';
        };

        // Boutons de IASelect
        document.querySelectorAll('.ia-select-btn').forEach(btn => {
            btn.addEventListener('click', () => {
            document.querySelectorAll('.ia-select-btn').forEach(b => b.classList.remove('active'));
              btn.classList.add('active');
            });
        });

        // envoyer message
        async function sendMessage() {

            const text = input.value.trim();
            if (!text) return;

            addMessage("user", text);
            input.value = "";

            const providerSelect = document.querySelector('.ia-select-btn.active')?.dataset.provider || '1';

            const formDataAgent = new FormData();
            formDataAgent.append('message', text);
            formDataAgent.append('provider', providerSelect);

            const data = await ajax.post('/includes/ajax/agent/process-agent.php', formDataAgent);

            addMessage("bot", data.reply);
        }

        sendBtn.onclick = sendMessage;

        input.addEventListener("keypress", (e) => {
            if (e.key === "Enter") sendMessage();
        });

        function addMessage(role, text) {
            const div = document.createElement('div');
            div.style.margin = "5px 0";
            div.style.padding = "8px";
            div.style.borderRadius = "8px";
            div.style.background = role === "user" ? "#eee" : "#111";
            div.style.color = role === "user" ? "#000" : "#fff";
            div.innerText = text;
            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;
        }

        const header = document.getElementById("ai-header");

        let isDragging = false;
        let offsetX, offsetY;

        header.addEventListener("mousedown", (e) => {
            isDragging = true;
            offsetX = e.clientX - chat.offsetLeft;
            offsetY = e.clientY - chat.offsetTop;
        });

        document.addEventListener("mousemove", (e) => {
            if (!isDragging) return;

            chat.style.left = (e.clientX - offsetX) + "px";
            chat.style.top = (e.clientY - offsetY) + "px";
            chat.style.bottom = "auto";
            chat.style.right = "auto";
        });

        document.addEventListener("mouseup", () => {
            isDragging = false;
        });