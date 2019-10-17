<?php
// подключаем функцию активации мета блока (my_extra_fields)
add_action('admin_init', 'my_extra_fields', 1);

function my_extra_fields() {
    add_meta_box( 'extra_fields', 'Relevance & Size', 'extra_fields_box_func', array('instagram'), 'normal', 'high'  );
}

// код блока
function extra_fields_box_func( $post ){
    ?>
    <p>
        <input type="hidden" name="extra[out]" value="">
        <label><input type="checkbox" name="extra[out]" value="OUT NOW!" <?php checked( get_post_meta($post->ID, 'out', true), 'OUT NOW!' )?> />OUT NOW!</label>
    </p>

    <p>
        <input type="hidden" name="extra[large]" value="">
        <label><input type="checkbox" name="extra[large]" value="1" <?php checked( get_post_meta($post->ID, 'large', true), 1 )?> />Large</label>
    </p>

    <input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
    <?php
}

// включаем обновление полей при сохранении
add_action('save_post', 'my_extra_fields_update', 0);

/* Сохраняем данные, при сохранении поста */
function my_extra_fields_update( $post_id ){
    // базовая проверка
    if (
        empty( $_POST['extra'] )
        || ! wp_verify_nonce( $_POST['extra_fields_nonce'], __FILE__ )
        || wp_is_post_autosave( $post_id )
        || wp_is_post_revision( $post_id )
    )
        return false;

    // Все ОК! Теперь, нужно сохранить/удалить данные
    $_POST['extra'] = array_map( 'sanitize_text_field', $_POST['extra'] );
    foreach( $_POST['extra'] as $key => $value ){
        if( empty($value) ){
            delete_post_meta( $post_id, $key ); // удаляем поле если значение пустое
            continue;
        }

        update_post_meta( $post_id, $key, $value ); // add_post_meta() работает автоматически
    }

    return $post_id;
}

