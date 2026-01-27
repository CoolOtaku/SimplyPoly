import EditorState from '../models/editor-state.js';
import TranslationStore from '../models/translation-store.js';

import EditorFrameView from '../views/editor-frame-view.js';
import TranslationPanelView from '../views/translation-panel-view.js';

import TranslationService from '../services/translation-service.js';

export default class EditorController {
    constructor() {
        this.state = new EditorState();
        this.store = new TranslationStore();

        this.view = new EditorFrameView('#editor-frame', this.store);

        this.translationPanel = new TranslationPanelView('#simplypoly-panel');
        this.translationService = new TranslationService();
    }

    init() {
        document.addEventListener('simplypoly:element:selected', (e) => {
            const path = e.detail.path;
            const existing = this.store.getAll()[path] || {};

            this.store.setOriginal(path, existing);
            this.translationPanel.show(e.detail, params.langs, this.store.getAll());
        });

        const previewSelect = document.getElementById('simplypoly-preview-lang');
        previewSelect.addEventListener('change', (e) => {
            const lang = e.target.value;

            document.dispatchEvent(new CustomEvent('simplypoly:preview:changed', { detail: { lang } }));
        });

        const saveBtn = document.querySelector('.save');
        document.addEventListener('simplypoly:translation:changed', (e) => {
            this.store.update(e.detail.path, e.detail.lang, e.detail.value);

            if (this.store.hasChanges(e.detail.path)) saveBtn.disabled = false;
            else saveBtn.disabled = true;
        });

        this.view.onZoomChange = (zoom) => this.state.setZoom(zoom);

        this.view.onZoomIn = () => this.zoomIn();
        this.view.onZoomOut = () => this.zoomOut();

        this.view.init();

        document.getElementById('zoom-in').addEventListener('click', () => this.zoomIn());
        document.getElementById('zoom-out').addEventListener('click', () => this.zoomOut());
    }

    zoomIn() {
        this.state.zoomIn();
        this.view.setZoom(this.state.getZoom());
    }

    zoomOut() {
        this.state.zoomOut();
        this.view.setZoom(this.state.getZoom());
    }
}
