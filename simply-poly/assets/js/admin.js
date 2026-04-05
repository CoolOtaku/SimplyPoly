jQuery(document).ready(function ($) {
    function formatLang(lang) {
        if (!lang.id) return lang.text;
        const flagUrl = $(lang.element).data('flag');
        if (!flagUrl) return lang.text;
        return $('<span><img src="' + flagUrl + '" width="20" style="margin-right:8px;vertical-align:middle;"> ' + lang.text + '</span>');
    }

    $('#language-select').select2({
        templateResult: formatLang,
        templateSelection: formatLang,
        width: 'resolve'
    });

    $('#default-language').select2({
        templateResult: formatLang,
        templateSelection: formatLang
    });
});

window.copySortcode = function (button) {
    const input = button.previousElementSibling;
    input.select();
    input.setSelectionRange(0, 99999);

    document.execCommand('copy');
    
    const lang = document.documentElement.lang || 'en';

    const messages = {
        'uk': 'Скопійовано!',
        'en': 'Copied!'
    };
    const copiedText = messages[lang] || messages['en'];

    const originalText = button.innerText;
    button.innerText = copiedText;

    setTimeout(() => button.innerText = originalText, 1500);
}