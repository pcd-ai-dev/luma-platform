/**
 * Copie un texte dans le presse-papiers
 * @param {string} text - Texte à copier
 * @param {Function} [onSuccess] - Callback si succès
 * @param {Function} [onError] - Callback si erreur
 */

    function copyToClipboard(text, onSuccess, onError) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        // Méthode moderne asynchrone
        navigator.clipboard.writeText(text)
        .then(() => {
            if (onSuccess) onSuccess();
            console.log("Texte copié dans le presse-papiers !");
        })
        .catch(err => {
            if (onError) onError(err);
            console.error("Erreur lors de la copie :", err);
        });
    } else {
        // Fallback pour navigateurs plus anciens
        const textarea = document.createElement("textarea");
        textarea.value = text;
        textarea.style.position = "fixed";  // évite de faire défiler la page
        textarea.style.opacity = "0";       // invisible
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();

        try {
        const successful = document.execCommand("copy");
        if (successful) {
            if (onSuccess) onSuccess();
            console.log("Texte copié avec execCommand !");
        } else {
            throw new Error("execCommand unsuccessful");
        }
        } catch (err) {
        if (onError) onError(err);
        console.error("Impossible de copier le texte :", err);
        } finally {
        document.body.removeChild(textarea);
        }
    }
    }