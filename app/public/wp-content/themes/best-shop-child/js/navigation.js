jQuery(document).ready(function($) {
    // Mobile menu toggle
    const menuToggle = $('.menu-toggle');
    const siteNavigation = $('#site-navigation');
    
    menuToggle.on('click', function(e) {
        e.preventDefault();
        siteNavigation.toggleClass('toggled');
        
        if (siteNavigation.hasClass('toggled')) {
            $(this).attr('aria-expanded', 'true');
            siteNavigation.find('ul').attr('aria-hidden', 'false');
        } else {
            $(this).attr('aria-expanded', 'false');
            siteNavigation.find('ul').attr('aria-hidden', 'true');
        }
    });

    // Close menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#site-navigation').length && !$(e.target).closest('.menu-toggle').length) {
            siteNavigation.removeClass('toggled');
            menuToggle.attr('aria-expanded', 'false');
            siteNavigation.find('ul').attr('aria-hidden', 'true');
        }
    });

    // Handle sub-menu accessibility
    $('.menu-item-has-children > a').after('<button class="sub-menu-toggle" aria-expanded="false"><span class="screen-reader-text">Toggle sub-menu</span></button>');
    
    $('.sub-menu-toggle').on('click', function(e) {
        e.preventDefault();
        const $this = $(this);
        const $submenu = $this.next('.sub-menu');
        
        $this.toggleClass('active');
        $submenu.toggleClass('toggled');
        
        if ($submenu.hasClass('toggled')) {
            $this.attr('aria-expanded', 'true');
            $submenu.attr('aria-hidden', 'false');
        } else {
            $this.attr('aria-expanded', 'false');
            $submenu.attr('aria-hidden', 'true');
        }
    });
}); 