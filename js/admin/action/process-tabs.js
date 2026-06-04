/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/


    //---------------------------------------------------------
    // ADMIN TAB PROCESS
    //---------------------------------------------------------
    
      document.addEventListener("DOMContentLoaded", () => {

        const tabHolder = document.getElementById("tabsholder");
        const tabs = tabHolder.querySelectorAll("li");
        const contents = document.querySelectorAll(".tabscontent");

        function showTab(tabNumber) {
          // TABNUMBER
          const tabName = "tab" + tabNumber;
          const contentId = "content" + tabNumber;

          // HIDE CONTENT / REMOVE CURRENT STATUS
          contents.forEach(c => c.style.display = "none");
          tabs.forEach(t => t.classList.remove("current"));

          const content = document.getElementById(contentId);
          const tab = tabHolder.querySelector(`li[data-tab="${tabName}"]`);

          if (content && tab) {
            content.style.display = "block";
            tab.classList.add("current");

            if (tabNumber === 3 && typeof scheduler !== "undefined") {
              requestAnimationFrame(() => {
                setSchedulerHeight(); 
                scheduler.setCurrentView();
              });
            }

          }
        }
        // clic sur onglet
        tabs.forEach(tab => {
          tab.addEventListener("click", () => {
            const tabNumber = parseInt(tab.dataset.tab.replace("tab", ""), 10);
            showTab(tabNumber);
          });
        });

        // onglet initial depuis PHP
        const initialTab = parseInt(tabHolder.dataset.initialTab, 10) || 1;
        showTab(initialTab);

      });