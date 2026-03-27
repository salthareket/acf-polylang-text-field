<?php
/**
 * Plugin Name: ACF Polylang Text Field
 * Description: Polylang'de tanımlı her dil için tek bir ACF alanında metin girişi sağlayan özel bir alan tipi.
 * Version: 1.0.3
 * Author: Tolga Koçak
 * License: GPL2
 * Requires Plugins: advanced-custom-fields, polylang
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Bağımlılık kontrolü — ACF ve Polylang yüklü değilse plugin'i yükleme
add_action( 'admin_init', function() {
    $missing = [];

    if ( ! class_exists( 'ACF' ) && ! class_exists( 'acf' ) ) {
        $missing[] = 'Advanced Custom Fields (ACF)';
    }
    if ( ! function_exists( 'pll_languages_list' ) ) {
        $missing[] = 'Polylang';
    }

    if ( ! empty( $missing ) ) {
        add_action( 'admin_notices', function() use ( $missing ) {
            $list = '<strong>' . implode( '</strong>, <strong>', $missing ) . '</strong>';
            printf(
                '<div class="notice notice-error"><p>%s</p></div>',
                sprintf(
                    esc_html__( 'ACF Polylang Text Field: Çalışmak için %s eklentilerinin yüklü ve aktif olması gerekir.', 'acf-polylang-text' ),
                    $list
                )
            );
        });

        // Plugin'i devre dışı bırakma — sadece uyarı göster, field type'ı kaydetme
        return;
    }
});

// Field type'ı sadece ACF ve Polylang varsa kaydet
add_action( 'acf/include_field_types', function() {
    if ( ! function_exists( 'pll_languages_list' ) ) return;

    require_once plugin_dir_path( __FILE__ ) . 'class-acf-field-polylang-text.php';
    if ( class_exists( 'acf_field_polylang_text' ) ) {
        acf_register_field_type( new acf_field_polylang_text() );
    }
});
