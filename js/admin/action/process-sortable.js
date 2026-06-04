/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/

    //---------------------------------------------------------
    // SORTABLE PROCESS
    //---------------------------------------------------------

        function initSortable(selector, table, field, target, type, maxDepth = 3) {
            const roots = document.querySelectorAll(selector);
            if (!roots.length) return;

            roots.forEach(root => {
                // empêcher double initialisation
                if (root.dataset.sortableInit === "1") {
                    const clone = root.cloneNode(true);
                    root.parentNode.replaceChild(clone, root);
                    root = clone;
                }
                root.dataset.sortableInit = "1";

                let dragged = null;
                let startX = 0;

                const placeholder = document.createElement("li");
                placeholder.className = (type === "list" || type === "listRoot") ? "placeHolderList" : "placeHolderImg";

                // marquer les branches existantes
                root.querySelectorAll("li").forEach(li => {
                    if (li.querySelector("ol")) {
                        li.classList.add("branch", "collapsed");
                    }
                });

                refreshDraggable();

                root.addEventListener("dragstart", onDragStart);
                root.addEventListener("dragover", onDragOver);
                root.addEventListener("drop", onDrop);
                root.addEventListener("dragend", onDragEnd);

                // -------------------------
                // FUNCTIONS
                // -------------------------
                function refreshDraggable() {
                    root.querySelectorAll("li[data-id]").forEach(li => li.draggable = true);
                }

        function onDragStart(e) {
            const li = e.target.closest("li[data-id]");
            if (!li) return;

            // 🔴 CRITIQUE : s'assurer que c'est un enfant DIRECT du root
            const parentSortable = li.parentElement.closest("ol.sortable");
            if (parentSortable !== root) return;

            e.stopPropagation();

            dragged = li;
            startX = e.clientX;
            li.classList.add("dragging");
            placeholder.style.height = li.offsetHeight + "px";

            li.querySelectorAll("a").forEach(a => a.style.pointerEvents = "none");

            e.dataTransfer.effectAllowed = "move";
        }

        function onDragOver(e) {
            e.preventDefault(); // 🔴 TOUJOURS EN PREMIER
            e.stopPropagation();

            if (!dragged) return;

            const li = e.target.closest("li[data-id]");
            if (!li) return;

            if (!root.contains(li)) return;

            console.log("OVER", li.dataset.id);

            if (li === dragged || dragged.contains(li)) return;

            const rect = li.getBoundingClientRect();
            const offsetY = e.clientY - rect.top;

            if (offsetY > rect.height / 2) {
                li.after(placeholder);
            } else {
                li.before(placeholder);
            }

            handleNesting(e.clientX, li);
        }

        function handleNesting(mouseX, targetLi) {
            if (!dragged || !targetLi) return; // 🔴 FIX

            if (dragged.contains(targetLi)) return;

            const deltaX = mouseX - startX;

            // IMBRIQUER
            if (deltaX > 30) {
                const newDepth = getDepth(targetLi) + 1;
                const draggedDepth = getSubtreeDepth(dragged);

                if (newDepth + draggedDepth > maxDepth) {
                    showDepthLimit(targetLi);
                    return;
                }

                let sub = targetLi.querySelector(":scope > ol");
                if(!sub){
                    sub = document.createElement("ol");
                    targetLi.appendChild(sub);
                }
                sub.appendChild(placeholder);
                targetLi.classList.add("expanded");
                targetLi.classList.remove("collapsed");
            }

            // REMONTER NIVEAU
            if (deltaX < -30) {
                const parentLi = targetLi.parentElement.closest("li[data-id]");
                if (parentLi) parentLi.after(placeholder);
            }
        }

        function onDrop(e) {
            if (!dragged) return;

            e.stopPropagation();
            e.preventDefault();

            if (!placeholder.parentElement) return;

            placeholder.replaceWith(dragged);
            refreshDraggable();
        }

        function onDragEnd() {
            if (!dragged) return;

            dragged.classList.remove("dragging");
            placeholder.remove();

            dragged.querySelectorAll("a").forEach(a => a.style.pointerEvents = "");

            sendOrder();
            dragged = null;
        }

        function getDepth(li) {
            let depth = 0;
            while (li.parentElement.closest("li[data-id]")) {
                depth++;
                li = li.parentElement.closest("li[data-id]");
            }
            return depth;
        }

        function getSubtreeDepth(li) {
            let max = 0;
            li.querySelectorAll("li[data-id]").forEach(child => {
                const depth = getDepth(child) - getDepth(li);
                if (depth > max) max = depth;
            });
             return max;
        }

        function showDepthLimit(li) {
            li.classList.add("depth-limit");
            clearTimeout(li._depthTimer);
            li._depthTimer = setTimeout(() => {
                li.classList.remove("depth-limit");
            }, 600);
        }

        // -------------------------
        // COLLAPSE / EXPAND
        // -------------------------
        root.querySelectorAll(".disclose").forEach(btn => {
            btn.addEventListener("click", e => {
                e.stopPropagation(); // empêcher le dragstart
                const li = btn.closest("li[data-id]");
                if (!li) return;
                li.classList.toggle("collapsed");
                li.classList.toggle("expanded");
            });
        });

        // -------------------------
        // AUTO SCROLL
        // -------------------------

            if (!window._sortableAutoScroll) {
                window._sortableAutoScroll = true;
                document.addEventListener("dragover", e => {
                    const margin = 60;
                    if (e.clientY < margin) window.scrollBy(0, -12);
                    if (window.innerHeight - e.clientY < margin) window.scrollBy(0, 12);
                });
            }

        // -------------------------
        // SERIALIZE / SEND
        // -------------------------

            function serialize() {
                const data = [];
                root.querySelectorAll("li[data-id]").forEach(li => {
                    data.push({
                        id: li.dataset.id,
                        parent: li.parentElement.closest("li[data-id]")? li.parentElement.closest("li[data-id]").dataset.id : 0
                    });
                });
                return data;
            }

            function sendOrder() {
                const data = serialize();
                const params = new URLSearchParams();
                data.forEach(item => {
                    params.append(`list[${item.id}]`, item.parent || 1);
                });
                params.append("table", table);
                params.append("field", field);
                params.append("target", target);
                params.append("type", type);
                // params.append("csrf", csrfToken);

                const url = ajaxPath + "action/process-sortable.php";
                fetch(url, {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded", "X-CSRF-Token": csrfToken },
                    body: params
                })
                .then(r => r.json())
                .then(res => console.log("Sortable saved", res))
                .catch(err => console.error("Sortable fetch error:", err));
            }

        });
    }