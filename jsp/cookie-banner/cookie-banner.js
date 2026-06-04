/*
* Javascript to show and hide cookie banner using localstroage
*/

/**
 * Shows the Cookie banner 
 */

const COOKIE_KEY = "web_dev_isCookieAccepted";
const BANNER_CLASS = "nk-cookie-banner";

function getCookieBanner() {
    return document.querySelector(`.${BANNER_CLASS}`);
}

function showCookieBanner() {
    const banner = getCookieBanner();
    if (banner) {
        banner.style.display = "block";
    }
}

function hideCookieBanner() {
    localStorage.setItem(COOKIE_KEY, "yes");

    const banner = getCookieBanner();
    if (banner) {
        banner.style.display = "none";
    }
}

function initializeCookieBanner() {
    const isAccepted = localStorage.getItem(COOKIE_KEY);

    if (isAccepted !== "yes") {
        localStorage.setItem(COOKIE_KEY, "no");
        showCookieBanner();
    }
}

window.addEventListener("load", initializeCookieBanner);

window.nk_hideCookieBanner = hideCookieBanner;