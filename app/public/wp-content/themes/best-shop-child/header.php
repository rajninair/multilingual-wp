<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'best-shop'); ?></a>
    
    <header id="masthead" class="site-header">
        <div class="header-wrapper">
            <div class="container">
                <div class="header-content">
                    <div class="site-branding">
                        <?php
                        if (has_custom_logo()) :
                            the_custom_logo();
                        endif;
                        ?>
                        <div class="site-title-wrap">
                            <?php
                            $site_title = get_bloginfo('name');
                            $site_description = get_bloginfo('description', 'display');
                            
                            if ($site_title) : ?>
                                <h1 class="site-title">
                                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                        <?php echo esc_html($site_title); ?>
                                    </a>
                                </h1>
                            <?php endif;

                            if ($site_description) : ?>
                                <p class="site-description"><?php echo esc_html($site_description); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="header-right">
                        <div class="language-switcher">
                            <?php 
                            if (class_exists('Multilingual_Config')) {
                                $languages = Multilingual_Config::get_supported_languages();
                                $current_lang = Multilingual_Config::get_current_language();
                                
                                // Get current URL path without language code
                                $current_path = $_SERVER['REQUEST_URI'];
                                $path_segments = array_filter(explode('/', trim($current_path, '/')));
                                
                                // Remove any language codes from path
                                $clean_segments = array();
                                foreach ($path_segments as $segment) {
                                    if (!array_key_exists($segment, $languages)) {
                                        $clean_segments[] = $segment;
                                    }
                                }
                                
                                $relative_path = implode('/', $clean_segments);
                                
                                echo '<div class="select-wrapper">';
                                echo '<select id="language-select" onchange="handleLanguageChange(this.value, \'' . esc_js($relative_path) . '\');">';
                                foreach ($languages as $code => $lang) {
                                    $selected = ($current_lang === $code) ? 'selected' : '';
                                    printf(
                                        '<option value="%s" %s>%s %s</option>',
                                        esc_attr($code),
                                        $selected,
                                        esc_html($lang['flag']),
                                        esc_html($lang['native_name'])
                                    );
                                }
                                echo '</select>';
                                echo '</div>';
                            }
                            ?>
                        </div>

                        <nav id="site-navigation" class="main-navigation">
                            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                                <span class="toggle-bar"></span>
                                <span class="toggle-bar"></span>
                                <span class="toggle-bar"></span>
                            </button>
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'primary-menu',
                                'menu_id'        => 'primary-menu',
                                'container'      => false,
                                'menu_class'     => 'nav-menu',
                                'fallback_cb'    => 'wp_page_menu',
                                'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                            ));
                            ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>

<style>
/* Language Switcher Styles */
.language-switcher {
    margin-right: 20px;
    display: inline-block;
    vertical-align: middle;
    position: relative;
}

.language-switcher select {
    padding: 8px 35px 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
    font-size: 14px;
    line-height: 1.3;
    color: #333;
    width: auto;
    min-width: 120px;
    max-width: 100%;
    height: auto;
    margin: 0;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: calc(100% - 12px) center;
    background-size: 12px;
}

.language-switcher select:hover {
    border-color: #999;
}

.language-switcher select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.language-switcher select option {
    padding: 8px;
    font-size: 14px;
    line-height: 1.3;
}

/* Fix for Firefox */
@-moz-document url-prefix() {
    .language-switcher select {
        padding-right: 12px;
        background-image: none;
    }
}

/* Fix for IE */
.language-switcher select::-ms-expand {
    display: none;
}

/* Header Layout */
.header-wrapper {
    padding: 15px 0;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* Site Branding */
.site-branding {
    z-index: 1000;
}

.site-title a, .site-description {
    color: #333;
}

/* Navigation */
.main-navigation {
    z-index: 1000;
}

.nav-menu {
    margin: 0;
    padding: 0;
    list-style: none;
}

.nav-menu li a {
    color: #333;
}

/* Mobile Menu Toggle */
.menu-toggle {
    display: none;
    background: none;
    border: none;
    padding: 10px;
    cursor: pointer;
}

.toggle-bar {
    display: block;
    width: 25px;
    height: 2px;
    background-color: #333;
    margin: 5px 0;
    transition: all 0.3s;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .menu-toggle {
        display: block;
    }

    .main-navigation ul {
        display: none;
    }

    .main-navigation.toggled ul {
        display: block;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    .header-right {
        flex-direction: row-reverse;
    }

    .language-switcher {
        margin-right: 10px;
    }

    .language-switcher select {
        padding-right: 30px;
        font-size: 13px;
        min-width: 100px;
    }
}
</style>

<script>
function handleLanguageChange(langCode, currentPath) {
    // Store the language preference
    localStorage.setItem('preferred_language', langCode);
    document.cookie = `preferred_language=${langCode}; path=/; max-age=31536000`; // 1 year

    // Construct new URL with selected language
    let newPath = '/' + langCode;
    if (currentPath) {
        newPath += '/' + currentPath;
    }
    
    // Preserve query string
    if (window.location.search) {
        newPath += window.location.search;
    }
    
    // Preserve hash
    if (window.location.hash) {
        newPath += window.location.hash;
    }
    
    window.location.href = newPath;
}

// Initialize language from localStorage on page load
document.addEventListener('DOMContentLoaded', function() {
    const storedLang = localStorage.getItem('preferred_language');
    if (storedLang) {
        const currentLang = '<?php echo Multilingual_Config::get_current_language(); ?>';
        if (storedLang !== currentLang) {
            const currentPath = '<?php 
                $path_segments = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                if (!empty($path_segments[0]) && array_key_exists($path_segments[0], Multilingual_Config::get_supported_languages())) {
                    array_shift($path_segments);
                }
                echo implode('/', $path_segments);
            ?>';
            handleLanguageChange(storedLang, currentPath);
        }
    }
});

jQuery(document).ready(function($) {
    // Mobile menu toggle
    $('.menu-toggle').on('click', function() {
        $('#site-navigation').toggleClass('toggled');
        $(this).attr('aria-expanded', $('#site-navigation').hasClass('toggled'));
    });

    // Close menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#site-navigation').length) {
            $('#site-navigation').removeClass('toggled');
            $('.menu-toggle').attr('aria-expanded', 'false');
        }
    });
});
</script> 