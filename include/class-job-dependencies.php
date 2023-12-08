<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JobDependencies
{
    public static function include_scripts(){
		// admin
		if( is_admin() ) {
			add_action('admin_head', array('JobDependencies', 'admin_enqueue_scripts') );
		}else{
			add_action(apply_filters('jobs-front-scripts-include', 'wp_enqueue_scripts'), array('JobDependencies', 'front_enqueue_scripts') );
		}
	}

	public static function admin_enqueue_scripts(){

		$ver = JOBPOSTINGSVERSION;

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-datepicker');



		$dir = JOBPOSTINGSURL;

		// register & include CSS
		wp_register_style('jp-admin-styles', "{$dir}admin/css/style.css", array() , $ver );
		wp_enqueue_style('jp-admin-styles');

		wp_register_style('jp-admin-colorpicker', "{$dir}admin/css/colorpicker.css", array() , $ver);
		wp_enqueue_style('jp-admin-colorpicker');


		wp_register_script('jquery-autogrow', "{$dir}admin/js/jquery-autogrow.js");
		wp_enqueue_script('jquery-autogrow');

		wp_register_script('jquery-match-height', "{$dir}admin/js/jquery-match-height.js");
		wp_enqueue_script('jquery-match-height');

		wp_register_style('jp-admin-ui', "{$dir}admin/css/jquery-ui.css");
		wp_enqueue_style('jp-admin-ui');

		wp_register_style('jp-admin-ui-styles', "{$dir}admin/css/jquery-ui.theme.min.css");
		wp_enqueue_style('jp-admin-ui-styles');


		// register & include JS
		wp_register_script('jp-admin-colorpicker-js', "{$dir}admin/js/colorpicker.js", array('jquery'), $ver );
		wp_enqueue_script('jp-admin-colorpicker-js');

		wp_register_script('jp-admin-repeater-js', "{$dir}admin/js/jquery.repeater.min.js", array('jquery'), $ver );
		wp_enqueue_script('jp-admin-repeater-js');


		// register & include JS
		wp_register_script('jp-admin-scripts', "{$dir}admin/js/script.js", array('jquery', 'jquery-autogrow', 'jquery-match-height'), $ver );
		wp_enqueue_script('jp-admin-scripts');

		$localized				    = array();
		$localized['ajaxurl'] 	    = admin_url( 'admin-ajax.php' );
		$localized['no_name']	    = __('Please input template name.', 'acf-ft');
		$localized['date_format']	= 'dd.mm.yy';//Job_Postings_Helper::dateformat_PHP_to_jQueryUI(get_option('date_format'));

		wp_localize_script('jp-admin-scripts', 'jpsd', $localized );

    }
    

	public static function front_enqueue_scripts(){

		$ver = JOBPOSTINGSVERSION;

		$dir = JOBPOSTINGSURL;
		
		$site_key 	= get_option( 'jobs_recaptcha_site_key' );
		$secret_key = get_option( 'jobs_recaptcha_secret_key' );
		$re_type 	= get_option( 'jobs_recaptcha_type' );

		wp_register_script('jp-front-select2', "{$dir}js/select2.min.js", array(), $ver );
		
		// register & include JS
		
		// if( $re_type == 'on' ){ 
		// 	if($site_key && $secret_key) wp_register_script('jp-google-recaptcha', "https://www.google.com/recaptcha/api.js?render=".$site_key);
		// }else{
		// 	if($site_key && $secret_key) wp_register_script('jp-google-recaptcha', "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit");
		// }
		// wp_enqueue_script('jp-google-recaptcha');

		do_action('job-postings/front_enqueue_scripts');
		
        wp_register_script('jp-front-scripts', "{$dir}js/script.js", array(), $ver);

        $localized					= array();
        $localized['ajaxurl'] 		= admin_url( 'admin-ajax.php' );
		$localized['no_name']		= __('Please input template name.', 'job-postings');
		$localized['site_key']		= $site_key;
		$localized['re_type']		= $re_type;
		$localized['re_message']  	= __('Ups... reCaptcha identified you as a bot. If you are not, please reload and try again.', 'job-postings');
		$localized['Not_valid_phone_number']  		= __('Not valid phone number', 'job-postings');
		$localized['Not_valid_email']  				= __('Not valid email', 'job-postings');
		$localized['Error_sending_notification'] 	= __('Error sending notification message, but dont worry, We saved Your data securelly. Thank you.', 'job-postings');
		$localized['Not_valid_file_type'] 	= __('Not a valid file-type selected', 'job-postings');
		$filesize_placeholder 	    = __('File %2$s exceeds the allowed file size of %1$s MB.', 'job-postings');
		$filesize_exceeded 		    = get_option( 'jobs_filesize_validation_'.Job_Postings::$lang );
		$filesize_exceeded 		    = $filesize_exceeded ? $filesize_exceeded : $filesize_placeholder;
        $localized['validation']	= $filesize_exceeded;
		$localized['max_filesize'] 	= get_option( 'jobs_max_filesize' ) ? get_option( 'jobs_max_filesize' ) : 10; //Defaults to 10MB
        wp_localize_script('jp-front-scripts', 'jpsd', $localized );

		// register & include CSS
        wp_register_style('jp-front-select2', "{$dir}css/select2.min.css", array(), $ver );
        wp_register_style('jp-front-styles', "{$dir}css/style.css", array(), $ver );

		
		// register & include JS
		$site_key 	= get_option( 'jobs_recaptcha_site_key' );
		$secret_key = get_option( 'jobs_recaptcha_secret_key' );
		$re_type 	= get_option( 'jobs_recaptcha_type' );
		if( $site_key && $secret_key ){
			if( $re_type == 'on' ){ 
				if($site_key && $secret_key) wp_register_script('jp-google-recaptcha', "https://www.google.com/recaptcha/api.js?render=".$site_key);
			}else{
				if($site_key && $secret_key) wp_register_script('jp-google-recaptcha', "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit");
			}
		}
	}
	

}
