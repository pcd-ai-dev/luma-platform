/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
* @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*/


//---------------------------------------------------------
// AJAX QUERY CLASS
//---------------------------------------------------------

    class AjaxService {
        constructor(baseURL = "", defaultHeaders = {}) {
            this.baseURL = baseURL;
            this.defaultHeaders = {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                ...defaultHeaders
            };
        }

        async request(url, options = {}) {
            const controller = new AbortController();
            const timeout = options.timeout || 10000;

            const timeoutId = setTimeout(() => controller.abort(), timeout);

            try {
                let body = options.body || null;
                let headers = {
                    ...this.defaultHeaders,
                    ...(options.headers || {})
                };

                if (body && !(body instanceof FormData)) {
                    body = JSON.stringify(body);
                    headers["Content-Type"] = "application/json";
                } else {
                    delete headers["Content-Type"];
                }

                const response = await fetch(this.baseURL + url, {
                    method: options.method || "GET",
                    headers,
                    body,
                    signal: controller.signal,
                    credentials: "same-origin"
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }

                return await response.json();

            } catch (error) {
                if (error.name === "AbortError") {
                    throw new Error("Timeout de la requête");
                }
                console.error("Erreur AJAX :", error);
                throw error;
            }
        }

        // Méthodes helpers
        get(url, options = {}) {
            return this.request(url, {
                ...options,
                method: "GET"
            });
        }

        post(url, data = {}, options = {}) {
            return this.request(url, {
                ...options,
                method: "POST",
                body: data
            });
        }

        put(url, data = {}, options = {}) {
            return this.request(url, {
                ...options,
                method: "PUT",
                body: data
            });
        }

        delete(url, options = {}) {
            return this.request(url, {
                ...options,
                method: "DELETE"
            });
        }
    }