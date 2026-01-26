import EditorController from './controllers/editor-controller.js';

document.addEventListener('DOMContentLoaded', () => {
    const controller = new EditorController();
    controller.init();
});