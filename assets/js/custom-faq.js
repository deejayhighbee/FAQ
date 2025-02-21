jQuery(document).ready(function($) {
    $('.faq-title').on('click', function() {
        var target = $(this).data('target');
        var isExpanded = $(this).attr('aria-expanded') === 'true';

        if (isExpanded) {
            // Collapse the currently open panel
            $(target).slideUp(300).removeClass('show');
            $(this).attr('aria-expanded', 'false');
        } else {
            // Collapse any open panels
            $('.collapse.show').slideUp(300).removeClass('show');
            $('.faq-title').attr('aria-expanded', 'false');

            // Expand the clicked panel
            $(target).slideDown(300).addClass('show');
            $(this).attr('aria-expanded', 'true');
        }
    });
});
