/*
* @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 | Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
    // MENU ADMIN
    //---------------------------------------------------------


document.addEventListener("DOMContentLoaded", () => {

    //---------------------------------------------------------
    // CONFIG
    //---------------------------------------------------------

        const blockGauche = document.querySelector('.block_gauche');
        const blockMenu = document.querySelector('#menuRight');
        const leftHeader = document.querySelector('.leftHeader');
        const titleAdminHeader = document.querySelector('.titleAdminHeader');
        const centerHeader = document.querySelector('#adminheader .centerHeader');


    //---------------------------------------------------------
    // ITEM ICON
    //---------------------------------------------------------

        document.querySelectorAll('.itemSeparationIcon, .itemSeparationIconExpanded').forEach(icon => {
            icon.addEventListener('click', async function () {

                const idlauch = this.id;
                const menuItem = document.querySelector('.menuItem_' + idlauch);
                const menuArea = document.querySelector('.menuArea_' + idlauch);

                this.classList.toggle('itemSeparationIconExpanded');
                this.classList.toggle('itemSeparationIcon');

                if (menuItem.style.display === "block") {
                    menuItem.style.display = "none";
                    menuArea.style.display = "none";
                } else {
                    menuItem.style.display = "block";
                    if (menuItem.classList.contains("itemSeparationExpanded")) {
                        menuArea.style.display = "block";
                    } else {
                        menuArea.style.display = "none";
                    }
                }

                try {
                    const data = await ajax.post("/includes/ajax/admin/process-menu-item.php", { idlauch });

                        if (data.countMenuItem === 0) {
                            blockGauche.style.width = '60px';
                            blockMenu.style.width = '0px';
                            leftHeader.style.minWidth = '60px';
                            centerHeader.style.paddingLeft = "10px";
                            titleAdminHeader.style.width = "0px";
                            titleAdminHeader.style.opacity = "0";
                            document.getElementById("collapseIMG").src="/img/interface/Admin/small/collapse_disable.svg";
                        } else {
                            blockGauche.style.width = '340px';
                            blockMenu.style.width = '224px';
                            blockMenu.style.opacity = '1';
                            leftHeader.style.minWidth = '340px';
                            centerHeader.style.paddingLeft = "30px";
                            titleAdminHeader.style.width = "200px";
                            titleAdminHeader.style.opacity = "1";
                            document.getElementById("collapseIMG").src="/img/interface/Admin/small/collapse_off.svg";
                        }

                } catch (e) {
                    console.error(e.message);
                }

            });
        });


    //---------------------------------------------------------
    // ITEM SEPARATION
    //---------------------------------------------------------

        document.querySelectorAll('.itemSeparation').forEach(item => {
            item.addEventListener('click', async function () {

                const idlauch = this.id;
                const menuArea = document.querySelector('.menuArea_' + idlauch);

                slideToggle(menuArea, 200);
                this.classList.toggle('itemSeparationExpanded');

                try {
                    await ajax.post("/includes/ajax/admin/process-menu.php", { idlauch });
                } catch (e) {
                    console.error(e.message);
                }

            });
        });


    //---------------------------------------------------------
    // COLLAPSE MENU
    //---------------------------------------------------------

        document.querySelectorAll('.collapseMenu').forEach(btn => {
            btn.addEventListener('click', async () => {

                try {
                    const data = await ajax.post("/includes/ajax/admin/process-menu-status.php", {});
                    if (data.status === 0) {
                        blockGauche.style.width = '60px';
                        blockMenu.style.width = '0px';
                        blockMenu.style.opacity = '0';
                        leftHeader.style.minWidth = '60px';
                        centerHeader.style.paddingLeft = "10px";
                        titleAdminHeader.style.width = "0px";
                        titleAdminHeader.style.opacity = "0";
                        document.getElementById("collapseIMG").src="/img/interface/Admin/small/collapse_off.svg";
                    } else {
                        blockGauche.style.width = '340px';
                        blockMenu.style.width = '224px';
                        blockMenu.style.opacity = '1';
                        leftHeader.style.minWidth = '340px';
                        centerHeader.style.paddingLeft = "30px";
                        titleAdminHeader.style.width = "200px";
                        titleAdminHeader.style.opacity = "1";
                        document.getElementById("collapseIMG").src="/img/interface/Admin/small/collapse_on.svg";
                    }
                } catch (e) {
                    console.error(e.message);
                }

            });
        });



    //---------------------------------------------------------
    // CONTROL HEADER
    //---------------------------------------------------------

        const isUExpandedDataMenu = {};

        document.addEventListener('click', (e) => {
            if (!e.target.classList.contains('TimeControlHeader')) return;

            const el = e.target;
            const idDataMenu = el.id;
            const menuName = el.dataset.menu;
            const menuTxt = el.dataset.name;
            const targetMenu = document.getElementById(menuName);

            if (!(idDataMenu in isUExpandedDataMenu)) {
                isUExpandedDataMenu[idDataMenu] = true;
            }

            if (isUExpandedDataMenu[idDataMenu]) {
                el.innerHTML = "&#x2212; " + menuTxt;
                targetMenu.style.display = "block";
                isUExpandedDataMenu[idDataMenu] = false;
            } else {
                el.innerHTML = "&#x2b; " + menuTxt;
                targetMenu.style.display = "none";
                isUExpandedDataMenu[idDataMenu] = true;
            }

            e.preventDefault();
        });

});