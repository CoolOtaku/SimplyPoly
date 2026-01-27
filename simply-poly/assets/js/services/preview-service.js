export default class PreviewService {
    constructor(frame, store) {
        this.frame = frame;
        this.store = store;
        this.currentLang = null;
    }

    init() {
        document.addEventListener('simplypoly:preview:changed', (e) => {
            this.currentLang = e.detail.lang;

            if (!this.currentLang) {
                this.resetPreview();
                return;
            }

            this.applyPreview();
        });
    }

    applyPreview() {
        if (!this.currentLang) return;

        const doc = this.frame.contentDocument;
        const translations = this.store.getAll();

        Object.keys(translations).forEach(path => {
            const el = doc.querySelector(path);
            if (!el) return;

            const text = translations[path][this.currentLang];
            if (text !== undefined) el.textContent = text;
        });
    }

    resetPreview() {
        this.frame.contentWindow.location.reload();
    }
}