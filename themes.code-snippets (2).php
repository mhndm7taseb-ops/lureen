<?php

/**
 * themes
 */
/**
 * Lureen Advanced Theme Customizer v2.1 - Production Ready
 * 
 * FIXES:
 * - Gradients now apply to ALL buttons, cart, and UI elements
 * - Single product description now displays properly
 * - Related products buttons fully styled
 * - Store name and website title customization added
 * - Fixed animation system (حركات العناصر)
 * - Enhanced mobile responsiveness
 * - Better RTL support
 * 
 * Installation: Replace existing themes snippet in Code Snippets Pro
 * Version: 2.1.0
 * Requires: WooCommerce, Lureen Core Snippets
 */

if (!defined('ABSPATH')) exit;

class Lureen_Theme_Customizer_V2 {
    
    private $option_name = 'lureen_theme_settings_v2';
    private $themes = [];
    private $animations = [];
    private $current_settings = [];
    
    public function __construct() {
        $this->init_themes();
        $this->init_animations();
        $this->current_settings = $this->get_settings();
        
        // Admin
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        
        // Frontend
        add_action('wp_head', [$this, 'inject_theme_styles'], 999);
        add_action('wp_footer', [$this, 'inject_theme_scripts'], 999);
        
        // Text replacement
        add_filter('the_content', [$this, 'replace_text_labels'], 999);
        add_filter('woocommerce_product_add_to_cart_text', [$this, 'replace_button_text'], 999);
        
        // Site title/name customization
        add_filter('pre_get_document_title', [$this, 'custom_document_title'], 999);
        add_filter('bloginfo', [$this, 'custom_bloginfo'], 999, 2);
        
        // Single product description
        add_action('woocommerce_after_single_product_summary', [$this, 'render_product_description'], 5);
    }
    
    /* =====================================================
       THEME DEFINITIONS - 30+ PROFESSIONAL THEMES
    ===================================================== */
    
