import Helper from '../helper.js';

export default class EditorFrameView {
    constructor(selector) {
        this.frame = $(selector).get(0);

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

        $(this.frame).on('load', () => this.onWait());
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

        const cssUrl = $(this.frame).data('css');

        if (cssUrl) {
            const link = this.iframeDoc.createElement('link');
            link.rel = 'stylesheet';
            link.href = cssUrl;
            this.iframeDoc.head.appendChild(link);
        }

        this.enableHoverEditable();

        $(this.iframeDoc).on('wheel', (e) => {
            const originalEvent = e.originalEvent;

            if (originalEvent.ctrlKey) {
                e.preventDefault();

                if (originalEvent.deltaY < 0) if (this.onZoomIn) this.onZoomIn();
                else if (this.onZoomOut) this.onZoomOut();

                $(this.frame).addClass('zoom-cursor');
            }
        });

        $(this.frame).addClass('loaded');
    }

    setZoom(zoom) {
        this.frame.style.setProperty('--zoom', zoom);

        if (zoom !== 1) $(this.frame).addClass('zoom-cursor');
        else $(this.frame).removeClass('zoom-cursor');

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

        $(this.iframeDoc).on('mouseover', (e) => {
            const target = e.target;

            if (lastHovered && lastHovered !== target) 
                $(lastHovered).removeClass('simplypoly-editable-hover');

            const tag = target.tagName.toLowerCase();

            const hasText = target.childNodes.length === 1 &&
                target.childNodes[0].nodeType === Node.TEXT_NODE &&
                target.textContent.trim().length > 0;

            if (editableTags.includes(tag) || hasText) {
                $(target).addClass('simplypoly-editable-hover');
                lastHovered = target;
            }
        });

        $(this.iframeDoc).on('mouseout', (e) => {
            if (e.target === lastHovered) {
                $(e.target).removeClass('simplypoly-editable-hover');
                lastHovered = null;
            }
        });

        $(this.iframeDoc).on('click', (e) => {
            const target = e.target;

            if (!$(target).hasClass('simplypoly-editable-hover')) return;

            e.preventDefault();
            e.stopPropagation();

            const tagName = target.tagName.toLowerCase();

            const payload = {
                tag: tagName,
                text: target.innerText || '',
                html: target.innerHTML,
                isImage: tagName === 'img',
                src: tagName === 'img' ? target.src : null,
                path: Helper.getDomPath(target)
            };

            document.dispatchEvent(new CustomEvent('simplypoly:element:selected', {detail: payload}));
        });
    }
}