<?php
/**
 * Plugin Name: ACF Polylang Text Field
 * Description: Polylang'de tanımlı her dil için tek bir ACF alanında metin girişi sağlayan özel bir alan tipi. Veri, ilişkisel dizi olarak saklanır.
 * Version: 1.0.1
 * Author: Tolga Koçak
 * License: GPL2
 */

// ACF'nin yüklü olup olmadığını kontrol et
if( ! defined( 'ABSPATH' ) ) exit;

// Özel alanı yüklemek için fonksiyon
function include_acf_field_polylang_text( $version ) {
    
    // Sınıf dosyasının yolunu tanımla
    $dir = plugin_dir_path( __FILE__ );

    // Özel sınıf dosyasını dahil et
    include_once( $dir . 'class-acf-field-polylang-text.php' );

}

// ACF'nin alan tiplerini dahil etme kancasına (hook) fonksiyonu bağla
add_action('acf/include_field_types', 'include_acf_field_polylang_text');

?>