    private function init_themes() {
        $this->themes = [
            // ORIGINAL & CLASSICS
            'original' => [
                'name' => 'لورين الأصلي',
                'audience' => 'نسائي كلاسيكي',
                'primary' => '#9b59b6',
                'primary_dark' => '#8e44ad',
                'secondary' => '#e74c3c',
                'secondary_dark' => '#c0392b',
                'accent' => '#2ecc71',
                'accent_dark' => '#27ae60',
                'text' => '#2c3e50',
                'bg' => '#fafafa',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            // FEMININE THEMES
            'rose_gold' => [
                'name' => 'الذهبي الوردي',
                'audience' => 'نسائي فاخر',
                'primary' => '#ec4899',
                'primary_dark' => '#db2777',
                'secondary' => '#f59e0b',
                'secondary_dark' => '#d97706',
                'accent' => '#8b5cf6',
                'accent_dark' => '#7c3aed',
                'text' => '#831843',
                'bg' => '#fef2f2',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'coral_sunset' => [
                'name' => 'غروب المرجان',
                'audience' => 'نسائي شبابي',
                'primary' => '#f43f5e',
                'primary_dark' => '#e11d48',
                'secondary' => '#fb923c',
                'secondary_dark' => '#f97316',
                'accent' => '#fbbf24',
                'accent_dark' => '#f59e0b',
                'text' => '#881337',
                'bg' => '#fff7ed',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'lavender_dream' => [
                'name' => 'حلم اللافندر',
                'audience' => 'نسائي هادئ',
                'primary' => '#a78bfa',
                'primary_dark' => '#8b5cf6',
                'secondary' => '#c084fc',
                'secondary_dark' => '#a855f7',
                'accent' => '#f472b6',
                'accent_dark' => '#ec4899',
                'text' => '#581c87',
                'bg' => '#faf5ff',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'mint_cream' => [
                'name' => 'النعناع الكريمي',
                'audience' => 'نسائي ناعم',
                'primary' => '#10b981',
                'primary_dark' => '#059669',
                'secondary' => '#34d399',
                'secondary_dark' => '#10b981',
                'accent' => '#6ee7b7',
                'accent_dark' => '#34d399',
                'text' => '#065f46',
                'bg' => '#f0fdf4',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'cherry_blossom' => [
                'name' => 'زهر الكرز',
                'audience' => 'نسائي رومانسي',
                'primary' => '#fda4af',
                'primary_dark' => '#fb7185',
                'secondary' => '#fdba74',
                'secondary_dark' => '#fb923c',
                'accent' => '#c084fc',
                'accent_dark' => '#a855f7',
                'text' => '#9f1239',
                'bg' => '#fff1f2',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            // MASCULINE THEMES
            'royal_blue' => [
                'name' => 'الأزرق الملكي',
                'audience' => 'رجالي أنيق',
                'primary' => '#2563eb',
                'primary_dark' => '#1d4ed8',
                'secondary' => '#0891b2',
                'secondary_dark' => '#0e7490',
                'accent' => '#10b981',
                'accent_dark' => '#059669',
                'text' => '#1e293b',
                'bg' => '#f8fafc',
                'card_bg' => '#ffffff',
                'button_style' => 'square'
            ],
            
            'midnight_black' => [
                'name' => 'الأسود الفاخر',
                'audience' => 'رجالي فاخر',
                'primary' => '#18181b',
                'primary_dark' => '#09090b',
                'secondary' => '#d4af37',
                'secondary_dark' => '#b8941f',
                'accent' => '#eab308',
                'accent_dark' => '#ca8a04',
                'text' => '#09090b',
                'bg' => '#fafaf9',
                'card_bg' => '#ffffff',
                'button_style' => 'square'
            ],
            
            'navy_gold' => [
                'name' => 'الكحلي الذهبي',
                'audience' => 'رجالي كلاسيكي',
                'primary' => '#1e3a8a',
                'primary_dark' => '#1e40af',
                'secondary' => '#d4af37',
                'secondary_dark' => '#b8941f',
                'accent' => '#0ea5e9',
                'accent_dark' => '#0284c7',
                'text' => '#0c4a6e',
                'bg' => '#f0f9ff',
                'card_bg' => '#ffffff',
                'button_style' => 'square'
            ],
            
            'ocean_deep' => [
                'name' => 'أعماق المحيط',
                'audience' => 'رجالي هادئ',
                'primary' => '#0c4a6e',
                'primary_dark' => '#075985',
                'secondary' => '#0284c7',
                'secondary_dark' => '#0369a1',
                'accent' => '#38bdf8',
                'accent_dark' => '#0ea5e9',
                'text' => '#082f49',
                'bg' => '#f0f9ff',
                'card_bg' => '#ffffff',
                'button_style' => 'square'
            ],
            
            'forest_green' => [
                'name' => 'الغابة الخضراء',
                'audience' => 'رجالي طبيعي',
                'primary' => '#15803d',
                'primary_dark' => '#166534',
                'secondary' => '#65a30d',
                'secondary_dark' => '#4d7c0f',
                'accent' => '#eab308',
                'accent_dark' => '#ca8a04',
                'text' => '#14532d',
                'bg' => '#f7fee7',
                'card_bg' => '#ffffff',
                'button_style' => 'square'
            ],
            
            // NEUTRAL THEMES
            'slate_minimal' => [
                'name' => 'الرمادي البسيط',
                'audience' => 'محايد عصري',
                'primary' => '#475569',
                'primary_dark' => '#334155',
                'secondary' => '#64748b',
                'secondary_dark' => '#475569',
                'accent' => '#0ea5e9',
                'accent_dark' => '#0284c7',
                'text' => '#1e293b',
                'bg' => '#f8fafc',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'emerald_green' => [
                'name' => 'الأخضر الزمردي',
                'audience' => 'محايد طبيعي',
                'primary' => '#059669',
                'primary_dark' => '#047857',
                'secondary' => '#0d9488',
                'secondary_dark' => '#0f766e',
                'accent' => '#84cc16',
                'accent_dark' => '#65a30d',
                'text' => '#064e3b',
                'bg' => '#f0fdf4',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'turquoise_fresh' => [
                'name' => 'الفيروزي المنعش',
                'audience' => 'محايد شبابي',
                'primary' => '#06b6d4',
                'primary_dark' => '#0891b2',
                'secondary' => '#14b8a6',
                'secondary_dark' => '#0d9488',
                'accent' => '#22d3ee',
                'accent_dark' => '#06b6d4',
                'text' => '#164e63',
                'bg' => '#ecfeff',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'amber_warmth' => [
                'name' => 'العنبر الدافئ',
                'audience' => 'محايد دافئ',
                'primary' => '#f59e0b',
                'primary_dark' => '#d97706',
                'secondary' => '#fb923c',
                'secondary_dark' => '#f97316',
                'accent' => '#fbbf24',
                'accent_dark' => '#f59e0b',
                'text' => '#78350f',
                'bg' => '#fffbeb',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            // LUXURY THEMES
            'crimson_luxury' => [
                'name' => 'القرمزي الفاخر',
                'audience' => 'فاخر محايد',
                'primary' => '#991b1b',
                'primary_dark' => '#7f1d1d',
                'secondary' => '#b91c1c',
                'secondary_dark' => '#991b1b',
                'accent' => '#d4af37',
                'accent_dark' => '#b8941f',
                'text' => '#450a0a',
                'bg' => '#fef2f2',
                'card_bg' => '#ffffff',
                'button_style' => 'square'
            ],
            
            'burgundy_gold' => [
                'name' => 'العنابي الذهبي',
                'audience' => 'فاخر كلاسيكي',
                'primary' => '#881337',
                'primary_dark' => '#4c0519',
                'secondary' => '#d4af37',
                'secondary_dark' => '#b8941f',
                'accent' => '#be123c',
                'accent_dark' => '#9f1239',
                'text' => '#450a0a',
                'bg' => '#fff1f2',
                'card_bg' => '#ffffff',
                'button_style' => 'square'
            ],
            
            'platinum_silver' => [
                'name' => 'البلاتين الفضي',
                'audience' => 'فاخر عصري',
                'primary' => '#71717a',
                'primary_dark' => '#52525b',
                'secondary' => '#a1a1aa',
                'secondary_dark' => '#71717a',
                'accent' => '#06b6d4',
                'accent_dark' => '#0891b2',
                'text' => '#27272a',
                'bg' => '#fafafa',
                'card_bg' => '#ffffff',
                'button_style' => 'square'
            ],
            
            // VIBRANT THEMES
            'electric_blue' => [
                'name' => 'الأزرق الكهربائي',
                'audience' => 'حيوي جريء',
                'primary' => '#3b82f6',
                'primary_dark' => '#2563eb',
                'secondary' => '#06b6d4',
                'secondary_dark' => '#0891b2',
                'accent' => '#a855f7',
                'accent_dark' => '#9333ea',
                'text' => '#1e3a8a',
                'bg' => '#eff6ff',
                'card_bg' => '#ffffff',
                'button_style' => 'pill'
            ],
            
            'neon_pink' => [
                'name' => 'الوردي النيون',
                'audience' => 'حيوي نسائي',
                'primary' => '#ec4899',
                'primary_dark' => '#db2777',
                'secondary' => '#8b5cf6',
                'secondary_dark' => '#7c3aed',
                'accent' => '#06b6d4',
                'accent_dark' => '#0891b2',
                'text' => '#831843',
                'bg' => '#fdf2f8',
                'card_bg' => '#ffffff',
                'button_style' => 'pill'
            ],
            
            'lime_green' => [
                'name' => 'الأخضر الليموني',
                'audience' => 'حيوي منعش',
                'primary' => '#84cc16',
                'primary_dark' => '#65a30d',
                'secondary' => '#22c55e',
                'secondary_dark' => '#16a34a',
                'accent' => '#eab308',
                'accent_dark' => '#ca8a04',
                'text' => '#365314',
                'bg' => '#f7fee7',
                'card_bg' => '#ffffff',
                'button_style' => 'pill'
            ],
            
            // PASTEL THEMES
            'soft_peach' => [
                'name' => 'الخوخي الناعم',
                'audience' => 'باستيل رقيق',
                'primary' => '#fb923c',
                'primary_dark' => '#f97316',
                'secondary' => '#fbbf24',
                'secondary_dark' => '#f59e0b',
                'accent' => '#f472b6',
                'accent_dark' => '#ec4899',
                'text' => '#7c2d12',
                'bg' => '#fff7ed',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'baby_blue' => [
                'name' => 'الأزرق الفاتح',
                'audience' => 'باستيل هادئ',
                'primary' => '#60a5fa',
                'primary_dark' => '#3b82f6',
                'secondary' => '#7dd3fc',
                'secondary_dark' => '#38bdf8',
                'accent' => '#c084fc',
                'accent_dark' => '#a855f7',
                'text' => '#1e3a8a',
                'bg' => '#eff6ff',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'lilac_purple' => [
                'name' => 'البنفسجي الليلكي',
                'audience' => 'باستيل رومانسي',
                'primary' => '#c084fc',
                'primary_dark' => '#a855f7',
                'secondary' => '#f0abfc',
                'secondary_dark' => '#e879f9',
                'accent' => '#fda4af',
                'accent_dark' => '#fb7185',
                'text' => '#581c87',
                'bg' => '#faf5ff',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            // SEASONAL THEMES
            'autumn_harvest' => [
                'name' => 'حصاد الخريف',
                'audience' => 'موسمي دافئ',
                'primary' => '#ea580c',
                'primary_dark' => '#c2410c',
                'secondary' => '#f59e0b',
                'secondary_dark' => '#d97706',
                'accent' => '#dc2626',
                'accent_dark' => '#b91c1c',
                'text' => '#7c2d12',
                'bg' => '#fff7ed',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'winter_frost' => [
                'name' => 'صقيع الشتاء',
                'audience' => 'موسمي بارد',
                'primary' => '#0ea5e9',
                'primary_dark' => '#0284c7',
                'secondary' => '#06b6d4',
                'secondary_dark' => '#0891b2',
                'accent' => '#a855f7',
                'accent_dark' => '#9333ea',
                'text' => '#0c4a6e',
                'bg' => '#f0f9ff',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'spring_bloom' => [
                'name' => 'إزهار الربيع',
                'audience' => 'موسمي منعش',
                'primary' => '#10b981',
                'primary_dark' => '#059669',
                'secondary' => '#fbbf24',
                'secondary_dark' => '#f59e0b',
                'accent' => '#f472b6',
                'accent_dark' => '#ec4899',
                'text' => '#065f46',
                'bg' => '#f0fdf4',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            'summer_sunshine' => [
                'name' => 'شمس الصيف',
                'audience' => 'موسمي مشرق',
                'primary' => '#facc15',
                'primary_dark' => '#eab308',
                'secondary' => '#fb923c',
                'secondary_dark' => '#f97316',
                'accent' => '#06b6d4',
                'accent_dark' => '#0891b2',
                'text' => '#713f12',
                'bg' => '#fefce8',
                'card_bg' => '#ffffff',
                'button_style' => 'rounded'
            ],
            
            // TRENDY THEMES
            'modern_monochrome' => [
                'name' => 'المونوكروم العصري',
                'audience' => 'عصري بسيط',
                'primary' => '#18181b',
                'primary_dark' => '#09090b',
                'secondary' => '#52525b',
                'secondary_dark' => '#3f3f46',
                'accent' => '#a1a1aa',
                'accent_dark' => '#71717a',
                'text' => '#09090b',
                'bg' => '#fafafa',
                'card_bg' => '#ffffff',
                'button_style' => 'square'
            ],
            
            'cyberpunk_neon' => [
                'name' => 'النيون السيبربانك',
                'audience' => 'عصري جريء',
                'primary' => '#a855f7',
                'primary_dark' => '#9333ea',
                'secondary' => '#06b6d4',
                'secondary_dark' => '#0891b2',
                'accent' => '#f472b6',
                'accent_dark' => '#ec4899',
                'text' => '#581c87',
                'bg' => '#faf5ff',
                'card_bg' => '#ffffff',
                'button_style' => 'pill'
            ],
            
            'sunset_gradient' => [
                'name' => 'تدرج الغروب',
                'audience' => 'عصري دراماتيكي',
                'primary' => '#f97316',
                'primary_dark' => '#ea580c',
                'secondary' => '#ec4899',
                'secondary_dark' => '#db2777',
                'accent' => '#8b5cf6',
                'accent_dark' => '#7c3aed',
                'text' => '#7c2d12',
                'bg' => '#fff7ed',
                'card_bg' => '#ffffff',
                'button_style' => 'pill'
            ],
        ];
    }
    
    /* =====================================================
       ANIMATION DEFINITIONS - 15+ ANIMATION TYPES
    ===================================================== */
    
    private function init_animations() {
        $this->animations = [
            'cart_open' => [
                'none' => 'بدون حركة',
                'slide' => 'انزلاق (Slide)',
                'fade' => 'تلاشي (Fade)',
                'zoom' => 'تكبير (Zoom)',
                'bounce' => 'ارتداد (Bounce)',
                'flip' => 'انقلاب (Flip)',
                'rotate' => 'دوران (Rotate)',
                'scale' => 'تحجيم (Scale)',
            ],
            'cart_persistent' => [
                'none' => 'بدون حركة',
                'pulse' => 'نبض (Pulse)',
                'bounce' => 'ارتداد (Bounce)',
                'shake' => 'اهتزاز (Shake)',
                'swing' => 'تأرجح (Swing)',
                'tada' => 'تادا (Tada)',
                'heartbeat' => 'نبض قلب (Heartbeat)',
                'wobble' => 'تذبذب (Wobble)',
                'jello' => 'جيلو (Jello)',
            ],
            'button_hover' => [
                'none' => 'بدون حركة',
                'lift' => 'رفع (Lift)',
                'grow' => 'تكبير (Grow)',
                'shrink' => 'تصغير (Shrink)',
                'glow' => 'توهج (Glow)',
                'shadow' => 'ظل (Shadow)',
                'rotate' => 'دوران (Rotate)',
                'skew' => 'انحراف (Skew)',
            ],
            'product_card' => [
                'none' => 'بدون حركة',
                'lift' => 'رفع (Lift)',
                'zoom' => 'تكبير (Zoom)',
                'tilt' => 'ميلان (Tilt)',
                'shine' => 'لمعان (Shine)',
                'float' => 'طفو (Float)',
            ]
        ];
    }
    
    /* =====================================================
       SETTINGS & ADMIN
    ===================================================== */
    
    private function get_default_settings() {
        return [
            'theme' => 'original',
            'button_style' => 'rounded',
            'cart_open_animation' => 'slide',
            'cart_persistent_animation' => 'pulse',
            'button_hover_animation' => 'lift',
            'product_card_animation' => 'lift',
            'store_name' => 'متجر لورين',
            'website_title' => 'لورين اونلاين - متجرك سيدتي',
            'hero_title' => 'مجموعة ربيع 2025',
            'hero_subtitle' => 'تألقي بأحدث صيحات الموسم',
            'hero_cta' => 'تسوقي الآن',
            'view_options_text' => 'شوفي الخيارات',
            'add_to_cart_text' => 'أضف للسلة',
            'choose_bundle_text' => 'اختر حزمتك',
            'new_badge_text' => 'جديد',
            'sale_badge_text' => 'تخفيض',
            'gradient_logo' => true,
            'gradient_social_icons' => true,
            'gradient_buttons' => true,
            'gradient_sticky_cart' => true,
            'gradient_badges' => true,
            'gradient_section_titles' => true,
        ];
    }
    
    private function get_settings() {
        $defaults = $this->get_default_settings();
        $saved = get_option($this->option_name, []);
        return wp_parse_args($saved, $defaults);
    }
    
    public function add_settings_page() {
        add_menu_page(
            'إعدادات ثيم لورين',
            'ثيم لورين',
            'manage_options',
            'lureen-theme-customizer-v2',
            [$this, 'render_settings_page'],
            'dashicons-art',
            59
        );
    }
    
    public function register_settings() {
        register_setting('lureen_theme_settings_group_v2', $this->option_name);
    }
    
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'toplevel_page_lureen-theme-customizer-v2') return;
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
    
    public function render_settings_page() {
        if (isset($_POST['lureen_theme_submit'])) {
            check_admin_referer('lureen_theme_settings_v2');
            
            $settings = [
                'theme' => sanitize_text_field($_POST['theme'] ?? 'original'),
                'button_style' => sanitize_text_field($_POST['button_style'] ?? 'rounded'),
                'cart_open_animation' => sanitize_text_field($_POST['cart_open_animation'] ?? 'slide'),
                'cart_persistent_animation' => sanitize_text_field($_POST['cart_persistent_animation'] ?? 'pulse'),
                'button_hover_animation' => sanitize_text_field($_POST['button_hover_animation'] ?? 'lift'),
                'product_card_animation' => sanitize_text_field($_POST['product_card_animation'] ?? 'lift'),
                'store_name' => sanitize_text_field($_POST['store_name'] ?? 'متجر لورين'),
                'website_title' => sanitize_text_field($_POST['website_title'] ?? 'لورين اونلاين - متجرك سيدتي'),
                'hero_title' => sanitize_text_field($_POST['hero_title'] ?? ''),
                'hero_subtitle' => sanitize_text_field($_POST['hero_subtitle'] ?? ''),
                'hero_cta' => sanitize_text_field($_POST['hero_cta'] ?? ''),
                'view_options_text' => sanitize_text_field($_POST['view_options_text'] ?? ''),
                'add_to_cart_text' => sanitize_text_field($_POST['add_to_cart_text'] ?? ''),
                'choose_bundle_text' => sanitize_text_field($_POST['choose_bundle_text'] ?? ''),
                'new_badge_text' => sanitize_text_field($_POST['new_badge_text'] ?? ''),
                'sale_badge_text' => sanitize_text_field($_POST['sale_badge_text'] ?? ''),
                'gradient_logo' => isset($_POST['gradient_logo']),
                'gradient_social_icons' => isset($_POST['gradient_social_icons']),
                'gradient_buttons' => isset($_POST['gradient_buttons']),
                'gradient_sticky_cart' => isset($_POST['gradient_sticky_cart']),
                'gradient_badges' => isset($_POST['gradient_badges']),
                'gradient_section_titles' => isset($_POST['gradient_section_titles']),
            ];
            
            update_option($this->option_name, $settings);
            $this->current_settings = $settings;
            
            echo '<div class="notice notice-success is-dismissible"><p>✅ تم حفظ الإعدادات بنجاح!</p></div>';
        }
        
        $settings = $this->current_settings;
        ?>
        <div class="wrap lureen-theme-admin" dir="rtl">
            <h1>🎨 إعدادات ثيم لورين المتقدمة v2.1</h1>
            <p class="description">اختاري الثيم والألوان والحركات المناسبة لمتجرك. جميع التغييرات تطبق تلقائياً على كامل الموقع.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('lureen_theme_settings_v2'); ?>
                
                <div class="lureen-admin-grid">
                    <!-- Theme Selection -->
                    <div class="lureen-admin-card full-width">
                        <h2>🎭 اختيار الثيم (30+ ثيم احترافي)</h2>
                        <div class="lureen-theme-grid">
                            <?php foreach ($this->themes as $key => $theme): ?>
                                <label class="lureen-theme-option <?php echo $settings['theme'] === $key ? 'active' : ''; ?>">
                                    <input type="radio" name="theme" value="<?php echo esc_attr($key); ?>" <?php checked($settings['theme'], $key); ?> />
                                    <div class="theme-preview">
                                        <div class="theme-colors">
                                            <span style="background: <?php echo esc_attr($theme['primary']); ?>"></span>
                                            <span style="background: <?php echo esc_attr($theme['secondary']); ?>"></span>
                                            <span style="background: <?php echo esc_attr($theme['accent']); ?>"></span>
                                        </div>
                                        <div class="theme-info">
                                            <strong><?php echo esc_html($theme['name']); ?></strong>
                                            <span class="theme-audience"><?php echo esc_html($theme['audience']); ?></span>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Store Branding -->
                    <div class="lureen-admin-card">
                        <h2>🏪 معلومات المتجر</h2>
                        
                        <label>
                            <strong>اسم المتجر</strong>
                            <input type="text" name="store_name" value="<?php echo esc_attr($settings['store_name']); ?>" class="regular-text" placeholder="متجر لورين" />
                            <p class="lureen-helper">يظهر في شريط التنقل وجميع أنحاء الموقع</p>
                        </label>
                        
                        <label>
                            <strong>عنوان الموقع</strong>
                            <input type="text" name="website_title" value="<?php echo esc_attr($settings['website_title']); ?>" class="regular-text" placeholder="لورين اونلاين - متجرك سيدتي" />
                            <p class="lureen-helper">يظهر في عنوان المتصفح ونتائج البحث</p>
                        </label>
                    </div>
                    
                    <!-- Gradient Options -->
                    <div class="lureen-admin-card">
                        <h2>🌈 تطبيق التدرجات اللونية</h2>
                        <p class="description">اختاري العناصر التي تريدين تطبيق التدرجات اللونية عليها</p>
                        <div class="lureen-checkbox-group">
                            <label>
                                <input type="checkbox" name="gradient_logo" <?php checked($settings['gradient_logo']); ?> />
                                <span>شعار المتجر</span>
                            </label>
                            <label>
                                <input type="checkbox" name="gradient_social_icons" <?php checked($settings['gradient_social_icons']); ?> />
                                <span>أيقونات التواصل الاجتماعي</span>
                            </label>
                            <label>
                                <input type="checkbox" name="gradient_buttons" <?php checked($settings['gradient_buttons']); ?> />
                                <span>الأزرار</span>
                            </label>
                            <label>
                                <input type="checkbox" name="gradient_sticky_cart" <?php checked($settings['gradient_sticky_cart']); ?> />
                                <span>السلة العائمة</span>
                            </label>
                            <label>
                                <input type="checkbox" name="gradient_badges" <?php checked($settings['gradient_badges']); ?> />
                                <span>شارات المنتجات (جديد/تخفيض)</span>
                            </label>
                            <label>
                                <input type="checkbox" name="gradient_section_titles" <?php checked($settings['gradient_section_titles']); ?> />
                                <span>عناوين الأقسام</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Button Style -->
                    <div class="lureen-admin-card">
                        <h2>🔘 شكل الأزرار</h2>
                        <div class="lureen-radio-group">
                            <label>
                                <input type="radio" name="button_style" value="rounded" <?php checked($settings['button_style'], 'rounded'); ?> />
                                <span>دائري (Rounded)</span>
                            </label>
                            <label>
                                <input type="radio" name="button_style" value="square" <?php checked($settings['button_style'], 'square'); ?> />
                                <span>مربع (Square)</span>
                            </label>
                            <label>
                                <input type="radio" name="button_style" value="pill" <?php checked($settings['button_style'], 'pill'); ?> />
                                <span>حبة دواء (Pill)</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Animations -->
                    <div class="lureen-admin-card">
                        <h2>🎬 حركات السلة</h2>
                        
                        <h3>حركة فتح السلة</h3>
                        <select name="cart_open_animation" class="regular-text">
                            <?php foreach ($this->animations['cart_open'] as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($settings['cart_open_animation'], $value); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <h3>حركة السلة المستمرة</h3>
                        <select name="cart_persistent_animation" class="regular-text">
                            <?php foreach ($this->animations['cart_persistent'] as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($settings['cart_persistent_animation'], $value); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="lureen-admin-card">
                        <h2>✨ حركات العناصر</h2>
                        
                        <h3>حركة الأزرار عند التمرير</h3>
                        <select name="button_hover_animation" class="regular-text">
                            <?php foreach ($this->animations['button_hover'] as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($settings['button_hover_animation'], $value); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <h3>حركة كروت المنتجات</h3>
                        <select name="product_card_animation" class="regular-text">
                            <?php foreach ($this->animations['product_card'] as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($settings['product_card_animation'], $value); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Hero Section -->
                    <div class="lureen-admin-card">
                        <h2>🌟 قسم البطل (Hero)</h2>
                        
                        <label>
                            <strong>العنوان الرئيسي</strong>
                            <input type="text" name="hero_title" value="<?php echo esc_attr($settings['hero_title']); ?>" class="regular-text" />
                        </label>
                        
                        <label>
                            <strong>العنوان الفرعي</strong>
                            <input type="text" name="hero_subtitle" value="<?php echo esc_attr($settings['hero_subtitle']); ?>" class="regular-text" />
                        </label>
                        
                        <label>
                            <strong>نص الزر</strong>
                            <input type="text" name="hero_cta" value="<?php echo esc_attr($settings['hero_cta']); ?>" class="regular-text" />
                        </label>
                    </div>
                    
                    <!-- Text Labels -->
                    <div class="lureen-admin-card">
                        <h2>📝 النصوص المخصصة</h2>
                        
                        <label>
                            <strong>نص "شوفي الخيارات"</strong>
                            <input type="text" name="view_options_text" value="<?php echo esc_attr($settings['view_options_text']); ?>" class="regular-text" />
                        </label>
                        
                        <label>
                            <strong>نص "أضف للسلة"</strong>
                            <input type="text" name="add_to_cart_text" value="<?php echo esc_attr($settings['add_to_cart_text']); ?>" class="regular-text" />
                        </label>
                        
                        <label>
                            <strong>نص "اختر حزمتك" (للحزم)</strong>
                            <input type="text" name="choose_bundle_text" value="<?php echo esc_attr($settings['choose_bundle_text']); ?>" class="regular-text" />
                        </label>
                        
                        <label>
                            <strong>شارة "جديد"</strong>
                            <input type="text" name="new_badge_text" value="<?php echo esc_attr($settings['new_badge_text']); ?>" class="regular-text" />
                        </label>
                        
                        <label>
                            <strong>شارة "تخفيض"</strong>
                            <input type="text" name="sale_badge_text" value="<?php echo esc_attr($settings['sale_badge_text']); ?>" class="regular-text" />
                        </label>
                    </div>
                </div>
                
                <p class="submit">
                    <button type="submit" name="lureen_theme_submit" class="button button-primary button-large">
                        💾 حفظ جميع التغييرات
                    </button>
                </p>
            </form>
        </div>
        
        <style>
        .lureen-theme-admin {
            background: #f0f0f1;
            margin: 20px 0 0 20px;
            padding: 20px;
            border-radius: 8px;
        }
        
        .lureen-theme-admin h1 {
            color: #1d2327;
            font-size: 28px;
            margin-bottom: 8px;
        }
        
        .lureen-theme-admin > .description {
            font-size: 15px;
            color: #646970;
            margin-bottom: 24px;
        }
        
        .lureen-admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .lureen-admin-card {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .lureen-admin-card.full-width {
            grid-column: 1 / -1;
        }
        
        .lureen-admin-card h2 {
            margin: 0 0 20px;
            font-size: 20px;
            color: #1d2327;
            border-bottom: 2px solid #f0f0f1;
            padding-bottom: 12px;
        }
        
        .lureen-admin-card h3 {
            margin: 20px 0 8px;
            font-size: 15px;
            color: #2c3338;
        }
        
        .lureen-admin-card label {
            display: block;
            margin-bottom: 16px;
        }
        
        .lureen-admin-card label strong {
            display: block;
            margin-bottom: 6px;
            color: #1d2327;
        }
        
        .lureen-admin-card input[type="text"],
        .lureen-admin-card select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .lureen-theme-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
        }
        
        .lureen-theme-option {
            cursor: pointer;
            border: 2px solid #dcdcde;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.2s;
            display: block;
        }
        
        .lureen-theme-option:hover {
            border-color: #2271b1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .lureen-theme-option.active {
            border-color: #2271b1;
            background: #f0f6fc;
        }
        
        .lureen-theme-option input[type="radio"] {
            display: none;
        }
        
        .theme-preview {
            text-align: center;
        }
        
        .theme-colors {
            display: flex;
            gap: 4px;
            margin-bottom: 12px;
            justify-content: center;
        }
        
        .theme-colors span {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .theme-info strong {
            display: block;
            font-size: 13px;
            color: #1d2327;
            margin-bottom: 4px;
        }
        
        .theme-audience {
            display: inline-block;
            font-size: 11px;
            color: #646970;
            background: #f0f0f1;
            padding: 2px 8px;
            border-radius: 12px;
        }
        
        .lureen-radio-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            cursor: pointer;
        }
        
        .lureen-radio-group input[type="radio"] {
            margin: 0;
        }
        
        .lureen-checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .lureen-checkbox-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .lureen-checkbox-group input[type="checkbox"] {
            margin: 0;
        }
        
        .lureen-helper {
            font-size: 13px;
            color: #7f8c8d;
            margin-top: 4px;
        }
        
        .button-large {
            font-size: 16px !important;
            padding: 12px 24px !important;
            height: auto !important;
        }
        </style>
        <?php
    }
    
    /* =====================================================
       FRONTEND THEME INJECTION
    ===================================================== */
    
    public function inject_theme_styles() {
        $settings = $this->current_settings;
        $theme = $this->themes[$settings['theme']] ?? $this->themes['original'];
        
        // Generate CSS variables
        $css = ":root {\n";
        $css .= "  --lureen-primary: {$theme['primary']};\n";
        $css .= "  --lureen-primary-dark: {$theme['primary_dark']};\n";
        $css .= "  --lureen-secondary: {$theme['secondary']};\n";
        $css .= "  --lureen-secondary-dark: {$theme['secondary_dark']};\n";
        $css .= "  --lureen-accent: {$theme['accent']};\n";
        $css .= "  --lureen-accent-dark: {$theme['accent_dark']};\n";
        $css .= "  --lureen-text: {$theme['text']};\n";
        $css .= "  --lureen-bg: {$theme['bg']};\n";
        $css .= "  --lureen-card-bg: {$theme['card_bg']};\n";
        $css .= "  --lureen-gradient: linear-gradient(135deg, {$theme['primary']}, {$theme['primary_dark']});\n";
        $css .= "  --lureen-gradient-hero: linear-gradient(135deg, {$theme['primary']}, {$theme['secondary']});\n";
        $css .= "  --lureen-gradient-accent: linear-gradient(135deg, {$theme['accent']}, {$theme['accent_dark']});\n";
        $css .= "}\n\n";
        
        // Apply theme colors to all elements
        $css .= $this->generate_theme_css($theme, $settings);
        
        // Add WooCommerce single product page styles
        $css .= $this->generate_woocommerce_single_styles($theme, $settings);
        
        // Add animation styles (FIXED)
        $css .= $this->generate_animation_styles($settings);
        
        echo "<style id='lureen-theme-customizer-v2'>\n{$css}</style>\n";
    }
    
    private function generate_theme_css($theme, $settings) {
        $button_radius = $settings['button_style'] === 'square' ? '8px' : ($settings['button_style'] === 'pill' ? '50px' : '40px');
        
        // Determine if gradients should be applied
        $logo_bg = $settings['gradient_logo'] ? "linear-gradient(135deg, {$theme['primary']}, {$theme['secondary']})" : $theme['primary'];
        $button_bg = $settings['gradient_buttons'] ? "linear-gradient(135deg, {$theme['primary']}, {$theme['primary_dark']})" : $theme['primary'];
        $sticky_cart_bg = $settings['gradient_sticky_cart'] ? "linear-gradient(135deg, {$theme['primary']}, {$theme['primary_dark']})" : $theme['primary'];
        $social_icon_bg = $settings['gradient_social_icons'] ? "linear-gradient(135deg, {$theme['primary']}, {$theme['secondary']})" : $theme['primary'];
        $badge_new_bg = $settings['gradient_badges'] ? "linear-gradient(135deg, {$theme['accent']}, {$theme['accent_dark']})" : $theme['accent'];
        $badge_sale_bg = $settings['gradient_badges'] ? "linear-gradient(135deg, {$theme['secondary']}, {$theme['secondary_dark']})" : $theme['secondary'];
        
        return <<<CSS
/* =====================================================
   LUREEN THEME CUSTOMIZER V2.1 - GLOBAL OVERRIDES
===================================================== */

/* Body & Background */
html, body {
    background: {$theme['bg']} !important;
    color: {$theme['text']} !important;
}

/* Navigation Bar */
.lureen-custom-logo {
    background: {$logo_bg} !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
}

.lureen-nav-menu li a {
    color: {$theme['text']} !important;
}

.lureen-nav-menu li a:hover,
.lureen-nav-menu li a.active {
    color: {$theme['primary']} !important;
}

.lureen-nav-menu li a::after {
    background: {$button_bg} !important;
}

.lureen-mobile-toggle {
    color: {$theme['primary']} !important;
}

/* Social Media Icons */
.lureen-social-icons .social-instagram,
.lureen-social-icons .social-facebook,
.lureen-social-icons .social-whatsapp,
.lureen-social-icons .social-tiktok {
    background: {$social_icon_bg} !important;
}

.lureen-social-icons .social-instagram:hover,
.lureen-social-icons .social-facebook:hover,
.lureen-social-icons .social-whatsapp:hover,
.lureen-social-icons .social-tiktok:hover {
    background: linear-gradient(135deg, {$theme['primary_dark']}, {$theme['secondary_dark']}) !important;
    box-shadow: 0 6px 15px rgba(0,0,0,0.3) !important;
}

/* Hero Section */
.lureen-hero {
    background: linear-gradient(135deg, {$theme['primary']}, {$theme['secondary']}) !important;
}

.lureen-cta-btn {
    background: {$theme['card_bg']} !important;
    color: {$theme['primary']} !important;
    border-radius: {$button_radius} !important;
}

.lureen-cta-btn:hover {
    background: {$theme['primary']} !important;
    color: {$theme['card_bg']} !important;
}

/* Section Titles */
.lureen-section-title::after {
    background: {$button_bg} !important;
}

/* Category Cards */
.lureen-category-card {
    background: {$theme['card_bg']} !important;
}

.lureen-category-name {
    color: {$theme['text']} !important;
}

.lureen-category-count {
    color: {$theme['primary']} !important;
}

/* Product Cards */
.lureen-product-card {
    background: {$theme['card_bg']} !important;
}

.lureen-product-name,
.lureen-product-name a {
    color: {$theme['text']} !important;
}

.lureen-product-badge.new {
    background: {$badge_new_bg} !important;
}

.lureen-product-badge.sale {
    background: {$badge_sale_bg} !important;
}

.lureen-price-normal,
.lureen-price-sale {
    color: {$theme['primary']} !important;
}

.lureen-price-regular {
    color: #999 !important;
}

/* =====================================================
   COMPREHENSIVE BUTTON STYLING - ALL TYPES
===================================================== */

/* Core Lureen Buttons */
.lureen-add-to-cart-btn,
.lureen-bundle-btn,
.lureen-bundle-btn.primary,
.lureen-load-more-btn,
.lureen-cta-btn,
.lureen-checkout-btn,
.lureen-add-btn,
button.lureen-add-to-cart-btn,
a.lureen-add-to-cart-btn,
a.lureen-bundle-cta,
a.lureen-bundle-btn {
    background: {$button_bg} !important;
    color: #fff !important;
    border-radius: {$button_radius} !important;
}

/* Added state */
.lureen-add-to-cart-btn.added {
    background: linear-gradient(135deg, {$theme['accent']}, {$theme['accent_dark']}) !important;
}

/* Secondary buttons */
.lureen-bundle-btn.secondary {
    background: #f0f0f5 !important;
    color: {$theme['text']} !important;
}

/* Cart buttons */
.lureen-view-cart {
    background: rgba({$this->hex_to_rgb($theme['primary'])}, 0.1) !important;
    color: {$theme['primary']} !important;
    border-radius: {$button_radius} !important;
}

/* Sticky Cart */
.lureen-sticky-cart {
    background: {$sticky_cart_bg} !important;
}

.lureen-cart-count {
    background: {$theme['secondary']} !important;
}

/* Cart Modal */
.lureen-cart-header {
    background: {$sticky_cart_bg} !important;
}

.lureen-cart-item-price {
    color: {$theme['primary']} !important;
}

.lureen-qty:hover {
    background: {$theme['primary']} !important;
    color: #fff !important;
}

.lureen-checkout-btn,
.lureen-add-btn {
    background: {$button_bg} !important;
    border-radius: {$button_radius} !important;
}

.lureen-view-cart {
    background: rgba({$this->hex_to_rgb($theme['primary'])}, 0.1) !important;
    color: {$theme['primary']} !important;
    border-radius: {$button_radius} !important;
}

/* Custom Cart Page */
.lureen-item-price {
    color: {$theme['primary']} !important;
}

.lureen-qty-btn:hover {
    background: {$theme['primary']} !important;
    color: #fff !important;
}

.lureen-continue-btn:hover {
    border-color: {$theme['primary']} !important;
    color: {$theme['primary']} !important;
}

/* Bundle Builder */
.lureen-bundle-title::after {
    background: {$button_bg} !important;
}

.lureen-bundle-product-card {
    background: {$theme['card_bg']} !important;
}

.lureen-bundle-product-name {
    color: {$theme['text']} !important;
}

.lureen-bundle-variations select:focus {
    border-color: {$theme['primary']} !important;
}

.lureen-sticky-helper {
    background: {$theme['card_bg']} !important;
}

.lureen-sticky-title {
    color: {$theme['text']} !important;
}

.lureen-sticky-progress {
    color: {$theme['primary']} !important;
}

/* Filters */
.lureen-ordering select.orderby:hover,
.lureen-ordering select.orderby:focus {
    border-color: {$theme['primary']} !important;
}

.lureen-search-form input[type="search"]:focus {
    border-color: {$theme['primary']} !important;
}

.lureen-search-form button {
    color: {$theme['primary']} !important;
}

/* Scrollbar */
.lureen-cart-items::-webkit-scrollbar-thumb {
    background: {$theme['primary']} !important;
}

CSS;
    }
    
    private function generate_woocommerce_single_styles($theme, $settings) {
        $button_radius = $settings['button_style'] === 'square' ? '8px' : ($settings['button_style'] === 'pill' ? '50px' : '40px');
        $button_bg = $settings['gradient_buttons'] ? "linear-gradient(135deg, {$theme['primary']}, {$theme['primary_dark']})" : $theme['primary'];
        $badge_new_bg = $settings['gradient_badges'] ? "linear-gradient(135deg, {$theme['accent']}, {$theme['accent_dark']})" : $theme['accent'];
        $badge_sale_bg = $settings['gradient_badges'] ? "linear-gradient(135deg, {$theme['secondary']}, {$theme['secondary_dark']})" : $theme['secondary'];
        
        return <<<CSS

/* =====================================================
   WOOCOMMERCE SINGLE PRODUCT PAGE STYLING
===================================================== */

/* Hide duplicate WooCommerce tabs */
.woocommerce-tabs.wc-tabs-wrapper,
.woocommerce div.product .woocommerce-tabs {
    display: none !important;
}

/* Product Title */
.single-product .product_title,
.woocommerce div.product .product_title {
    color: {$theme['text']} !important;
    font-weight: 800 !important;
}

/* Product Price */
.single-product .price,
.woocommerce div.product p.price,
.woocommerce div.product span.price {
    color: {$theme['primary']} !important;
    font-weight: 800 !important;
}

.single-product .price del,
.woocommerce div.product p.price del,
.woocommerce div.product span.price del {
    color: #999 !important;
    opacity: 0.7 !important;
}

.single-product .price ins,
.woocommerce div.product p.price ins,
.woocommerce div.product span.price ins {
    color: {$theme['secondary']} !important;
    text-decoration: none !important;
}

/* Product Badges */
.single-product .onsale,
.woocommerce span.onsale {
    background: {$badge_sale_bg} !important;
    color: #fff !important;
    font-weight: 800 !important;
    border-radius: 8px !important;
}

/* =====================================================
   COMPREHENSIVE ADD TO CART BUTTON STYLING
===================================================== */

/* All WooCommerce Add to Cart Buttons */
.single-product .single_add_to_cart_button,
.woocommerce #respond input#submit.alt,
.woocommerce a.button.alt,
.woocommerce button.button.alt,
.woocommerce input.button.alt,
.woocommerce #respond input#submit,
.woocommerce a.button,
.woocommerce button.button,
.woocommerce input.button,
.single_add_to_cart_button,
button.single_add_to_cart_button,
.woocommerce-variation-add-to-cart button,
form.cart button[type="submit"],
form.cart .single_add_to_cart_button {
    background: {$button_bg} !important;
    color: #fff !important;
    border: none !important;
    border-radius: {$button_radius} !important;
    font-weight: 800 !important;
    padding: 14px 32px !important;
    font-size: 16px !important;
    transition: transform 0.25s ease, opacity 0.25s ease !important;
}

.single-product .single_add_to_cart_button:hover,
.woocommerce #respond input#submit:hover,
.woocommerce a.button:hover,
.woocommerce button.button:hover,
.woocommerce input.button:hover {
    transform: translateY(-2px) !important;
    opacity: 0.95 !important;
}

/* Quantity Input */
.single-product .quantity input.qty,
.woocommerce div.product form.cart .quantity input.qty,
.woocommerce .quantity input.qty {
    border: 2px solid #e0e0e0 !important;
    border-radius: 12px !important;
    padding: 10px !important;
    font-weight: 700 !important;
    color: {$theme['text']} !important;
}

.single-product .quantity input.qty:focus,
.woocommerce div.product form.cart .quantity input.qty:focus {
    border-color: {$theme['primary']} !important;
    outline: none !important;
}

/* Variation Dropdowns */
.single-product .variations select,
.woocommerce div.product form.cart .variations select {
    border: 2px solid #e0e0e0 !important;
    border-radius: 12px !important;
    padding: 10px 14px !important;
    font-weight: 600 !important;
    color: {$theme['text']} !important;
}

.single-product .variations select:focus,
.woocommerce div.product form.cart .variations select:focus {
    border-color: {$theme['primary']} !important;
    outline: none !important;
}

/* Product Meta */
.single-product .product_meta,
.woocommerce div.product .product_meta {
    color: {$theme['text']} !important;
    border-top: 2px solid #f0f0f0 !important;
    padding-top: 16px !important;
}

.single-product .product_meta a,
.woocommerce div.product .product_meta a {
    color: {$theme['primary']} !important;
    font-weight: 600 !important;
}

.single-product .product_meta a:hover,
.woocommerce div.product .product_meta a:hover {
    color: {$theme['primary_dark']} !important;
}

/* Product Description Section (Custom Rendered) */
.lureen-product-description {
    max-width: 1200px;
    margin: 40px auto;
    padding: 30px;
    background: {$theme['card_bg']};
    border-radius: 20px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.08);
}

.lureen-product-description h2 {
    text-align: center;
    font-size: 28px;
    font-weight: 800;
    color: {$theme['text']};
    margin: 0 0 24px;
    position: relative;
    padding-bottom: 12px;
}

.lureen-product-description h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 84px;
    height: 4px;
    background: {$button_bg};
}

.lureen-product-description-content {
    color: {$theme['text']};
    line-height: 1.8;
    font-size: 16px;
}

.lureen-product-description-content p {
    margin-bottom: 16px;
}

.lureen-product-description-content ul,
.lureen-product-description-content ol {
    margin: 16px 0;
    padding-right: 24px;
}

.lureen-product-description-content li {
    margin-bottom: 8px;
}

/* Product Reviews */
.woocommerce #reviews #comments ol.commentlist li {
    background: #f8f9fa !important;
    border-radius: 12px !important;
    padding: 16px !important;
    margin-bottom: 12px !important;
}

.woocommerce #reviews #comments ol.commentlist li .star-rating {
    color: {$theme['accent']} !important;
}

.woocommerce #reviews #comments ol.commentlist li .meta strong {
    color: {$theme['text']} !important;
    font-weight: 800 !important;
}

/* Review Form */
.woocommerce #review_form #respond textarea,
.woocommerce #review_form #respond input[type="text"],
.woocommerce #review_form #respond input[type="email"] {
    border: 2px solid #e0e0e0 !important;
    border-radius: 12px !important;
    padding: 12px !important;
}

.woocommerce #review_form #respond textarea:focus,
.woocommerce #review_form #respond input:focus {
    border-color: {$theme['primary']} !important;
    outline: none !important;
}

/* Star Rating */
.woocommerce .star-rating {
    color: {$theme['accent']} !important;
}

.woocommerce .star-rating span {
    color: {$theme['accent']} !important;
}

/* =====================================================
   RELATED PRODUCTS SECTION - COMPREHENSIVE BUTTON FIX
===================================================== */

.related.products,
.upsells.products,
.woocommerce-page .related.products,
.woocommerce-page .upsells.products {
    margin-top: 48px !important;
}

.related.products > h2,
.upsells.products > h2,
.woocommerce-page .related.products > h2,
.woocommerce-page .upsells.products > h2 {
    text-align: center !important;
    font-size: 28px !important;
    font-weight: 800 !important;
    color: {$theme['text']} !important;
    margin-bottom: 32px !important;
    position: relative !important;
    padding-bottom: 12px !important;
}

.related.products > h2::after,
.upsells.products > h2::after,
.woocommerce-page .related.products > h2::after,
.woocommerce-page .upsells.products > h2::after {
    content: '' !important;
    position: absolute !important;
    bottom: 0 !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: 84px !important;
    height: 4px !important;
    background: {$button_bg} !important;
}

/* Related Products Grid */
.related.products ul.products,
.upsells.products ul.products,
.woocommerce-page .related.products ul.products,
.woocommerce-page .upsells.products ul.products {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)) !important;
    gap: 24px !important;
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.related.products ul.products li.product,
.upsells.products ul.products li.product,
.woocommerce-page .related.products ul.products li.product,
.woocommerce-page .upsells.products ul.products li.product {
    background: {$theme['card_bg']} !important;
    border-radius: 20px !important;
    overflow: hidden !important;
    box-shadow: 0 8px 28px rgba(0,0,0,0.08) !important;
    transition: transform 0.35s ease, box-shadow 0.35s ease !important;
    display: flex !important;
    flex-direction: column !important;
}

.related.products ul.products li.product:hover,
.upsells.products ul.products li.product:hover,
.woocommerce-page .related.products ul.products li.product:hover,
.woocommerce-page .upsells.products ul.products li.product:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 18px 44px rgba({$this->hex_to_rgb($theme['primary'])}, 0.28) !important;
}

.related.products ul.products li.product img,
.upsells.products ul.products li.product img,
.woocommerce-page .related.products ul.products li.product img,
.woocommerce-page .upsells.products ul.products li.product img {
    width: 100% !important;
    aspect-ratio: 1/1 !important;
    object-fit: cover !important;
}

.related.products ul.products li.product .woocommerce-loop-product__title,
.upsells.products ul.products li.product .woocommerce-loop-product__title,
.woocommerce-page .related.products ul.products li.product .woocommerce-loop-product__title,
.woocommerce-page .upsells.products ul.products li.product .woocommerce-loop-product__title,
.related.products ul.products li.product h2,
.upsells.products ul.products li.product h2,
.woocommerce-page .related.products ul.products li.product h2,
.woocommerce-page .upsells.products ul.products li.product h2 {
    font-size: 17px !important;
    font-weight: 800 !important;
    color: {$theme['text']} !important;
    padding: 14px 16px 8px !important;
    line-height: 1.35 !important;
}

.related.products ul.products li.product .price,
.upsells.products ul.products li.product .price,
.woocommerce-page .related.products ul.products li.product .price,
.woocommerce-page .upsells.products ul.products li.product .price {
    color: {$theme['primary']} !important;
    font-weight: 800 !important;
    font-size: 18px !important;
    padding: 0 16px 14px !important;
}

.related.products ul.products li.product .price del,
.upsells.products ul.products li.product .price del,
.woocommerce-page .related.products ul.products li.product .price del,
.woocommerce-page .upsells.products ul.products li.product .price del {
    color: #999 !important;
    font-size: 14px !important;
}

.related.products ul.products li.product .price ins,
.upsells.products ul.products li.product .price ins,
.woocommerce-page .related.products ul.products li.product .price ins,
.woocommerce-page .upsells.products ul.products li.product .price ins {
    color: {$theme['secondary']} !important;
    text-decoration: none !important;
}

/* =====================================================
   CRITICAL: ALL RELATED PRODUCTS BUTTON VARIATIONS
===================================================== */

/* Target ALL possible button selectors in related products */
.related.products ul.products li.product .button,
.upsells.products ul.products li.product .button,
.woocommerce-page .related.products ul.products li.product .button,
.woocommerce-page .upsells.products ul.products li.product .button,
.related.products ul.products li.product a.button,
.upsells.products ul.products li.product a.button,
.woocommerce-page .related.products ul.products li.product a.button,
.woocommerce-page .upsells.products ul.products li.product a.button,
.related.products ul.products li.product .add_to_cart_button,
.upsells.products ul.products li.product .add_to_cart_button,
.woocommerce-page .related.products ul.products li.product .add_to_cart_button,
.woocommerce-page .upsells.products ul.products li.product .add_to_cart_button,
.related.products ul.products li.product .product_type_simple,
.upsells.products ul.products li.product .product_type_simple,
.related.products ul.products li.product .product_type_variable,
.upsells.products ul.products li.product .product_type_variable,
.related.products ul.products li.product .ajax_add_to_cart,
.upsells.products ul.products li.product .ajax_add_to_cart,
.related.products .add_to_cart_button,
.upsells.products .add_to_cart_button,
.related.products .product_type_simple,
.upsells.products .product_type_simple,
.related.products .product_type_variable,
.upsells.products .product_type_variable,
.related.products a.button,
.upsells.products a.button,
.related.products button.button,
.upsells.products button.button {
    background: {$button_bg} !important;
    color: #fff !important;
    border: none !important;
    border-radius: {$button_radius} !important;
    font-weight: 800 !important;
    padding: 11px 20px !important;
    margin: 0 16px 16px !important;
    text-align: center !important;
    width: calc(100% - 32px) !important;
    display: block !important;
    text-decoration: none !important;
    transition: transform 0.25s ease, opacity 0.25s ease !important;
}

.related.products ul.products li.product .button:hover,
.upsells.products ul.products li.product .button:hover,
.woocommerce-page .related.products ul.products li.product .button:hover,
.woocommerce-page .upsells.products ul.products li.product .button:hover,
.related.products .add_to_cart_button:hover,
.upsells.products .add_to_cart_button:hover,
.related.products a.button:hover,
.upsells.products a.button:hover {
    opacity: 0.95 !important;
    transform: translateY(-1px) !important;
}

.related.products ul.products li.product .onsale,
.upsells.products ul.products li.product .onsale,
.woocommerce-page .related.products ul.products li.product .onsale,
.woocommerce-page .upsells.products ul.products li.product .onsale {
    background: {$badge_sale_bg} !important;
    color: #fff !important;
    font-weight: 800 !important;
    border-radius: 8px !important;
    padding: 6px 12px !important;
    font-size: 12px !important;
}

/* Mobile Responsiveness for Related Products */
@media (max-width: 768px) {
    .related.products ul.products,
    .upsells.products ul.products,
    .woocommerce-page .related.products ul.products,
    .woocommerce-page .upsells.products ul.products {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 12px !important;
    }
    
    .related.products > h2,
    .upsells.products > h2,
    .woocommerce-page .related.products > h2,
    .woocommerce-page .upsells.products > h2 {
        font-size: 24px !important;
        margin-bottom: 20px !important;
    }
    
    .lureen-product-description {
        margin: 20px auto;
        padding: 20px 16px;
    }
    
    .lureen-product-description h2 {
        font-size: 24px;
    }
}

/* =====================================================
   WOOCOMMERCE NOTICES
===================================================== */

.woocommerce-message,
.woocommerce-info,
.woocommerce-error {
    border-radius: 12px !important;
    padding: 16px 20px !important;
    font-weight: 600 !important;
}

.woocommerce-message {
    background: rgba({$this->hex_to_rgb($theme['accent'])}, 0.1) !important;
    border-left: 4px solid {$theme['accent']} !important;
    color: {$theme['accent_dark']} !important;
}

.woocommerce-info {
    background: rgba({$this->hex_to_rgb($theme['primary'])}, 0.1) !important;
    border-left: 4px solid {$theme['primary']} !important;
    color: {$theme['primary_dark']} !important;
}

.woocommerce-error {
    background: rgba(239, 68, 68, 0.1) !important;
    border-left: 4px solid #ef4444 !important;
    color: #dc2626 !important;
}

.woocommerce-message a,
.woocommerce-info a,
.woocommerce-error a {
    color: inherit !important;
    text-decoration: underline !important;
    font-weight: 800 !important;
}

CSS;
    }
    
    private function generate_animation_styles($settings) {
        $cart_open = $settings['cart_open_animation'] ?? 'slide';
        $cart_persistent = $settings['cart_persistent_animation'] ?? 'pulse';
        $button_hover = $settings['button_hover_animation'] ?? 'lift';
        $product_card = $settings['product_card_animation'] ?? 'lift';
        
        $css = "\n/* =====================================================\n   ANIMATIONS - FIXED FOR STABILITY\n===================================================== */\n\n";
        
        // Cart Opening Animations
        if ($cart_open !== 'none') {
            $css .= $this->get_cart_open_animation_css($cart_open);
        }
        
        // Cart Persistent Animations
        if ($cart_persistent !== 'none') {
            $css .= $this->get_cart_persistent_animation_css($cart_persistent);
        }
        
        // Button Hover Animations (FIXED)
        if ($button_hover !== 'none') {
            $css .= $this->get_button_hover_animation_css($button_hover);
        }
        
        // Product Card Animations (FIXED)
        if ($product_card !== 'none') {
            $css .= $this->get_product_card_animation_css($product_card);
        }
        
        return $css;
    }
    
    private function get_cart_open_animation_css($animation) {
        $animations = [
            'slide' => <<<CSS
.lureen-cart-modal.active {
    animation: lureen-slide-in 0.4s ease-out;
}
@keyframes lureen-slide-in {
    from { opacity: 0; transform: translateX(-100%); }
    to { opacity: 1; transform: translateX(0); }
}

CSS,
            'fade' => <<<CSS
.lureen-cart-modal.active {
    animation: lureen-fade-in 0.3s ease-out;
}
@keyframes lureen-fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

CSS,
            'zoom' => <<<CSS
.lureen-cart-modal.active {
    animation: lureen-zoom-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes lureen-zoom-in {
    from { opacity: 0; transform: scale(0.5); }
    to { opacity: 1; transform: scale(1); }
}

CSS,
            'bounce' => <<<CSS
.lureen-cart-modal.active {
    animation: lureen-bounce-in 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}
@keyframes lureen-bounce-in {
    0% { opacity: 0; transform: scale(0.3); }
    50% { opacity: 1; transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); }
}

CSS,
            'flip' => <<<CSS
.lureen-cart-modal.active {
    animation: lureen-flip-in 0.6s ease-out;
}
@keyframes lureen-flip-in {
    from { opacity: 0; transform: perspective(400px) rotateY(-90deg); }
    to { opacity: 1; transform: perspective(400px) rotateY(0); }
}

CSS,
            'rotate' => <<<CSS
.lureen-cart-modal.active {
    animation: lureen-rotate-in 0.5s ease-out;
}
@keyframes lureen-rotate-in {
    from { opacity: 0; transform: rotate(-180deg) scale(0); }
    to { opacity: 1; transform: rotate(0) scale(1); }
}

CSS,
            'scale' => <<<CSS
.lureen-cart-modal.active {
    animation: lureen-scale-in 0.4s ease-out;
}
@keyframes lureen-scale-in {
    from { opacity: 0; transform: scale(1.5); }
    to { opacity: 1; transform: scale(1); }
}

CSS,
        ];
        
        return $animations[$animation] ?? '';
    }
    
    private function get_cart_persistent_animation_css($animation) {
        $animations = [
            'pulse' => <<<CSS
.lureen-sticky-cart {
    animation: lureen-pulse 2s ease-in-out infinite;
}
@keyframes lureen-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

CSS,
            'bounce' => <<<CSS
.lureen-sticky-cart {
    animation: lureen-bounce-subtle 3s ease-in-out infinite;
}
@keyframes lureen-bounce-subtle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

CSS,
            'shake' => <<<CSS
.lureen-sticky-cart {
    animation: lureen-shake 4s ease-in-out infinite;
}
@keyframes lureen-shake {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(5deg); }
    75% { transform: rotate(-5deg); }
}

CSS,
            'swing' => <<<CSS
.lureen-sticky-cart {
    animation: lureen-swing 3s ease-in-out infinite;
    transform-origin: top center;
}
@keyframes lureen-swing {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(10deg); }
    75% { transform: rotate(-10deg); }
}

CSS,
            'tada' => <<<CSS
.lureen-sticky-cart {
    animation: lureen-tada 3s ease-in-out infinite;
}
@keyframes lureen-tada {
    0%, 100% { transform: scale(1) rotate(0deg); }
    10%, 20% { transform: scale(0.9) rotate(-3deg); }
    30%, 50%, 70%, 90% { transform: scale(1.1) rotate(3deg); }
    40%, 60%, 80% { transform: scale(1.1) rotate(-3deg); }
}

CSS,
            'heartbeat' => <<<CSS
.lureen-sticky-cart {
    animation: lureen-heartbeat 1.5s ease-in-out infinite;
}
@keyframes lureen-heartbeat {
    0%, 100% { transform: scale(1); }
    14% { transform: scale(1.1); }
    28% { transform: scale(1); }
    42% { transform: scale(1.1); }
    70% { transform: scale(1); }
}

CSS,
            'wobble' => <<<CSS
.lureen-sticky-cart {
    animation: lureen-wobble 3s ease-in-out infinite;
}
@keyframes lureen-wobble {
    0%, 100% { transform: translateX(0) rotate(0deg); }
    15% { transform: translateX(-5px) rotate(-5deg); }
    30% { transform: translateX(4px) rotate(3deg); }
    45% { transform: translateX(-3px) rotate(-3deg); }
    60% { transform: translateX(2px) rotate(2deg); }
    75% { transform: translateX(-1px) rotate(-1deg); }
}

CSS,
            'jello' => <<<CSS
.lureen-sticky-cart {
    animation: lureen-jello 3s ease-in-out infinite;
}
@keyframes lureen-jello {
    0%, 100% { transform: skewX(0deg) skewY(0deg); }
    30% { transform: skewX(12.5deg) skewY(12.5deg); }
    40% { transform: skewX(-9.375deg) skewY(-9.375deg); }
    50% { transform: skewX(5.625deg) skewY(5.625deg); }
    65% { transform: skewX(-3.125deg) skewY(-3.125deg); }
    75% { transform: skewX(1.5625deg) skewY(1.5625deg); }
}

CSS,
        ];
        
        return $animations[$animation] ?? '';
    }
    
    private function get_button_hover_animation_css($animation) {
        // FIXED: Only target buttons on hover, not affecting layout
        $selectors = '.lureen-add-to-cart-btn, .lureen-bundle-btn, .lureen-load-more-btn, .lureen-cta-btn, .lureen-checkout-btn, .single_add_to_cart_button, .woocommerce a.button, .woocommerce button.button, .related.products .button, .upsells.products .button, .lureen-bundle-cta';
        
        $animations = [
            'lift' => <<<CSS
{$selectors}:hover:not(:disabled):not(.loading) {
    transform: translateY(-3px) !important;
}

CSS,
            'grow' => <<<CSS
{$selectors}:hover:not(:disabled):not(.loading) {
    transform: scale(1.05) !important;
}

CSS,
            'shrink' => <<<CSS
{$selectors}:hover:not(:disabled):not(.loading) {
    transform: scale(0.95) !important;
}

CSS,
            'glow' => <<<CSS
{$selectors}:hover:not(:disabled):not(.loading) {
    box-shadow: 0 0 20px currentColor !important;
}

CSS,
            'shadow' => <<<CSS
{$selectors}:hover:not(:disabled):not(.loading) {
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3) !important;
}

CSS,
            'rotate' => <<<CSS
{$selectors}:hover:not(:disabled):not(.loading) {
    transform: rotate(3deg) !important;
}

CSS,
            'skew' => <<<CSS
{$selectors}:hover:not(:disabled):not(.loading) {
    transform: skew(-2deg, -2deg) !important;
}

CSS,
        ];
        
        return $animations[$animation] ?? '';
    }
    
    private function get_product_card_animation_css($animation) {
        // FIXED: Only target cards on hover, preserve grid layout
        $selectors = '.lureen-product-card, .lureen-category-card, .lureen-bundle-product-card, .related.products ul.products li.product, .upsells.products ul.products li.product';
        
        $animations = [
            'lift' => <<<CSS
{$selectors}:hover {
    transform: translateY(-8px) !important;
}

CSS,
            'zoom' => <<<CSS
{$selectors}:hover {
    transform: scale(1.03) !important;
}

CSS,
            'tilt' => <<<CSS
{$selectors}:hover {
    transform: perspective(1000px) rotateX(5deg) rotateY(-5deg) !important;
}

CSS,
            'shine' => <<<CSS
{$selectors} {
    position: relative !important;
    overflow: hidden !important;
}
{$selectors}::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: -100% !important;
    width: 100% !important;
    height: 100% !important;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent) !important;
    transition: left 0.5s !important;
    z-index: 1 !important;
}
{$selectors}:hover::before {
    left: 100% !important;
}

CSS,
            'float' => <<<CSS
{$selectors} {
    animation: lureen-float 6s ease-in-out infinite !important;
}
@keyframes lureen-float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

CSS,
        ];
        
        return $animations[$animation] ?? '';
    }
    
    public function inject_theme_scripts() {
        // Scripts are handled via inline CSS animations
        // No additional JS needed for v2.1
    }
    
    /* =====================================================
       TEXT REPLACEMENT
    ===================================================== */
    
    public function replace_text_labels($content) {
        $settings = $this->current_settings;
        
        $replacements = [
            'مجموعة ربيع 2025' => $settings['hero_title'],
            'تألقي بأحدث صيحات الموسم' => $settings['hero_subtitle'],
            'تسوقي الآن' => $settings['hero_cta'],
            'شوفي الخيارات' => $settings['view_options_text'],
            'أضف للسلة' => $settings['add_to_cart_text'],
            'اختر حزمتك' => $settings['choose_bundle_text'],
            'جديد' => $settings['new_badge_text'],
            'تخفيض' => $settings['sale_badge_text']
        ];
        
        foreach ($replacements as $original => $replacement) {
            if (!empty($replacement) && $replacement !== $original) {
                $content = str_replace($original, $replacement, $content);
            }
        }
        
        return $content;
    }
    
    public function replace_button_text($text, $product = null) {
        $settings = $this->current_settings;
        
        if ($text === 'شوفي الخيارات' && !empty($settings['view_options_text'])) {
            return $settings['view_options_text'];
        }
        
        if ($text === 'أضف للسلة' && !empty($settings['add_to_cart_text'])) {
            return $settings['add_to_cart_text'];
        }
        
        if ($text === 'اختر حزمتك' && !empty($settings['choose_bundle_text'])) {
            return $settings['choose_bundle_text'];
        }
        
        return $text;
    }
    
    /* =====================================================
       SITE TITLE/NAME CUSTOMIZATION
    ===================================================== */
    
    public function custom_document_title($title) {
        $settings = $this->current_settings;
        
        if (is_front_page() && !empty($settings['website_title'])) {
            return $settings['website_title'];
        }
        
        return $title;
    }
    
    public function custom_bloginfo($output, $show) {
        $settings = $this->current_settings;
        
        if ($show === 'name' && !empty($settings['store_name'])) {
            return $settings['store_name'];
        }
        
        if ($show === 'description' && !empty($settings['website_title'])) {
            return $settings['website_title'];
        }
        
        return $output;
    }
    
    /* =====================================================
       SINGLE PRODUCT DESCRIPTION DISPLAY
    ===================================================== */
    
    public function render_product_description() {
        global $product;
        
        if (!$product) return;
        
        $description = $product->get_description();
        
        if (empty($description)) return;
        
        ?>
        <div class="lureen-product-description">
            <h2>وصف المنتج</h2>
            <div class="lureen-product-description-content">
                <?php echo wp_kses_post($description); ?>
            </div>
        </div>
        <?php
    }
    
    /* =====================================================
       HELPER FUNCTIONS
    ===================================================== */
    
    private function hex_to_rgb($hex) {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "$r, $g, $b";
    }
}

// Initialize
new Lureen_Theme_Customizer_V2();
