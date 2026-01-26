export default class TranslationPanelView {
    constructor(selector) {
        this.el = document.querySelector(selector);
        this.sourceEl = this.el.querySelector('.simplypoly-source-text');
        this.langsEl = this.el.querySelector('.simplypoly-languages');
        this.isOpen = false;

        this.handleOutsideClick = this.handleOutsideClick.bind(this);
    }

    show(payload, languages, translations) {
        if (!this.el) return;

        this.el.classList.remove('hidden');
        this.isOpen = true;

        this.renderSource(payload);
        this.renderLanguages(payload, languages, translations);

        setTimeout(() => {
            document.addEventListener('click', this.handleOutsideClick);
        }, 0);
    }

    hide() {
        this.el.classList.add('hidden');
        this.isOpen = false;

        document.removeEventListener('click', this.handleOutsideClick);
    }

    handleOutsideClick(e) {
        if (!this.isOpen) return;
        if (this.el.contains(e.target)) return;

        this.hide();
    }

    renderSource(payload) {
        this.sourceEl.innerHTML = `
            <div class="simplypoly-original">
                ${payload.isImage ? '[image]' : payload.text}
            </div>
        `;
    }

    renderLanguages(payload, languages, translations) {
        this.langsEl.innerHTML = '';

        const existing = translations[payload.path] || {};

        languages.forEach(lang => {
            const row = document.createElement('div');
            row.className = 'simplypoly-lang';

            const savedValue = existing[lang] || '';

            row.innerHTML = `
                <img src="https://flagcdn.com/${lang}.svg" width="24" alt="${lang}">
                <textarea 
                    placeholder="Translate to ${lang.toUpperCase()}"
                    data-lang="${lang}"
                    data-path="${payload.path}"
                >${savedValue}</textarea>
            `;

            row.querySelector('textarea').addEventListener('input', (e) => {
                document.dispatchEvent(new CustomEvent('simplypoly:translation:changed', {
                    detail: { path: payload.path, lang: lang, value: e.target.value }
                }));
            });

            this.langsEl.appendChild(row);
        });
    }
}
