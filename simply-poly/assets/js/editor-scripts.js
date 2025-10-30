let zoom = 1;
const frame = document.getElementById('editor-frame');

function zoomIn() {
    zoom += 0.1;
    frame.style.transform = `scale(${zoom})`;
}

function zoomOut() {
    zoom = Math.max(0.3, zoom - 0.1);
    frame.style.transform = `scale(${zoom})`;
}