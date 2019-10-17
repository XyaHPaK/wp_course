<?php
class ET_Builder_Module_Instagram extends ET_Builder_Module {
    function init() {
        $this->name       = esc_html__( 'Instagram', 'et_builder' );
        $this->plural     = esc_html__( 'Instagram', 'et_builder' );
        $this->slug       = 'et_pb_instagram';
        $this->vb_support = 'off';
        $this->main_css_element = '%%order_class%% .et_pb_instagram';

        $this->settings_modal_toggles = array(
            'general'  => array(
                'toggles' => array(
                    'main_content' => esc_html__( 'Content', 'et_builder' ),
                ),
            ),
        );
        wp_enqueue_script('equalHeight', get_stylesheet_directory_uri() . '/divi_modules/Instagram/js/equalHeight.js', array('jquery'));
        wp_enqueue_script('LoadMore', get_stylesheet_directory_uri() . '/divi_modules/Instagram/js/loadmore.js', array('jquery'));

        wp_localize_script('LoadMore', 'myajax',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
            )
        );

        wp_enqueue_style('instagram', get_stylesheet_directory_uri() . '/divi_modules/Instagram/css/instagram_mod.css');
    }

    function Instagram_output() {

        $instagram_output = array();

        $query = array_slice(create_true_arr(),0, 10);
        $arr_rest = array_slice(create_true_arr(),10);
        foreach ( $query as $post) {
            if (get_the_post_thumbnail_url($post->ID) !== false ) {
                $url = get_the_post_thumbnail_url($post->ID);
                $image = sprintf('<img class="large" src="%1$s" alt="%1$s">', $url);
            } else {
                $url = $post->post_content;
                $image = sprintf('<img class="equal" src="%1$s" alt="%1$s">', $url);
            }
            $label = get_post_meta($post->ID, 'out', true);
            $label_html = sprintf('<span>%1$s</span>', $label);
            $output = sprintf(
                '<div class="img_container">%1$s%2$s</div>',
                $image,
                $label_html
            );
            array_push($instagram_output, $output);
        }
        ?>
        <!-- ajax vars           -->
        <script>
            var true_arr = '<?php echo serialize($arr_rest); ?>';
            var offset = 0;
            var length = <?php echo count($arr_rest) ?>;
        </script>
        <?php


        return implode('',$instagram_output);
    }

    function render( $attrs, $content = null, $render_slug ) {
        $images = $this->Instagram_output();
        $output = sprintf(
            '<div class="instagram_media_container">%1$s</div><div id="true_loadmore">see more</div>',
            $images
        );

        return $output;
    }

}

new ET_Builder_Module_Instagram;