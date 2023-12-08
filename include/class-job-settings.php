<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JobSettings
{

    public static function init(){
        add_action( 'admin_init', array('JobSettings', 'register_settings') );
        
		add_action( 'admin_menu', array('JobSettings', 'register_submenus') );
    }

    public static function register_submenus() {

		add_submenu_page('edit.php?post_type=jobs', __('Jobs for Wordpress - Settings','job-postings'), __('Settings','job-postings'), 'edit_pages', 'jp-settings', array('JobSettings', 'render_settings_page'));
		
		add_submenu_page('edit.php?post_type=jobs', __('Jobs for Wordpress - Help','job-postings'), __('Help','job-postings'), 'edit_pages', 'jp-help', array('JobSettings', 'render_help_page'));

    }


    public static function register_settings() {
        
        foreach(Job_Postings::$languages as $lang => $lng){

            //register_setting( 'jobs_options_'.$lang , 'jobs_settings_last_screen'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_confirmation_text'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_currency_position'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_currency_symbol'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_hiring_organization'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_preview_cta'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_archive_page'.'_'.$lang, array('JobSettings', 'update_archive_page') );
            register_setting( 'jobs_options_'.$lang , 'jobs_custom_slug'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_no_jobs_message'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_offer_ended_message'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_offer_ended_message_enabled'.'_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_apply_advanced_'.$lang );
            register_setting( 'jobs_options_'.$lang , 'jobs_filesize_validation_'.$lang );
        }

        // Global values
        //register_setting( 'jobs_options' , 'jobs_settings_last_screen' );

        register_setting( 'jobs_options' , 'jobs_schema_type' );
        register_setting( 'jobs_options' , 'jobs_company_logo' );
        register_setting( 'jobs_options' , 'jobs_sidebar_position' );
        register_setting( 'jobs_options' , 'jobs_posts_per_page' );
        register_setting( 'jobs_options' , 'jobs_hide_letter_upload' );
        register_setting( 'jobs_options' , 'jobs_hide_cv_upload' );
        register_setting( 'jobs_options' , 'jobs_default_email' );
        register_setting( 'jobs_options' , 'jobs_selected_schema' );

        register_setting( 'jobs_options' , 'jobs_file_storage' );
        register_setting( 'jobs_options' , 'jobs_max_filesize' );

        register_setting( 'jobs_options' , 'jobs_dont_store_user_data' );
        

        register_setting( 'jobs_options' , 'jobs_recaptcha_type' );
        register_setting( 'jobs_options' , 'jobs_recaptcha_site_key' );
        register_setting( 'jobs_options' , 'jobs_recaptcha_secret_key' );

        register_setting( 'jobs_options' , 'jobs_default_field_selection' );

        register_setting( 'jobs_options' , 'jobs_apply_advanced' );

        // Color values
        register_setting( 'jobs_options' , 'jobs_button_bg_color' );
        register_setting( 'jobs_options' , 'jobs_button_bg_color_hover' );
        register_setting( 'jobs_options' , 'jobs_button_text_color' );
        register_setting( 'jobs_options' , 'jobs_heading_text_color' );
        register_setting( 'jobs_options' , 'jobs_subheading_text_color' );
        register_setting( 'jobs_options' , 'jobs_list_item_bg' );
        register_setting( 'jobs_options' , 'jobs_list_item_border' );
        register_setting( 'jobs_options' , 'jobs_content_heading_color' );
        register_setting( 'jobs_options' , 'jobs_content_text_color' );
        register_setting( 'jobs_options' , 'jobs_button_roundness' );
        register_setting( 'jobs_options' , 'jobs_box_roundness' );
        register_setting( 'jobs_options' , 'jobs_filters_styles' );

        register_setting( 'jobs_options' , 'jobs_preview_location' );
        register_setting( 'jobs_options' , 'jobs_preview_employment_type' );

        register_setting( 'jobs_options' , 'jobs_custom_css' );

    } 

    public static function update_archive_page( $input ){

        $current_archive_page = get_option('jobs_archive_page'.'_'.Job_Postings::$lang);

        if( $current_archive_page == $input ) return $input;

        $args = array(
            'post_type' => 'jobs',
            'post_status' => 'any',
            'posts_per_page' => -1
        );

        $jobs = get_posts($args);
        if( $jobs ){
            foreach ($jobs as $key => $job) {
                wp_update_post( array( 'ID' => $job->ID, 'post_parent' => $input ) );
            }
        }
        

        return $input;
    }
    
    public static function render_settings_page() {
        include_once( JOBPOSTINGSPATH . 'admin/settings.php');
    }
    
    public static function render_help_page() {
        include_once( JOBPOSTINGSPATH . 'admin/help.php');
    }
}
