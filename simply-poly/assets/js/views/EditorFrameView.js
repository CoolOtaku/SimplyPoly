export default class EditorFrameView {
    constructor(selector) {
        this.frame = document.querySelector(selector);
        this.onZoomChange = null;
        this.onZoomIn = null;
        this.onZoomOut = null;
        this.onIframeLoaded = null;
    }

    init() {
        if (!this.frame) {
            console.error('Editor iframe not found');
            return;
        }

        this.frame.addEventListener('load', () => this.onLoad());
    }

    onLoad() {
        const iframeDoc = this.frame.contentDocument || this.frame.contentWindow.document;

        const style = iframeDoc.createElement('style');
        style.textContent = '#wpadminbar { display: none !important; }';
        iframeDoc.head.appendChild(style);

        iframeDoc.addEventListener('wheel', (e) => {
            if (e.ctrlKey) {
                e.preventDefault();
                if (e.deltaY < 0) {
                    if (this.onZoomIn) this.onZoomIn();
                } else {
                    if (this.onZoomOut) this.onZoomOut();
                }
                this.frame.classList.add('zoom-cursor');
            }
        }, { passive: false });

        this.frame.classList.add('loaded');

        if (this.onIframeLoaded) this.onIframeLoaded();
    }

    setZoom(zoom) {
        this.frame.style.setProperty('--zoom', zoom);

        if (zoom !== 1) this.frame.classList.add('zoom-cursor');
        else this.frame.classList.remove('zoom-cursor');

        if (this.onZoomChange) this.onZoomChange(zoom);
    }
}
