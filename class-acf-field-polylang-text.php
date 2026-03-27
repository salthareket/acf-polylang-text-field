<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class acf_field_polylang_text extends acf_field {

    public function __construct() {
        $this->name     = 'polylang_text';
        $this->label    = __( 'Polylang Metin Alanı', 'acf-polylang-text' );
        $this->category = 'basic';
        $this->defaults = [
            'field_type'    => 'textarea',
            'rows'          => 5,
            'tabs'          => 'all',
            'toolbar'       => 'full',
            'media_buttons' => 1,
        ];
        parent::__construct();
    }

    function render_field_settings( $field ) {

        acf_render_field_setting( $field, [
            'label'   => __( 'Girdi Tipi', 'acf-polylang-text' ),
            'type'    => 'select',
            'name'    => 'field_type',
            'choices' => [
                'text'     => __( 'Tek Satır Metin (Text)', 'acf-polylang-text' ),
                'textarea' => __( 'Çok Satırlı Metin (Textarea)', 'acf-polylang-text' ),
                'wysiwyg'  => __( 'Zengin Metin Editörü (WYSIWYG)', 'acf-polylang-text' ),
            ],
        ]);

        acf_render_field_setting( $field, [
            'label' => __( 'Satır Sayısı', 'acf-polylang-text' ),
            'type'  => 'number',
            'name'  => 'rows',
            'min'   => 1,
            'conditional_logic' => [[ [ 'field' => 'field_type', 'operator' => '!=', 'value' => 'text' ] ]],
        ]);

        $wysiwyg_cond = [[ [ 'field' => 'field_type', 'operator' => '==', 'value' => 'wysiwyg' ] ]];

        acf_render_field_setting( $field, [
            'label'   => __( 'Sekmelemeler', 'acf-polylang-text' ),
            'type'    => 'select',
            'name'    => 'tabs',
            'choices' => [
                'all'    => __( 'Görsel ve metin', 'acf-polylang-text' ),
                'visual' => __( 'Sadece Görsel', 'acf-polylang-text' ),
                'text'   => __( 'Sadece Metin', 'acf-polylang-text' ),
            ],
            'conditional_logic' => $wysiwyg_cond,
        ]);

        acf_render_field_setting( $field, [
            'label'   => __( 'Araç çubuğu', 'acf-polylang-text' ),
            'type'    => 'select',
            'name'    => 'toolbar',
            'choices' => [
                'full'  => __( 'Full', 'acf-polylang-text' ),
                'basic' => __( 'Basic', 'acf-polylang-text' ),
                'teeny' => __( 'Teeny', 'acf-polylang-text' ),
            ],
            'conditional_logic' => $wysiwyg_cond,
        ]);

        acf_render_field_setting( $field, [
            'label'             => __( 'Ortam yükleme tuşları gösterilsin', 'acf-polylang-text' ),
            'type'              => 'true_false',
            'name'              => 'media_buttons',
            'ui'                => 1,
            'conditional_logic' => $wysiwyg_cond,
        ]);
    }

    function render_field( $field ) {

        // Polylang yoksa render etme — ana dosyada zaten kontrol var ama savunma amaçlı
        if ( ! function_exists( 'pll_languages_list' ) ) {
            echo '<p style="color:red;">' . esc_html__( 'Polylang eklentisi gereklidir.', 'acf-polylang-text' ) . '</p>';
            return;
        }

        $values     = is_array( $field['value'] ) ? $field['value'] : [];
        $languages  = pll_languages_list();
        $field_type = $field['field_type'];

        echo '<div class="acf-polylang-text-field">';

        foreach ( $languages as $lang_slug ) {
            $input_name  = $field['name'] . '[' . $lang_slug . ']';
            $input_value = $values[ $lang_slug ] ?? '';
            $input_id    = esc_attr( $field['id'] ) . '-' . $lang_slug;

            echo '<div class="language-field-wrapper" style="margin-bottom:12px;padding:10px;border:1px solid #ddd;border-radius:4px;">';
            echo '<h4 style="margin:0 0 8px;padding-bottom:5px;border-bottom:1px solid #eee;font-size:13px;">';
            echo '<span class="dashicons dashicons-admin-site" style="font-size:14px;"></span> ' . esc_html( strtoupper( $lang_slug ) );
            echo '</h4>';

            switch ( $field_type ) {
                case 'text':
                    printf(
                        '<input type="text" id="%s" name="%s" value="%s" style="width:100%%;" />',
                        $input_id, esc_attr( $input_name ), esc_attr( $input_value )
                    );
                    break;

                case 'textarea':
                    printf(
                        '<textarea id="%s" name="%s" rows="%s" style="width:100%%;">%s</textarea>',
                        $input_id, esc_attr( $input_name ), esc_attr( $field['rows'] ), esc_textarea( $input_value )
                    );
                    break;

                case 'wysiwyg':
                    wp_editor( $input_value, $input_id, $this->get_editor_settings( $field, $input_name ) );
                    break;
            }

            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * WYSIWYG editor ayarlarını tek noktadan oluşturur.
     */
    private function get_editor_settings( array $field, string $input_name ): array {
        $is_text_only   = $field['tabs'] === 'text';
        $is_visual_only = $field['tabs'] === 'visual';
        $is_teeny       = $field['toolbar'] === 'teeny';
        $is_basic       = $field['toolbar'] === 'basic';

        return [
            'textarea_name' => esc_attr( $input_name ),
            'textarea_rows' => (int) $field['rows'],
            'editor_class'  => 'acf-polylang-input-wysiwyg',
            'tinymce'       => ( $is_text_only || $is_teeny ) ? false : true,
            'quicktags'     => $is_visual_only ? false : true,
            'media_buttons' => (bool) $field['media_buttons'],
            'teeny'         => $is_teeny,
            'toolbar1'      => $is_basic
                ? 'bold,italic,bullist,numlist,link,blockquote'
                : 'formatselect,bold,italic,bullist,numlist,link,blockquote,alignleft,aligncenter,alignright,wp_adv',
            'toolbar2'      => $is_basic
                ? ''
                : 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
            'toolbar3'      => '',
            'toolbar4'      => '',
        ];
    }

    function update_value( $value, $post_id, $field ) {
        return $value;
    }

    function load_value( $value, $post_id, $field ) {
        return $value;
    }
}
