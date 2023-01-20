<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JobSecurity
{
    public static function init(){
        
        // do redirect if someone tryes to access attachment page
        add_action( 'template_redirect', array('JobSecurity', 'attachments_redirect'), 1 );
    }


    public static function attachments_redirect() {
        global $post;

        if( !$post ) return;

        $att = get_post_meta($post->ID, 'jobs_plugin_attachment', true);

        if ( is_attachment() && isset( $post->post_parent ) && is_numeric( $post->post_parent ) && ( $post->post_parent != 0 ) && ( $att != '' && $att == 'true' ) ) {

            $parent_post_in_trash = get_post_status( $post->post_parent ) === 'trash' ? true : false;

            if ( $parent_post_in_trash ) {
                    wp_die( 'Page not found.', '404 - Page not found', 404 );
            }

            $disable_redirect = apply_filters('job-entry/attachment_page_redirect', true);

            if($disable_redirect){
                wp_safe_redirect( get_permalink( $post->post_parent ), '301' );
                exit;
            }

        }
        elseif ( is_attachment() && isset( $post->post_parent ) && is_numeric( $post->post_parent ) && ( $post->post_parent < 1 ) ) {

            $disable_redirect = apply_filters('job-entry/noparent_attachment_page_redirect', true);

            if($disable_redirect){
                wp_safe_redirect( get_bloginfo( 'wpurl' ), '302' );
                exit;
            }
        }


    }

}