import Helper from '../helper.js';

export default class EditorFrameView {
    constructor(selector) {
        this.frame = document.querySelector(selector);

        this.iframeDoc = null;
        this.onZoomChange = null;
        this.onZoomIn = null;
        this.onZoomOut = null;
    }

    init() {
        if (!this.frame) {
            console.error('❌ Editor iframe not found');
            return;
        }

        this.frame.addEventListener('load', () => this.onWait());
    }

    onWait(attempt = 0) {
        try {
            const doc = this.frame.contentDocument || this.frame.contentWindow.document;
            if (doc && doc.readyState === 'complete' && doc.body) {
                this.iframeDoc = doc;
                this.onLoad();
                return;
            }
        } catch (e) {
            console.warn('⚠️ Iframe not accessible yet...');
        }

        if (attempt < 50) setTimeout(() => this.onWait(attempt + 1), 100);
        else console.error('❌ Iframe never became ready!');
    }

    onLoad() {
        console.log('✅ Iframe fully ready');

        const cssUrl = this.frame.dataset.css;
        if (cssUrl) {
            const link = this.iframeDoc.createElement('link');
            link.rel = 'stylesheet';
            link.href = cssUrl;
            this.iframeDoc.head.appendChild(link);
        }

        this.enableHoverEditable();

        this.iframeDoc.addEventListener('wheel', (e) => {
            if (e.ctrlKey) {
                e.preventDefault();

                if (e.deltaY < 0) if (this.onZoomIn) this.onZoomIn();
                else if (this.onZoomOut) this.onZoomOut();

                this.frame.classList.add('zoom-cursor');
            }
        }, { passive: false });

        this.frame.classList.add('loaded');
    }

    setZoom(zoom) {
        this.frame.style.setProperty('--zoom', zoom);

        if (zoom !== 1) this.frame.classList.add('zoom-cursor');
        else this.frame.classList.remove('zoom-cursor');

        if (this.onZoomChange) this.onZoomChange(zoom);
    }

    enableHoverEditable() {
        if (!this.iframeDoc) {
            console.warn('❌ Iframe document not ready yet');
            return;
        }

        let lastHovered = null;
        const editableTags = [
            'p', 'span', 'strong', 'em', 'a', 'b', 'i', 'u', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
        ];

        // HOVER
        this.iframeDoc.addEventListener('mouseover', (e) => {
            const target = e.target;

            if (lastHovered && lastHovered !== target) lastHovered.classList.remove('simplypoly-editable-hover');

            const tag = target.tagName.toLowerCase();
            const hasText = target.childNodes.length === 1 &&
                target.childNodes[0].nodeType === Node.TEXT_NODE &&
                target.textContent.trim().length > 0;

            if (editableTags.includes(tag) || hasText) {
                target.classList.add('simplypoly-editable-hover');
                lastHovered = target;
            }
        });

        this.iframeDoc.addEventListener('mouseout', (e) => {
            if (e.target === lastHovered) {
                e.target.classList.remove('simplypoly-editable-hover');
                lastHovered = null;
            }
        });

        // CLICK
        this.iframeDoc.addEventListener('click', (e) => {
            const target = e.target;

            if (!target.classList.contains('simplypoly-editable-hover')) return;

            e.preventDefault();
            e.stopPropagation();

            const payload = {
                tag: target.tagName.toLowerCase(),
                text: target.innerText || '',
                html: target.innerHTML,
                isImage: target.tagName.toLowerCase() === 'img',
                src: target.tagName.toLowerCase() === 'img' ? target.src : null,
                path: Helper.getDomPath(target)
            };

            document.dispatchEvent(new CustomEvent('simplypoly:element:selected', { detail: payload }));
        });
    }
}