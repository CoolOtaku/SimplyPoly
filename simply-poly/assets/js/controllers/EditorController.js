import EditorState from '../models/EditorState.js';
import EditorFrameView from '../views/EditorFrameView.js';
import TranslationPanelView from '../views/TranslationPanelView.js';

export default class EditorController {
    constructor() {
        this.state = new EditorState();
        this.view = new EditorFrameView('#editor-frame');
        this.translationPanel = new TranslationPanelView('#simplypoly-panel');
    }

    init() {
        this.view.onZoomChange = (zoom) => {
            this.state.setZoom(zoom);
        };

        this.view.onZoomIn = () => this.zoomIn();
        this.view.onZoomOut = () => this.zoomOut();

        this.view.init();

        document.addEventListener('simplypoly:element:selected', (e) => this.translationPanel.show(e.detail, params.langs));
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
