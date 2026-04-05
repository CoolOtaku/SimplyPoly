export default class TranslationPanelView {
    constructor(selector) {
        this.el = $(selector);
        this.sourceEl = this.el.find('.simplypoly-source-text');
        this.langsEl = this.el.find('.simplypoly-languages');
        this.isOpen = false;

        this.handleOutsideClick = this.handleOutsideClick.bind(this);
    }

    show(payload, languages, translations) {
        if (!this.el.length) return;

        this.el.removeClass('hidden');
        this.isOpen = true;

        this.renderSource(payload);
        this.renderLanguages(payload, languages, translations);

        setTimeout(() => $(document).on('click', this.handleOutsideClick), 0);
    }

    hide() {
        this.el.addClass('hidden');
        this.isOpen = false;

        $(document).off('click', this.handleOutsideClick);
    }

    handleOutsideClick(e) {
        if (!this.isOpen) return;
        if ($(e.target).closest(this.el).length) return;

        this.hide();
    }

    renderSource(payload) {
        this.sourceEl.html(`
            <div class="simplypoly-original">
                ${payload.isImage ? '[image]' : payload.text}
            </div>
        `);
    }

    renderLanguages(payload, languages, translations) {
        this.langsEl.html('');

        const existing = translations[payload.path] || {};

        languages.forEach((lang) => {
            const savedValue = existing[lang] || '';

            const $row = $(`
                <div class="simplypoly-lang">
                    <img src="https://flagcdn.com/${lang}.svg" width="24" alt="${lang}">
                    <textarea
                        placeholder="Translate to ${lang.toUpperCase()}"
                        data-lang="${lang}"
                        data-path="${payload.path}"
                    >${savedValue}</textarea>
                </div>
            `);

            $row.find('textarea').on('input', function () {
                document.dispatchEvent(new CustomEvent('simplypoly:translation:changed', {
                    detail: {
                        path: payload.path,
                        lang: lang,
                        value: $(this).val()
                    }
                }));
            });

            this.langsEl.append($row);
        });
    }
}