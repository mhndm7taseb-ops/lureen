<?php

/**
 * social media icons
 */
/**
 * Lureen Social Media Icons - Helper Snippet
 * 
 * Adds Instagram, Facebook, WhatsApp, and TikTok icons to the navigation bar
 * Between the store logo and menu items
 * 
 * Installation: Add to Code Snippets Pro as a separate snippet
 */

if (!defined('ABSPATH')) { exit; }

/* -----------------------------------------------------------
   SOCIAL MEDIA ICONS IN NAVIGATION
----------------------------------------------------------- */

// Add social media icons CSS
add_action('wp_head', function() { ?>
<style>
/* Social Media Icons Container */
.lureen-social-icons {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0 auto;
}

.lureen-social-icons a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    transition: all 0.3s ease;
    text-decoration: none;
    position: relative;
}

.lureen-social-icons a:hover {
    transform: translateY(-2px);
}

/* Instagram Icon */
.lureen-social-icons .social-instagram {
    background: linear-gradient(135deg, #9b59b6, #e74c3c);
    color: #fff;
}

.lureen-social-icons .social-instagram:hover {
    box-shadow: 0 6px 15px rgba(155, 89, 182, 0.4);
    background: linear-gradient(135deg, #8e44ad, #c0392b);
}

/* Facebook Icon */
.lureen-social-icons .social-facebook {
    background: linear-gradient(135deg, #9b59b6, #e74c3c);
    color: #fff;
}

.lureen-social-icons .social-facebook:hover {
    box-shadow: 0 6px 15px rgba(155, 89, 182, 0.4);
    background: linear-gradient(135deg, #8e44ad, #c0392b);
}

/* WhatsApp Icon */
.lureen-social-icons .social-whatsapp {
    background: linear-gradient(135deg, #9b59b6, #e74c3c);
    color: #fff;
}

.lureen-social-icons .social-whatsapp:hover {
    box-shadow: 0 6px 15px rgba(155, 89, 182, 0.4);
    background: linear-gradient(135deg, #8e44ad, #c0392b);
}

/* TikTok Icon */
.lureen-social-icons .social-tiktok {
    background: linear-gradient(135deg, #9b59b6, #e74c3c);
    color: #fff;
}

.lureen-social-icons .social-tiktok:hover {
    box-shadow: 0 6px 15px rgba(155, 89, 182, 0.4);
    background: linear-gradient(135deg, #8e44ad, #c0392b);
}

.lureen-social-icons i {
    font-size: 13px;
}

/* Mobile Responsive */
@media(max-width: 768px) {
    .lureen-social-icons {
        gap: 5px;
        margin: 0;
    }
    
    .lureen-social-icons a {
        width: 18px;
        height: 18px;
    }
    
    .lureen-social-icons i {
        font-size: 11px;
    }
    
    /* Keep original nav container layout */
    .lureen-nav-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .lureen-custom-logo {
        order: 1;
    }
    
    .lureen-social-icons {
        order: 2;
        margin-left: auto;
        margin-right: 10px;
    }
    
    .lureen-mobile-toggle {
        order: 3;
    }
    
    .lureen-nav-menu {
        order: 4;
    }
}

/* Desktop - place icons between logo and menu */
@media(min-width: 769px) {
    .lureen-nav-container {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
    }
    
    .lureen-custom-logo {
        justify-self: start;
    }
    
    .lureen-social-icons {
        justify-self: center;
    }
    
    .lureen-nav-menu {
        justify-self: end;
    }
    
    .lureen-mobile-toggle {
        display: none;
    }
}
</style>
<?php }, 101);

// Inject social media icons into navigation
add_action('wp_footer', function() { ?>
<script>
jQuery(document).ready(function($) {
    // Social media URLs - CUSTOMIZE THESE WITH YOUR ACTUAL LINKS
    var socialLinks = {
        instagram: 'https://instagram.com/lureenonline',  // Replace with your Instagram
        facebook: 'https://facebook.com/lureenonline',    // Replace with your Facebook
        whatsapp: 'https://wa.me/966XXXXXXXXX',           // Replace with your WhatsApp number
        tiktok: 'https://tiktok.com/@lureenonline'        // Replace with your TikTok
    };
    
    // Create social icons HTML
    var socialIconsHTML = `
        <div class="lureen-social-icons">
            <a href="${socialLinks.instagram}" target="_blank" rel="noopener noreferrer" class="social-instagram" title="تابعونا على إنستغرام" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="${socialLinks.facebook}" target="_blank" rel="noopener noreferrer" class="social-facebook" title="تابعونا على فيسبوك" aria-label="Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="${socialLinks.whatsapp}" target="_blank" rel="noopener noreferrer" class="social-whatsapp" title="تواصل معنا عبر واتساب" aria-label="WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a href="${socialLinks.tiktok}" target="_blank" rel="noopener noreferrer" class="social-tiktok" title="تابعونا على تيك توك" aria-label="TikTok">
                <i class="fab fa-tiktok"></i>
            </a>
        </div>
    `;
    
    // Insert after logo on desktop, before toggle on mobile
    var $navContainer = $('.lureen-nav-container');
    var $logo = $('.lureen-custom-logo');
    
    if ($logo.length && $navContainer.length) {
        // Check if icons already exist (prevent duplicates)
        if (!$('.lureen-social-icons').length) {
            $(socialIconsHTML).insertAfter($logo);
        }
    }
});
</script>
<?php }, 102);

// Optional: Add settings page to customize social links
add_action('admin_menu', function() {
    add_options_page(
        'إعدادات روابط التواصل الاجتماعي',
        'روابط التواصل',
        'manage_options',
        'lureen-social-links',
        'lureen_social_links_page'
    );
});

function lureen_social_links_page() {
    // Save settings
    if (isset($_POST['lureen_social_submit'])) {
        check_admin_referer('lureen_social_links');
        
        update_option('lureen_instagram_url', sanitize_text_field($_POST['instagram_url']));
        update_option('lureen_facebook_url', sanitize_text_field($_POST['facebook_url']));
        update_option('lureen_whatsapp_url', sanitize_text_field($_POST['whatsapp_url']));
        update_option('lureen_tiktok_url', sanitize_text_field($_POST['tiktok_url']));
        
        echo '<div class="updated"><p>تم حفظ الإعدادات بنجاح!</p></div>';
    }
    
    $instagram = get_option('lureen_instagram_url', 'https://instagram.com/lureenonline');
    $facebook = get_option('lureen_facebook_url', 'https://facebook.com/lureenonline');
    $whatsapp = get_option('lureen_whatsapp_url', 'https://wa.me/966XXXXXXXXX');
    $tiktok = get_option('lureen_tiktok_url', 'https://tiktok.com/@lureenonline');
    ?>
    <div class="wrap" dir="rtl">
        <h1>إعدادات روابط التواصل الاجتماعي</h1>
        <form method="post" action="">
            <?php wp_nonce_field('lureen_social_links'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="instagram_url">رابط إنستغرام</label></th>
                    <td>
                        <input type="url" id="instagram_url" name="instagram_url" value="<?php echo esc_attr($instagram); ?>" class="regular-text" dir="ltr" />
                        <p class="description">مثال: https://instagram.com/yourusername</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="facebook_url">رابط فيسبوك</label></th>
                    <td>
                        <input type="url" id="facebook_url" name="facebook_url" value="<?php echo esc_attr($facebook); ?>" class="regular-text" dir="ltr" />
                        <p class="description">مثال: https://facebook.com/yourpage</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="whatsapp_url">رابط واتساب</label></th>
                    <td>
                        <input type="url" id="whatsapp_url" name="whatsapp_url" value="<?php echo esc_attr($whatsapp); ?>" class="regular-text" dir="ltr" />
                        <p class="description">مثال: https://wa.me/966501234567 (استخدم رقم الدولة بدون +)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="tiktok_url">رابط تيك توك</label></th>
                    <td>
                        <input type="url" id="tiktok_url" name="tiktok_url" value="<?php echo esc_attr($tiktok); ?>" class="regular-text" dir="ltr" />
                        <p class="description">مثال: https://tiktok.com/@yourusername</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('حفظ الإعدادات', 'primary', 'lureen_social_submit'); ?>
        </form>
    </div>
    <?php
}

// Use saved links in frontend
add_action('wp_footer', function() {
    $instagram = get_option('lureen_instagram_url', 'https://instagram.com/lureenonline');
    $facebook = get_option('lureen_facebook_url', 'https://facebook.com/lureenonline');
    $whatsapp = get_option('lureen_whatsapp_url', 'https://wa.me/966XXXXXXXXX');
    $tiktok = get_option('lureen_tiktok_url', 'https://tiktok.com/@lureenonline');
    ?>
    <script>
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function($) {
            // Update social links from WordPress options
            var savedLinks = {
                instagram: '<?php echo esc_js($instagram); ?>',
                facebook: '<?php echo esc_js($facebook); ?>',
                whatsapp: '<?php echo esc_js($whatsapp); ?>',
                tiktok: '<?php echo esc_js($tiktok); ?>'
            };
            
            $('.social-instagram').attr('href', savedLinks.instagram);
            $('.social-facebook').attr('href', savedLinks.facebook);
            $('.social-whatsapp').attr('href', savedLinks.whatsapp);
            $('.social-tiktok').attr('href', savedLinks.tiktok);
        });
    }
    </script>
    <?php
}, 103);
