/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright 2003-2026 Lumaprod
*/


    //---------------------------------------------------------
    // SLIDE UP FUNCTION
    //---------------------------------------------------------

        function slideUp(element, duration = 300) {
            return new Promise(resolve => {
                if (!element) return resolve();
                element.style.height = element.offsetHeight + 'px';
                element.style.overflow = 'hidden';
                element.style.transition = `height ${duration}ms`;
                requestAnimationFrame(() => {
                    element.style.height = 0;
                });
                setTimeout(() => {
                    element.style.display = 'none';
                    element.style.removeProperty('height');
                    element.style.removeProperty('overflow');
                    element.style.removeProperty('transition');
                    resolve();
                }, duration);
            });
        }


    //---------------------------------------------------------
    // SLIDE DOWN FUNCTION
    //---------------------------------------------------------

        function slideDown(element, duration = 300) {
            return new Promise(resolve => {
                if (!element) return resolve();
                element.style.removeProperty('display');
                let display = window.getComputedStyle(element).display;
                if (display === 'none') display = 'block';
                element.style.display = display;

                let height = element.offsetHeight;
                element.style.height = '0px';
                element.style.overflow = 'hidden';
                element.style.transition = `height ${duration}ms`;

                requestAnimationFrame(() => {
                    element.style.height = height + 'px';
                });

                setTimeout(() => {
                    element.style.removeProperty('height');
                    element.style.removeProperty('overflow');
                    element.style.removeProperty('transition');
                    resolve();
                }, duration);
            });
        }


    //---------------------------------------------------------
    // SLIDE TOOGLE FUNCTION
    //---------------------------------------------------------

        function slideToggle(element, duration = 300) {
            const isHidden = window.getComputedStyle(element).display === "none";

            element.style.overflow = "hidden";
            element.style.transition = `height ${duration}ms ease`;

            if (isHidden) {
                // SHOW
                element.style.display = "block";

                // force reflow
                element.offsetHeight;

                const height = element.scrollHeight;

                element.style.height = "0px";

                requestAnimationFrame(() => {
                    element.style.height = height + "px";
                });

            } else {
                // HIDE
                const height = element.scrollHeight;

                element.style.height = height + "px";

                requestAnimationFrame(() => {
                    element.style.height = "0px";
                });
            }

            // cleanup unique
            const onEnd = () => {
                if (isHidden) {
                    element.style.height = "auto";
                } else {
                    element.style.display = "none";
                }

                element.style.overflow = "";
                element.style.transition = "";
                element.removeEventListener("transitionend", onEnd);
            };

            element.addEventListener("transitionend", onEnd, { once: true });
        }


    //---------------------------------------------------------
    // WARNING MESSAGE FUNCTION
    //---------------------------------------------------------

        function showWarningMessage(id, message, backgroundColor = "#81B929", duration = 2000) {
            
            const el = document.getElementById(id);

            el.style.backgroundColor = backgroundColor;
            el.style.color = "#fff";
            el.style.display = "block";
            el.style.opacity = "1";
            el.innerHTML = message;

            setTimeout(() => {
                el.style.transition = "opacity 0.6s ease";
                el.style.opacity = "0";

                setTimeout(() => {
                el.style.display = "none";
                el.style.opacity = "1";
                }, 600);

            }, duration);
        }


    //---------------------------------------------------------
    // AJAX QUERY INIT
    //---------------------------------------------------------

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const siteHost = document.querySelector('meta[name="identifier-URL"]').content;

        const ajax = new AjaxService(siteHost, {
            "X-CSRF-Token": csrfToken
        });