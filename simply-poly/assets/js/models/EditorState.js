export default class EditorState {
    constructor() {
        this.zoom = 1;
        this.minZoom = 0.3;
        this.maxZoom = 3;
    }

    setZoom(value) {
        this.zoom = Math.min(this.maxZoom, Math.max(this.minZoom, value));
    }

    getZoom() {
        return this.zoom;
    }

    zoomIn(step = 0.1) {
        this.setZoom(this.zoom + step);
    }

    zoomOut(step = 0.1) {
        this.setZoom(this.zoom - step);
    }
}