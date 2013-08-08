<?php

/*
Plugin Name: Hubbard Radio STL Eblast Creator
Plugin URI: http://www.hubbardradiostl.com
Description: Fully create an easily send eblasts to Triton's Tribal Direct. Simply upload the images, add a link and add a text version of the image, and this plugin will return the HTML and Text version of the eblast.
Version: 1.0
Author: marc.palmer
Author URI: http://www.palmermarc.com

------------------------------------------------------------------------

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

define( 'PLUGIN_VERSION', '1.0' );
define( '_HRI_EBLASTS_PATH', dirname( __FILE__ ) );

class MPEblasts {
    
    function __construct() {
        // Register the eBlasts post type and prepare the meta box
        add_action( 'init', array( $this, '_register_eblast_post_type' ), 0 );
        
        // Register the custom taxonomies
        add_action( 'init', array( $this, '_register_station_taxonomy' ), 0 );
        
        // Redirect the eblast templates
        add_filter( 'template_include', array( $this, '_eblast_template_redirect' ), 1 );
        
        add_action( 'load-post.php', array( $this, '_load_eblast_meta_box' ), 10 );
        add_action( 'load-post-new.php', array( $this, '_load_eblast_meta_box' ), 10 );
        
        add_action( 'admin_enqueue_scripts', array( $this, '_add_eblast_js' ) );
    }
    
    function _add_eblast_js() {
        // Enqueue the styling
        wp_enqueue_style( 'thickbox' );
        wp_enqueue_style( 'hri-eblast-css', plugins_url( '/css/hri-eblast.css', __FILE__ ) );
        
        // Enqueue the scripts
        wp_enqueue_script('media-upload');
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'hri-eblasts-js', plugins_url( '/js/hri-eblast.js', __FILE__ ), array( 'jquery' ) );
    }
    
    function _register_eblast_post_type() {
        $labels = array(
            'name'                => 'Eblasts',
            'singular_name'       => 'Eblast',
            'menu_name'           => 'Eblast',
            'parent_item_colon'   => 'Parent Eblast:',
            'all_items'           => 'All Eblasts',
            'view_item'           => 'View Eblast',
            'add_new_item'        => 'Add New Eblast',
            'add_new'             => 'New Eblast',
            'edit_item'           => 'Edit Eblast',
            'update_item'         => 'Update Eblast',
            'search_items'        => 'Search eblasts',
            'not_found'           => 'No eblasts found',
            'not_found_in_trash'  => 'No eblasts found in Trash',
        );
        $args = array(
            'labels'              => $labels,
            'supports'            => array( 'custom-fields', 'title', 'author', ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 100,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
        );
        
        register_post_type( 'eblast', $args );
    }

    // Register Custom Taxonomy
    function _register_station_taxonomy()  {
    
        $labels = array(
            'name'                       => 'Stations',
            'singular_name'              => 'Station',
            'menu_name'                  => 'Station',
            'all_items'                  => 'All Stations',
            'parent_item'                => 'Parent Station',
            'parent_item_colon'          => 'Parent Station:',
            'new_item_name'              => 'New Station Name',
            'add_new_item'               => 'Add New Station',
            'edit_item'                  => 'Edit Station',
            'update_item'                => 'Update Station',
            'separate_items_with_commas' => 'Separate stations with commas',
            'search_items'               => 'Search stations',
            'add_or_remove_items'        => 'Add or remove stations',
            'choose_from_most_used'      => 'Choose from the most used stations',
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'station', 'eblast', $args );
    
    }


    function _load_eblast_meta_box() {
        add_action( 'add_meta_boxes', array( $this, '_add_eblast_meta_box' ), 10 );
        add_action( 'save_post', array( $this, '_save_eblast_meta_box' ), 10, 2 );
    }

 /* This function adds the talent fee meta box to events */
    function _add_eblast_meta_box() {
        add_meta_box( 'station-eblast-leaderboard', esc_html__( 'Station eBlast 728x90 Leaderboard', 'station-eblasts' ), array( 'MPEblasts', '_eblast_leaderboard_meta_box' ), 'eblast', 'normal', 'high' );
        add_meta_box( 'station-eblast-featured', esc_html__( 'Station eBlast Featured', 'station-eblasts' ), array( 'MPEblasts', '_eblast_featured_meta_box' ), 'eblast', 'normal', 'high' );
        add_meta_box( 'station-eblast-core-items', esc_html__( 'Station eBlast Core Items', 'station-eblasts' ), array( 'MPEblasts', '_eblast_core_meta_box' ), 'eblast', 'normal', 'high' );
        add_meta_box( 'station-eblast-left-ad', esc_html__( 'Station eBlast Left 364x160 Ad', 'station-eblasts' ), array( 'MPEblasts', '_eblast_footer_left_meta_box' ), 'eblast', 'normal', 'high' );
        add_meta_box( 'station-eblast-right-ad', esc_html__( 'Station eBlast Right 364x160 Ad', 'station-eblasts' ), array( 'MPEblasts', '_eblast_footer_right_meta_box' ), 'eblast', 'normal', 'high' );
    }

    function _eblast_leaderboard_meta_box( $object, $box ) {
        wp_nonce_field( basename( __FILE__ ), 'eblast-meta-box' );
        $leaderboard_image = get_post_meta( $object->ID, 'leaderboard_image', TRUE );
        $leaderboard_link = get_post_meta( $object->ID, 'leaderboard_link', TRUE );
        $leaderboard_text = get_post_meta( $object->ID, 'leaderboard_text', TRUE );
        ?>
        <table id="eblast_header" cellpadding="5" border="0" cellspacing="5">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td class="thead">
                        <div id="leaderboard_image_preview"><?php if( $leaderboard_image ) echo '<img src="'.esc_url( $leaderboard_image ).'" alt="" title="" />'; ?></div>
                    </td>
                </tr>
                <tr>
                    <td><label for="leaderboard_image">Image: </label></td>
                    <td>
                        <input class="eblast_image" size="85" type="text" id="leaderboard_image" name="leaderboard_image" value="<?php echo esc_url( $leaderboard_image ); ?>" />
                        <input class="upload_image_button" type="button" value="Upload Image" />
                    </td>
                </tr>
                <tr>
                    <td><label for="leaderboard_link">Link: </label></td>
                    <td><input size="85" type="text" name="leaderboard_link" value="<?php echo esc_url( $leaderboard_link ); ?>" /></td>
                </tr>
                <tr>
                    <td><label for="leaderboard_text">Text: </label></td>
                    <td><textarea cols="65" rows="5" name="leaderboard_text"><?php echo esc_html( $leaderboard_text ); ?></textarea></td>
                </tr>
            </tbody>
        </table>
        <div class="shim"></div>
        <?php
    }

    function _eblast_featured_meta_box( $object, $box ) {
        wp_nonce_field( basename( __FILE__ ), 'eblast-meta-box' );
        $featured_image = get_post_meta( $object->ID, 'featured_image', TRUE );
        $featured_link = get_post_meta( $object->ID, 'featured_link', TRUE );
        $featured_text = get_post_meta( $object->ID, 'featured_text', TRUE );
        ?>
        <table id="eblast_header" cellpadding="5" border="0" cellspacing="5">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td class="thead">
                        <div id="featured_image_preview"><?php if( $featured_image ) echo '<img src="'. esc_url( $featured_image ).'" alt="" title="" />'; ?></div>
                    </td>
                </tr>
                <tr>
                    <td><label for="featured_image">Image: </label></td>
                    <td>
                        <input class="eblast_image" size="85" type="text" id="featured_image" name="featured_image" value="<?php echo esc_url( $featured_image ); ?>" />
                        <input class="upload_image_button" type="button" value="Upload Image" />
                    </td>
                </tr>
                <tr>
                    <td><label for="featured_image">Link: </label></td>
                    <td><input size="85" type="text" name="featured_link" value="<?php echo esc_url( $featured_link ); ?>" /></td>
                </tr>
                <tr>
                    <td><label for="featured_text">Text: </label></td>
                    <td><textarea cols="65" rows="5" name="featured_text"><?php echo esc_html( $featured_text ); ?></textarea></td>
                </tr>
            </tbody>
        </table>
        <div class="shim"></div>
        <?php
    }
    
    function _eblast_core_meta_box( $object, $box ) {
        wp_nonce_field( basename( __FILE__ ), 'eblast-meta-box' );
        $core_items = ( get_post_meta( $object->ID, 'core_items', TRUE ) ) ? get_post_meta( $object->ID, 'core_items', TRUE ) : '[{"image":"","link":"","text":""}]';
        $core_items = str_replace( "\'", "/'", $core_items );
        $core_items = json_decode( $core_items  );
        
        ?>
        <table id="eblast_core_items" cellpadding="0" cellspacing="0" border="0">
            <tbody>
                <?php foreach( $core_items as $item_id => $item ) : ?>
                <tr class="item" id="item_<?php echo $item_id; ?>">
                    <td>&nbsp;</td>
                    <td class="thead">
                        <div id="core_items_<?php echo $item_id; ?>_image_preview"><br />
                            <?php if( $item->image ) echo '<img src="'. esc_url( $item->image ).'" alt="" title="" />'; ?>
                        </div>
                    </td>
                </tr>
                <tr class="item_image" id="item_<?php echo $item_id; ?>_image">
                    <td><label for="core_items_<?php echo $item_id; ?>_image">Image: </label></td>
                    <td>
                        <input class="eblast_image" size="85" type="text" id="core_items_<?php echo $item_id; ?>_image" name="core_items[<?php echo $item_id; ?>][image]" value="<?php echo esc_url( $item->image ); ?>" />
                        <input class="upload_image_button" type="button" value="Upload Image" />
                    </td>
                </tr>
                <tr class="item_link" id="item_<?php echo $item_id; ?>_link">
                    <td><label for="core_items[<?php echo $item_id; ?>][link]">Link: </label></td>
                    <td><input size="85" type="text" name="core_items[<?php echo $item_id; ?>][link]" value="<?php echo esc_url( $item->link ); ?>" /></td>
                </tr>
                <tr class="item_text" id="item_<?php echo $item_id; ?>_text">
                    <td><label for="core_items[<?php echo $item_id; ?>][text]">Text: </label></td>
                    <td><textarea cols="65" rows="5" name="core_items[<?php echo $item_id; ?>][text]"><?php echo esc_html( $item->text ); ?></textarea></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p id="add_core_item">Add Another Item +</p>
        <script>
            jQuery(document).ready(function() {
                jQuery('#add_core_item').click(function() {
                    var itemcount = jQuery('#eblast_core_items .item').length;
                    
                    jQuery('#eblast_core_items').append(
                        '<tr class="item" id="item_' + itemcount + '">' +
                            '<td>&nbsp;</td>' +
                            '<td class="thead">'+
                                '<div id="core_items_' + itemcount + '_image_preview"></div>' +
                            '</td>' +
                        '</tr>' +
                        '<tr class="item_image" id="item_'+itemcount+'_image">'+
                            '<td><label for="core_items_' + itemcount + '_image">Image: </label></td>' +
                            '<td>' +
                                '<input class="eblast_image" size="85" type="text" id="core_items_' + itemcount + '_image" name="core_items[' + itemcount + '][image]" value="" />' +
                                '<input class="upload_image_button" type="button" value="Upload Image" />' +
                            '</td>' +
                        '</tr>' +
                        '<tr class="item_link" id="item_' + itemcount + '_link">' +
                            '<td><label for="core_items[' + itemcount + '][image]">Link: </label></td>' +
                            '<td><input size="85" type="text" name="core_items[' + itemcount + '][link]" value="" /></td>' +
                        '</tr>' +
                        '<tr class="item_text" id="item_' + itemcount + '_text">' +
                            '<td><label for="core_items[' + itemcount + '][text]">Text: </label></td>' +
                            '<td><textarea cols="65" rows="5" name="core_items[' + itemcount + '][text]"></textarea></td>' +
                        '</tr>'
                    );
                });
            });
        </script>
        <?php
    }
    
    function _eblast_footer_left_meta_box( $object, $box ) {
        wp_nonce_field( basename( __FILE__ ), 'eblast-meta-box' );
        $footer_left_image = get_post_meta( $object->ID, 'footer_left_image', TRUE );
        $footer_left_link = get_post_meta( $object->ID, 'footer_left_link', TRUE );
        $footer_left_text = get_post_meta( $object->ID, 'footer_left_text', TRUE );
        ?>
        <table id="eblast_header" cellpadding="5" border="0" cellspacing="5">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td class="thead">
                        <div id="footer_left_image_preview"><?php if( $footer_left_image ) echo '<img src="'. esc_url( $footer_left_image ).'" alt="" title="" />'; ?></div>
                    </td>
                </tr>
                <tr>
                    <td><label for="footer_left_image">Image: </label></td>
                    <td>
                        <input class="eblast_image" size="85" type="text" id="footer_left_image" name="footer_left_image" value="<?php echo esc_url( $footer_left_image ); ?>" />
                        <input class="upload_image_button" type="button" value="Upload Image" />
                    </td>
                </tr>
                <tr>
                    <td><label for="footer_left_image">Link: </label></td>
                    <td><input size="85" type="text" name="footer_left_link" value="<?php echo esc_url( $footer_left_link ); ?>" /></td>
                </tr>
                <tr>
                    <td><label for="footer_left_text">Text: </label></td>
                    <td><textarea cols="65" rows="5" name="footer_left_text"><?php echo esc_html( $footer_left_text ); ?></textarea></td>
                </tr>
            </tbody>
        </table>
        <div class="shim"></div>
        <?php
    }
    
    function _eblast_footer_right_meta_box( $object, $box ) {
        wp_nonce_field( basename( __FILE__ ), 'eblast-meta-box' );
        $footer_right_image = get_post_meta( $object->ID, 'footer_right_image', TRUE );
        $footer_right_link = get_post_meta( $object->ID, 'footer_right_link', TRUE );
        $footer_right_text = get_post_meta( $object->ID, 'footer_right_text', TRUE );
        ?>
        <table id="eblast_header" cellpadding="5" border="0" cellspacing="5">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td class="thead">
                        <div id="footer_right_image_preview"><?php if( $footer_right_image ) echo '<img src="'. esc_url( $footer_right_image ).'" alt="" title="" />"'; ?></div>
                    </td>
                </tr>
                <tr>
                    <td><label for="footer_right_image">Image: </label></td>
                    <td>
                        <input class="eblast_image" size="85" type="text" id="footer_right_image" name="footer_right_image" value="<?php echo esc_url( $footer_right_image ); ?>" />
                        <input class="upload_image_button" type="button" value="Upload Image" />
                    </td>
                </tr>
                <tr>
                    <td><label for="footer_right_image">Link: </label></td>
                    <td><input size="85" type="text" name="footer_right_link" value="<?php echo esc_url( $footer_right_link ); ?>" /></td>
                </tr>
                <tr>
                    <td><label for="footer_right_text">Text: </label></td>
                    <td><textarea cols="65" rows="5" name="footer_right_text"><?php echo esc_html( $footer_right_text ); ?></textarea></td>
                </tr>
            </tbody>
        </table>
        <div class="shim"></div>
        <?php
    }

    function _save_eblast_meta_box( $post_id, $post ) {
        
        /* Verify the nonce before proceeding. */
        if( !isset( $_POST['eblast-meta-box'] ) || !wp_verify_nonce( $_POST['eblast-meta-box'], basename( __FILE__ ) ) )
            return $post_id;
        
        $core_items = array();
        
        foreach( $_POST['core_items'] as $item_id => $item_val ) :
            $core_items[$item_id]['image'] = sanitize_text_field( $item_val['image'] );
            $core_items[$item_id]['link'] = sanitize_text_field( $item_val['link'] );
            $core_items[$item_id]['text'] = sanitize_text_field( $item_val['text'] );
        endforeach;
        
        $metaKeys = array( 
            'leaderboard_image' => sanitize_text_field( $_POST['leaderboard_image'] ),
            'leaderboard_link' => sanitize_text_field( $_POST['leaderboard_link'] ),
            'leaderboard_text' => sanitize_text_field( $_POST['leaderboard_text'] ),
            'featured_image' => sanitize_text_field( $_POST['featured_image'] ),
            'featured_link' => sanitize_text_field( $_POST['featured_link'] ),
            'featured_text' => sanitize_text_field( $_POST['featured_text'] ),
            'footer_left_image' => sanitize_text_field( $_POST['footer_left_image'] ),
            'footer_left_link' => sanitize_text_field( $_POST['footer_left_link'] ),
            'footer_left_text' => sanitize_text_field( $_POST['footer_left_text'] ),
            'footer_right_image' => sanitize_text_field( $_POST['footer_right_image'] ),
            'footer_right_link' => sanitize_text_field( $_POST['footer_right_link'] ),
            'footer_right_text' => sanitize_text_field( $_POST['footer_right_text'] ),
            'core_items' => json_encode( $core_items, JSON_FORCE_OBJECT ),
        ); 
        
        foreach( $metaKeys as $meta_key => $new_meta_value ) :
            
            if( isset( $_POST[ $meta_key ] ) )
                update_post_meta( $post_id, $meta_key, $new_meta_value );

        endforeach;
    }

    function _eblast_template_redirect( $template_path ) {
        
        if ( get_post_type() == 'eblast' ) {
            
            if ( is_single() ) {
                $stations = wp_get_post_terms( get_the_ID(), 'station' );
                $station_file =  _HRI_EBLASTS_PATH . '/templates/' . $stations[0]->slug . '-eblast.php';
                
                if ( $theme_file = locate_template( array ( 'single-eblast.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    if( is_file( $station_file ) ) {
                        $template_path = $station_file;
                    } else {
                        $template_path = _HRI_EBLASTS_PATH.'/templates/single-eblast.php';
                    }
                }
            }

            if ( is_archive() ) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ( $theme_file = locate_template( array ( 'archive-eblast.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = _HRI_EBLASTS_PATH . '/templates/archive-eblast.php';
                }
            } 
            
        }
        
        return $template_path;
    }

}

new MPEblasts();
