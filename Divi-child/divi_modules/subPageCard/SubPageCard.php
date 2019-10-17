<?php
class ET_Builder_Module_SubPageCard extends ET_Builder_Module {
    function init() {
        $this->name       = esc_html__( 'Sub Page Card', 'et_builder' );
        $this->plural     = esc_html__( 'Sub Page Cards', 'et_builder' );
        $this->slug       = 'et_pb_sub_page';
        $this->vb_support = 'off';
        $this->main_css_element = '%%order_class%% .et_pb_post';

        $this->settings_modal_toggles = array(
            'general'  => array(
                'toggles' => array(
                    'main_content' => esc_html__( 'Content', 'et_builder' ),
                ),
            ),
        );
        wp_enqueue_style('sp_module', get_stylesheet_directory_uri() . '/divi_modules/subPageCard/css/sp_mod-style.css');
    }

    function get_fields() {
        $fields = array(
            'sub_page' => array(
                'label'             => esc_html__( 'Sub Page', 'et_builder' ),
                'type'              => 'text',
                'option_category'   => 'configuration',
                'description'       => esc_html__( 'Enter parent page id', 'et_builder' ),
                'toggle_slug'       => 'main_content',
            ),
        );
        return $fields;
    }

    function render( $attrs, $content = null, $render_slug ) {

        $main_page = $this->props['sub_page'];

        if ('' !== $main_page){
            $page_args = array(
                'depth'        => 0,
                'show_date'    => '',
                'date_format'  => get_option('date_format'),
                'child_of'     => $main_page,
                'exclude'      => '',
                'exclude_tree' => '',
                'include'      => '',
                'title_li'     => __('Pages'),
                'echo'         => 1,
                'authors'      => '',
                'sort_column'  => 'menu_order, post_title',
                'sort_order'   => 'ASC',
                'link_before'  => '',
                'link_after'   => '',
                'meta_key'     => '',
                'meta_value'   => '',
                'number'       => '',
                'offset'       => '',
                'walker'       => '',
                'post_type'    => 'page', // из функции get_pages()
            );
            $sub_pages = get_pages( $page_args );
            $sub_page_output = array();
            $sub_page_bckg = array(
                0 => '#1ba3a5',
                1 => '#fbb535',
                2 => '#fe2a63',
                3 => '#3d1249',
                4 => '#00713d',
                5 => '#ff5c47',
                6 => '#66b1eb'
            );
            $count = 0;
            foreach( $sub_pages as $post ) :
                setup_postdata( $post );
                if ($count > 6) {
                    $count = 0;
                }
                $sub_page_bckg1 =  $sub_page_bckg[$count];
                $sub_page_title = $post->post_title;
                $sub_page_img = get_the_post_thumbnail_url($post->ID);
                $sub_page_link = get_the_permalink($post->ID);
                $sub_page_excerpt = $post->post_excerpt;
                $sub_page_output1 = sprintf(
                  '<a href="%4$s" class="sp_card_container">
                            <div class="content_container" style="background-color: %5$s">
                                <h2>%1$s</h2>
                                <span>%2$s</span>
                            </div>
                            <div class="img_container"><img src="%3$s"></div>
                        </a>',
                    $sub_page_title,
                    $sub_page_excerpt,
                    $sub_page_img,
                    $sub_page_link,
                    $sub_page_bckg1
                );
                array_push($sub_page_output, $sub_page_output1);
                $count ++;
            endforeach;

            return implode('',$sub_page_output);
        }
    }
}

new ET_Builder_Module_SubPageCard;