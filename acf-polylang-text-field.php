<?php
/**
 * Plugin Name: ACF Polylang Text Field
 * Description: Polylang'de tanımlı her dil için tek bir ACF alanında metin girişi sağlayan özel bir alan tipi. Veri, ilişkisel dizi olarak saklanır.
 * Version: 1.0.2
 * Author: Tolga Koçak
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'acf/include_field_types', function() {
    require_once plugin_dir_path( __FILE__ ) . 'class-acf-field-polylang-text.php';
    if ( class_exists( 'acf_field_polylang_text' ) ) {
        acf_register_field_type( new acf_field_polylang_text() );
    }
} );