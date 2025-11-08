import EditorController from './controllers/EditorController.js';

document.addEventListener('DOMContentLoaded', () => {
    const controller = new EditorController();
    controller.init();
});