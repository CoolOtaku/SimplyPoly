jQuery(document).ready(function ($) {
    $('.simply-poly-switcher-dropdown').each(function () {
        const $dropdown = $(this);
        const $button = $dropdown.find('.simply-poly-switcher-toggle');
        const $menu = $dropdown.find('.simply-poly-switcher-menu');

        if ($button.data('initialized')) return;
        $button.data('initialized', true);

        $button.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const isVisible = $menu.is(':visible');
            $('.simply-poly-switcher-menu').hide();
            if (!isVisible) $menu.show();
        });
    });

    $(document).on('click', function () {
        $('.simply-poly-switcher-menu').hide();
    });
});