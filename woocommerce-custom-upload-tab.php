<?php

/*
 * Plugin Name: Woocommerce Custom Upload Tab
 * Plugin URI: https://dominant-core.hr
 * Text Domain: custom-upload-tag
 * Domain Path: /languages
 * Description: Add custom metabox for upload documents to woocommerce and add link to additional info section
 * Tags: documents, upload, custom, wordpress, woocommerce, pdf, tab
 * Author: Domagoj Rogošić
 * Author URI: https://dominant-core.hr
 * Version: 08.2020.v1.1
 */

add_filter('woocommerce_product_data_tabs', 'dominant_product_settings_tabs' );
function dominant_product_settings_tabs( $tabs ){

    $tabs['documents'] = array(
        'label'    => __('Documents', 'custom-upload-tag'),
        'target'   => 'documents_data',
        'class'    => array('show_if_simple'),
        'priority' => 21,
    );
    return $tabs;

}

add_action( 'woocommerce_product_data_panels', 'dominant_core_product_panel' );
function dominant_core_product_panel(){
?>
    <div id="documents_data" class="panel woocommerce_options_panel hidden">
<?
    global $post;

    $documents = get_post_meta($post->ID, '_dominant_document');

    if($documents) { ?>

        <div class="options options_group">

        <?php foreach($documents[0] as $document) { ?>

            <p class="form-field">
                <label>
                    <a href="<?php echo $document['url'] ?>" target="_blank"><?php echo $document['filename'] ?></a>
                </label>
            </p>

        <?php } ?>

        </div>

    <?php } ?>

        <input type="hidden" name="dominant_document" value="" id="custom_document_data">

<?php if($documents) { ?>
            <p class="form-field">
                <label for="detach_all" style="color: red;"><?php _e( 'Detach all Documents?', 'custom-upload-tag' ); ?></label>
                <input type="checkbox" class="checkbox" style="" name="detach_all" id="detach_all" value="yes">
                <span class="description"><?php _e( 'Select this option and save the changes to reveal all documents with this item.', 'custom-upload-tag' ); ?></span>
            </p>
        <?php } ?>
        <div class="options" style="display: none;">
            <h3><?php _e( 'Selected Documents', 'custom-upload-tag' ); ?> <small><?php _e( 'After Update, Documents will be saved', 'custom-upload-tag' ); ?></small></h3>

            <span class="added_file_names"></span>
        </div>
        <p>
            <a href="#" class="dominant-upload button button-primary button-large"><?php _e( 'Add Documents', 'custom-upload-tag' ); ?></a>
        </p>
    </div>

<?php }

add_action( 'admin_enqueue_scripts', 'dominant_include_js' );

function dominant_include_js() {

    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }

    wp_enqueue_script( 'upload_script', plugin_dir_url( __FILE__ ) . 'assets/js/script.js', array( 'jquery' ) );
}

add_action('save_post', 'save_file_to_database');
function save_file_to_database($post_id) {
    if(isset($_POST['detach_all']) AND $_POST['detach_all'] == 'yes') {
        delete_post_meta($post_id, '_dominant_document');
    } else {
        if(isset($_POST['dominant_document'])) {
            $image_data = json_decode( stripslashes( $_POST['dominant_document'] ) );
            if($image_data) {
                $data_array = [];
                foreach($image_data as $value) {
                    $data_array[] = array('id' => $value->id, 'url' => esc_url_raw($value->url), 'filename' => $value->filename);
                }
            }

            $documents = get_post_meta($post_id, '_dominant_document');
            if($documents) {
                foreach($documents[0] as $document) {
                    $data_array[] = array('id' => $document['id'], 'url' => esc_url_raw($document['url']), 'filename' => $document['filename']);
                }
            }

            update_post_meta($post_id, '_dominant_document', $data_array);
        }
    }

}

add_filter( 'woocommerce_display_product_attributes', 'display_document_list_in_additional_info_table', 10, 2 );
function display_document_list_in_additional_info_table( $product_attributes, $product ) {
    $documents = get_post_meta($product->get_id(), '_dominant_document');

    if($documents) {
        $list = '';
        foreach($documents[0] as $document) {
            $list .= '<li><a href="' . $document['url'] . '" target="_blank">' . $document['filename'] . '</a></li>';
        }

        $product_attributes[ 'attribute_' . 'documents' ] = array(
            'label' => __('Documents', 'custom-upload-tag'),
            'value' => '<ul>' . $list . '</ul>',
        );

        return $product_attributes;
    }

}