<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class acf_field_polylang_text extends acf_field {

    public function __construct() {

        $this->name     = 'polylang_text';
        $this->label    = __( 'Polylang Metin Alanı', 'acf-polylang-text' );
        $this->category = 'basic';

        $this->defaults = array(
            'field_type'    => 'textarea',
            'rows'          => 5,
            'tabs'          => 'all',
            'toolbar'       => 'full',
            'media_buttons' => 1,
        );

        parent::__construct();
    }

    /*
    * render_field_settings()
    * Alan ayarları menüsünü oluşturur (Girdi Tipi, Satır Sayısı ve WYSIWYG ayarları)
    */
    function render_field_settings( $field ) {
        
        // --- TEMEL AYARLAR ---
        
        // 1. Alan Tipi Seçimi (Text/Textarea/WYSIWYG)
        acf_render_field_setting( $field, array(
            'label'        => __('Girdi Tipi', 'acf-polylang-text'),
            'instructions' => __('Her dil için hangi girdi tipini kullanacağınızı seçin.', 'acf-polylang-text'),
            'type'         => 'select',
            'name'         => 'field_type',
            'choices'      => array(
                'text'      => __('Tek Satır Metin (Text)', 'acf-polylang-text'),
                'textarea'  => __('Çok Satırlı Metin (Textarea)', 'acf-polylang-text'),
                'wysiwyg'   => __('Zengin Metin Editörü (WYSIWYG)', 'acf-polylang-text'),
            )
        ));

        // 2. Textarea/WYSIWYG için Satır Sayısı Ayarı (Koşullu)
        acf_render_field_setting( $field, array(
            'label'        => __('Satır Sayısı', 'acf-polylang-text'),
            'instructions' => __('Textarea veya WYSIWYG alanları için satır sayısını ayarlayın.', 'acf-polylang-text'),
            'type'         => 'number',
            'name'         => 'rows',
            'min'          => 1,
            'conditional_logic' => array(
                array(
                    array(
                        'field' => 'field_type',
                        'operator' => '!=',
                        'value' => 'text', 
                    ),
                ),
            )
        ));
        
        // --- WYSIWYG'E ÖZEL AYARLAR (Koşullu Olarak Gösterilir) ---

        $wysiwyg_conditional_logic = array(
            array(
                array(
                    'field' => 'field_type',
                    'operator' => '==',
                    'value' => 'wysiwyg',
                ),
            )
        );
        
        // 3. Sekmelemeler (Tabs: Görsel ve Metin)
        acf_render_field_setting( $field, array(
            'label'        => __('Sekmelemeler', 'acf-polylang-text'),
            'instructions' => __('Görsel ve Metin sekmelerinin gösterilip gösterilmeyeceğini seçin.', 'acf-polylang-text'),
            'type'         => 'select',
            'name'         => 'tabs',
            'choices'      => array(
                'all'       => __('Görsel ve metin', 'acf-polylang-text'),
                'visual'    => __('Sadece Görsel', 'acf-polylang-text'),
                'text'      => __('Sadece Metin', 'acf-polylang-text'),
            ),
            'conditional_logic' => $wysiwyg_conditional_logic,
        ));

        // 4. Araç Çubuğu (Toolbar)
        acf_render_field_setting( $field, array(
            'label'        => __('Araç çubuğu', 'acf-polylang-text'),
            'instructions' => __('Kullanılacak araç çubuğu stilini seçin.', 'acf-polylang-text'),
            'type'         => 'select',
            'name'         => 'toolbar',
            'choices'      => array(
                'full'      => __('Full', 'acf-polylang-text'),
                'basic'     => __('Basic', 'acf-polylang-text'),
                'teeny'     => __('Teeny (Çok basit)', 'acf-polylang-text'),
            ),
            'conditional_logic' => $wysiwyg_conditional_logic,
        ));

        // 5. Ortam Yükleme Tuşları (Media Buttons)
        acf_render_field_setting( $field, array(
            'label'        => __('Ortam yükleme tuşları gösterilsin', 'acf-polylang-text'),
            'type'         => 'true_false',
            'name'         => 'media_buttons',
            'ui'           => 1, // Toggle anahtarı olarak göster
            'conditional_logic' => $wysiwyg_conditional_logic,
        ));
    }


    /*
    * render_field()
    * Alan düzenleme sayfasında gösterilecek HTML'i oluşturur
    */
    function render_field( $field ) {
        
        if ( !function_exists('pll_languages_list') ) {
            echo '<p style="color: red;">' . __('Hata: Bu alan tipi için Polylang eklentisi gereklidir.', 'acf-polylang-text') . '</p>';
            return;
        }

        $values = is_array( $field['value'] ) ? $field['value'] : array();
        $languages = pll_languages_list();
        $field_type = $field['field_type'];

        echo '<div class="acf-polylang-text-field">';

        foreach ( $languages as $lang_slug ) {

            $input_name = $field['name'] . '[' . $lang_slug . ']';
            $input_value = isset( $values[ $lang_slug ] ) ? $values[ $lang_slug ] : '';
            $input_id = esc_attr($field['id']) . '-' . $lang_slug;
            
            // Dil etiketi
            echo '<div class="language-field-wrapper" style="margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">';
            echo '<h4 style="margin-top: 0; padding-bottom: 5px; border-bottom: 1px solid #eee;"><span class="dashicons dashicons-admin-site"></span> ' . strtoupper( $lang_slug ) . '</h4>';
            
            // Seçilen alana göre farklı HTML çıktısı
            switch ( $field_type ) {
                case 'text':
                    echo '<input type="text" id="' . $input_id . '" name="' . esc_attr($input_name) . '" value="' . esc_attr($input_value) . '" class="acf-polylang-input-text" />';
                    break;
                    
                case 'textarea':
                    echo '<textarea id="' . $input_id . '" name="' . esc_attr($input_name) . '" rows="' . esc_attr($field['rows']) . '" class="acf-polylang-input-textarea">' . esc_textarea($input_value) . '</textarea>';
                    break;
                    
                case 'wysiwyg':
                    
                    // WYSIWYG (Rich Text Editor) Ayarlarını Hazırla
                    $editor_settings = array(
                        'textarea_name' => esc_attr($input_name),
                        'textarea_rows' => esc_attr($field['rows']),
                        'editor_class'  => 'acf-polylang-input-wysiwyg',
                        
                        // Kullanıcının seçtiği ayarları uygula
                        'tinymce'       => ($field['tabs'] == 'text' || $field['toolbar'] == 'teeny') ? false : true, // Metin sekmesi veya teeny seçiliyse TinyMCE'yi kapat
                        'quicktags'     => ($field['tabs'] == 'visual') ? false : true, // Görsel sekmesi seçiliyse Quicktags'i kapat
                        'media_buttons' => (bool)$field['media_buttons'],
                        'teeny'         => ($field['toolbar'] == 'teeny') ? true : false,
                        
                        // Toolbar ayarları
                        'toolbar1' => ($field['toolbar'] == 'basic') ? 'bold,italic,bullist,numlist,link,blockquote' : 'formatselect,bold,italic,bullist,numlist,link,blockquote,alignleft,aligncenter,alignright,wp_adv',
                        'toolbar2' => ($field['toolbar'] == 'basic') ? '' : 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
                        'toolbar3' => '',
                        'toolbar4' => '',
                    );
                    
                    // Sadece Metin (Text Only) sekmesi seçiliyse, tinymce tamamen kapatılır.
                    if ( $field['tabs'] == 'text' ) {
                        $editor_settings['tinymce'] = false;
                        $editor_settings['quicktags'] = true;
                    } 
                    
                    // Sadece Görsel (Visual Only) sekmesi seçiliyse, quicktags kapatılır.
                    if ( $field['tabs'] == 'visual' ) {
                        $editor_settings['quicktags'] = false;
                    }
                    
                    // Editörü oluştur
                    wp_editor( $input_value, $input_id, $editor_settings );
                    break;
            }

            echo '</div>'; // .language-field-wrapper
        }
        
        echo '</div>'; // .acf-polylang-text-field
    }

    /*
    * update_value()
    * Yönetici panelinden gelen (dizi formatındaki) değeri veritabanına kaydederken serileştirir.
    */
    function update_value( $value, $post_id, $field ) { 
        return $value; 
    }
    
    function load_value( $value, $post_id, $field ) { 
        return $value; 
    }
}