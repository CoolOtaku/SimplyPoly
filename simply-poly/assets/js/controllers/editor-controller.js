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
        } catch (error) {
            console.error(error);
        }

        $(document).on('simplypoly:element:selected', (e) => {
            const path = e.originalEvent.detail.path;
            const existing = this.store.getAll()[path] || {};

            this.store.setOriginal(path, existing);
            this.translationPanel.show(
                e.originalEvent.detail,
                simplypoly.langs,
                this.store.getAll()
            );
        });

        $('#simplypoly-preview-lang').on('change', function () {
            const lang = $(this).val();

            document.dispatchEvent(new CustomEvent('simplypoly:preview:changed', {detail: { lang }}));
        });

        const $saveBtn = $('.save');

        $saveBtn.on('click', async () => {
            try {
                $saveBtn.prop('disabled', true);

                const result = await this.translationService.save(this.store.getAll());

                alert(result.message);

                this.store.reset();
            } catch (error) {
                alert(error.message);
                $saveBtn.prop('disabled', false);
            }
        });

        $(document).on('simplypoly:translation:changed', (e) => {
            const detail = e.originalEvent.detail;

            this.store.update(detail.path, detail.lang, detail.value);
            $saveBtn.prop('disabled', !this.store.hasChanges(detail.path));
            if (this.previewTimeout) clearTimeout(this.previewTimeout);

            this.previewTimeout = setTimeout(() => this.previewService.applyPreview(), 1000);
        });

        this.view.onZoomChange = (zoom) => this.state.setZoom(zoom);

        this.view.onZoomIn = () => this.zoomIn();
        this.view.onZoomOut = () => this.zoomOut();

        $('#zoom-in').on('click', () => this.zoomIn());
        $('#zoom-out').on('click', () => this.zoomOut());
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