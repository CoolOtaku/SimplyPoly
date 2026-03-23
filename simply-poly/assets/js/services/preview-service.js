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

        const adminBar = doc.getElementById('wpadminbar');
        let parent = null;
        let nextSibling = null;

        if (adminBar && adminBar.parentNode) {
            parent = adminBar.parentNode;
            nextSibling = adminBar.nextSibling;
            parent.removeChild(adminBar);
        }

        Object.keys(translations).forEach(path => {
            const el = doc.querySelector(path);
            if (!el) return;

            const text = translations[path][this.currentLang];
            if (text !== undefined) el.textContent = text;
        });

        if (adminBar && parent) {
            if (nextSibling) parent.insertBefore(adminBar, nextSibling);
            else parent.appendChild(adminBar);
        }
    }

    resetPreview() {
        this.frame.contentWindow.location.reload();
    }
}