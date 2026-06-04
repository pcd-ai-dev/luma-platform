/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


    //---------------------------------------------------------
    // FRONT LAZYLOAD JS
    //---------------------------------------------------------

        function initLazyLoad() {

            const images = document.querySelectorAll("img.lazyImg");

            const observer = new IntersectionObserver((entries, obs) => {

                entries.forEach(entry => {

                    if (!entry.isIntersecting) return;

                    const img = entry.target;
                    const src = img.dataset.src;

                    if (!src) return;

                    img.src = src;
                    img.onload = () => img.classList.add("loaded");

                    img.removeAttribute("data-src");
                    obs.unobserve(img);
                });

            }, {
                rootMargin: "200px"
            });

            images.forEach(img => observer.observe(img));
        }