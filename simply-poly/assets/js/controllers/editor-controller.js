import EditorState from '../models/editor-state.js';
import TranslationStore from '../models/translation-store.js';

import EditorFrameView from '../views/editor-frame-view.js';
import TranslationPanelView from '../views/translation-panel-view.js';

import TranslationService from '../services/translation-service.js';
import PreviewService from '../services/preview-service.js';

export default class EditorController {
    constructor() {
        this.state = new EditorState();
        this.store = new TranslationStore();

        this.view = new EditorFrameView('#editor-frame');
        this.translationPanel = new TranslationPanelView('#simplypoly-panel');

        this.translationService = new TranslationService();
        this.previewService = new PreviewService(this.view.frame, this.store);

        this.previewTimeout = null;
    }

    async init() {
        this.view.init();
        this.previewService.init();

        try {
            const data = await this.translationService.load();
            this.store.setAll(data);
        } catch (error) { console.error(error) }

        document.addEventListener('simplypoly:element:selected', (e) => {
            const path = e.detail.path;
            const existing = this.store.getAll()[path] || {};

            this.store.setOriginal(path, existing);
            this.translationPanel.show(e.detail, simplypoly.langs, this.store.getAll());
        });

        const previewSelect = document.getElementById('simplypoly-preview-lang');
        previewSelect.addEventListener('change', (e) => {
            const lang = e.target.value;

            document.dispatchEvent(new CustomEvent('simplypoly:preview:changed', { detail: { lang } }));
        });

        const saveBtn = document.querySelector('.save');
        saveBtn.addEventListener('click', async () => {
            try {
                saveBtn.disabled = true;

                const result = await this.translationService.save(this.store.getAll());

                alert(result.message);

                this.store.reset();
            } catch (error) {
                alert(error.message);
                saveBtn.disabled = false;
            }
        });

        document.addEventListener('simplypoly:translation:changed', (e) => {
            this.store.update(e.detail.path, e.detail.lang, e.detail.value);

            saveBtn.disabled = !this.store.hasChanges(e.detail.path);

            if (this.previewTimeout) clearTimeout(this.previewTimeout);

            this.previewTimeout = setTimeout(() => {
                this.previewService.applyPreview();
            }, 1000);
        });

        this.view.onZoomChange = (zoom) => this.state.setZoom(zoom);

        this.view.onZoomIn = () => this.zoomIn();
        this.view.onZoomOut = () => this.zoomOut();

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
