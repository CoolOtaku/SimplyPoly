export default class TranslationStore {
    constructor() {
        this.original = {};
        this.current = {};
    }

    setOriginal(path, translations) {
        this.original[path] = { ...translations };
        this.current[path] = { ...translations };
    }

    update(path, lang, value) {
        if (!this.current[path]) this.current[path] = {};
        this.current[path][lang] = value;
    }

    hasChanges(path) {
        const orig = this.original[path] || {};
        const curr = this.current[path] || {};
        return JSON.stringify(orig) !== JSON.stringify(curr);
    }

    getAll() {
        return this.current;
    }

    reset() {
        this.original = JSON.parse(JSON.stringify(this.current));
    }
}