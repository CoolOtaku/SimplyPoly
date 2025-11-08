import EditorFrameView from '../views/EditorFrameView.js';
import EditorState from '../models/EditorState.js';

export default class EditorController {
    constructor() {
        this.state = new EditorState();
        this.view = new EditorFrameView('#editor-frame');
    }

    init() {
        this.view.onZoomChange = (zoom) => {
            this.state.setZoom(zoom);
        };

        this.view.onZoomIn = () => this.zoomIn();
        this.view.onZoomOut = () => this.zoomOut();

        this.view.onIframeLoaded = () => {
            console.log('Iframe loaded and ready.');
        };

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
