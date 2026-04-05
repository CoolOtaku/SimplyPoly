import EditorController from './controllers/editor-controller.js';

jQuery(document).ready(function ($) {
    const controller = new EditorController();
    controller.init();
});