<?php

class JobDependencies
{
    public static function include_scripts(){
		// admin
		if( is_admin() ) {
			add_action('admin_head', array('JobDependencies', 'admin_enqueue_scripts') );
		}else{
			add_action(apply_filters('jobs-front-scripts-include', 'wp_footer'), array('JobDependencies', 'front_enqueue_scripts') );
		}
	}

	public static function admin_enqueue_scripts(){

		$ver = '?v=' . JOBPOSTINGSVERSION;

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-datepicker');



		$dir = JOBPOSTINGSURL;

		// register & include CSS
		wp_register_style('jp-admin-styles', "{$dir}admin/css/style.css".$ver);
		wp_enqueue_style('jp-admin-styles');

		wp_register_style('jp-admin-colorpicker', "{$dir}admin/css/colorpicker.css".$ver);
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
		wp_register_script('jp-admin-colorpicker-js', "{$dir}admin/js/colorpicker.js".$ver, array('jquery'));
		wp_enqueue_script('jp-admin-colorpicker-js');

		wp_register_script('jp-admin-repeater-js', "{$dir}admin/js/jquery.repeater.min.js".$ver, array('jquery'));
		wp_enqueue_script('jp-admin-repeater-js');


		// register & include JS
		wp_register_script('jp-admin-scripts', "{$dir}admin/js/script.js".$ver, array('jquery', 'jquery-autogrow', 'jquery-match-height'));
		wp_enqueue_script('jp-admin-scripts');

		$localized				    = array();
		$localized['ajaxurl'] 	    = admin_url( 'admin-ajax.php' );
		$localized['no_name']	    = __('Please input template name.', 'acf-ft');
		$localized['date_format']	= Job_Postings_Helper::dateformat_PHP_to_jQueryUI(get_option('date_format'));

		wp_localize_script('jp-admin-scripts', 'jpsd', $localized );

    }
    

	public static function front_enqueue_scripts(){

		$ver = '?v=' . JOBPOSTINGSVERSION;

		$dir = JOBPOSTINGSURL;
		
		$site_key 	= get_option( 'jobs_recaptcha_site_key' );
		$secret_key = get_option( 'jobs_recaptcha_secret_key' );
		$re_type 	= get_option( 'jobs_recaptcha_type' );

		wp_enqueue_script('jquery');


		
        wp_register_script('jp-front-select2', "{$dir}js/select2.min.js".$ver);
		wp_enqueue_script('jp-front-select2');

		// register & include JS
		
		// if( $re_type == 'on' ){ 
		// 	if($site_key && $secret_key) wp_register_script('jp-google-recaptcha', "https://www.google.com/recaptcha/api.js?render=".$site_key);
		// }else{
		// 	if($site_key && $secret_key) wp_register_script('jp-google-recaptcha', "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit");
		// }
		// wp_enqueue_script('jp-google-recaptcha');

		do_action('job-postings/front_enqueue_scripts');
		
        wp_register_script('jp-front-scripts', "{$dir}js/script.js".$ver);
        wp_enqueue_script('jp-front-scripts');

        $localized					= array();
        $localized['ajaxurl'] 		= admin_url( 'admin-ajax.php' );
		$localized['no_name']		= __('Please input template name.', 'job-postings');
		$localized['site_key']		= $site_key;
		$localized['re_type']		= $re_type;
		$localized['re_message']  	= __('Ups... reCaptcha identified you as a bot. If your not, please reload and try again.', 'job-postings');

		$filesize_placeholder 	    = __('File %2$s exceeds the allowed file size of %1$s MB.', 'job-postings');
		$filesize_exceeded 		    = get_option( 'jobs_filesize_validation_'.Job_Postings::$lang );
		$filesize_exceeded 		    = $filesize_exceeded ? $filesize_exceeded : $filesize_placeholder;
        $localized['validation']	= $filesize_exceeded;

		$localized['max_filesize'] 	= get_option( 'jobs_max_filesize' ) ? get_option( 'jobs_max_filesize' ) : 10; //Defaults to 10MB


        wp_localize_script('jp-front-scripts', 'jpsd', $localized );

		// register & include CSS
		
        wp_register_style('jp-front-select2', "{$dir}css/select2.min.css".$ver);
		wp_enqueue_style('jp-front-select2');

        wp_register_style('jp-front-styles', "{$dir}css/style.css".$ver);
        wp_enqueue_style('jp-front-styles');


		wp_enqueue_style('select2');
	}
	
	public static function reCaptchaScripts(){
		$site_key 	= get_option( 'jobs_recaptcha_site_key' );
		$secret_key = get_option( 'jobs_recaptcha_secret_key' );
		$re_type 	= get_option( 'jobs_recaptcha_type' );

		// register & include JS
		if( $re_type == 'on' ){ 
			if($site_key && $secret_key) wp_register_script('jp-google-recaptcha', "https://www.google.com/recaptcha/api.js?render=".$site_key);
		}else{
			if($site_key && $secret_key) wp_register_script('jp-google-recaptcha', "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit");
		}
		wp_enqueue_script('jp-google-recaptcha');
	}

}
