export default class Helper {
    static getDomPath(el) {
        if (!el || el.nodeType !== Node.ELEMENT_NODE) return null;

        const segments = [];

        while (el && el.tagName.toLowerCase() !== 'html') {
            const tag = el.tagName.toLowerCase();

            const parent = el.parentElement;
            if (!parent) break;

            const siblings = Array.from(parent.children)
                .filter(child => child.tagName === el.tagName);

            const index = siblings.indexOf(el) + 1;

            segments.unshift(`${tag}:nth-of-type(${index})`);

            el = parent;
        }

        return segments.join(' > ');
    }
}