$(document).ready(function () {

    const languageSelector = document.getElementById("languageSelector");

    let currentLanguage = localStorage.getItem("language") || "en";

    function loadTranslations() {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "GET",
                url: `translations/${currentLanguage}.json`,
                dataType: "json",
                success: function (response) {
                    resolve(response);
                },
                error: function (error) {
                    reject(error);
                },
            });
        });
    }

    function applyTranslations(translations) {
        const selectors = {
            ".navbar-brand": "navbar_brand",
            "#userDropdown + .dropdown-menu a": "user_dropdown_logout",
            "#run-code": "run_code_button",
            "#htmlOutput + label": "html_output_label",
            "#textOutput + label": "text_output_label",
            "#infoModal .modal-title": "modal_title",
            "#infoModal .modal-body p": "modal_text",
            "#infoModal .btn": "modal_close",
            "#reviewTitle": "review_title",
            "#loginTitle": "login_title",
            "#loginUsername": "login_username",
            "#loginPassword": "login_password",
            "#loginButton": "login_button",
        };

        for (const [selector, translationKey] of Object.entries(selectors)) {
            const element = document.querySelector(selector);
            if (element) {
                element.textContent = translations[translationKey];
            }
        }
    }

    async function changeLanguage() {
        localStorage.setItem("language", currentLanguage);
        const translations = await loadTranslations();
        applyTranslations(translations);
    }

    languageSelector.value = currentLanguage;
    changeLanguage();
    languageSelector.addEventListener("change", () => {
        currentLanguage = languageSelector.value;
        changeLanguage();
    });
});