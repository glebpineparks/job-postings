<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JobApplicationSubmit
{
    public static function init(){
        add_filter('job-postings/email/merge_tags', array('JobNotifications', 'convertMergeFields'), 10, 4);
        
		add_action( 'wp_ajax_jobslisting_apply_now', array('JobApplicationSubmit', 'ajax_submit') );
        add_action( 'wp_ajax_nopriv_jobslisting_apply_now', array('JobApplicationSubmit', 'ajax_submit') );
    }


    public static function ajax_submit(){
        
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $posted_data 	= isset( $_POST ) ? $_POST : array();
        $file_data 		= isset( $_FILES ) ? $_FILES : array();
        $data 				= array_merge( $posted_data, $file_data );

        // if honeypot is touched, return without saving
        $honeypot 		= strip_tags($_POST['input_honeypot']);
        $honeypot 		= sanitize_title($honeypot);
        if( $honeypot != '' ) {
            echo 'Cheating?';
            die();
        }

        $store_user_data = get_option( 'jobs_dont_store_user_data' );

        $site_key   = get_option( 'jobs_recaptcha_site_key' );
        $secret_key = get_option( 'jobs_recaptcha_secret_key' );
        $re_type    = get_option( 'jobs_recaptcha_type' );
        $apply_advanced         = get_option( 'jobs_apply_advanced' );

        if( isset( $file_data ) ){
            $chosenFileType = [];
            foreach( $file_data as $file ){
                $extension = substr($file['name'], strrpos($file['name'], '.') + 1);
                $chosenFileType[] = $extension;
            }

            if( isset($chosenFileType) ){
                $chosenFileType = array_unique($chosenFileType);
            }

            $collectAcceptedFiles = array_map(function( $param ){
                $setVal = '';
                if( !empty($param['multi_files_accepted']) ){
                    $setVal = $param['multi_files_accepted'];
                }

                if( !empty($param['files_accepted']) ){
                    $setVal = $param['files_accepted'];
                }

                return $setVal;

            }, $apply_advanced['modal']);

            // Use array_filter to remove indexes with empty 'multi_files_accepted' values
            $filteredData = array_filter($collectAcceptedFiles, function ($item) {
                return !empty($item);
            });

            
            if( isset($filteredData) && !empty($filteredData) ){
                $accepted_types = implode(", ", $filteredData);
                foreach( $chosenFileType as $type ){
                    if( strpos($accepted_types, $type) === false ){
                        echo json_encode( array('status' => 'error', 'messages' => array('Not_valid_file_type') ) );
                        die();
                    }
                }
            }
        }
        
        if( $site_key && $secret_key) {
            $data = array(
				'secret' => $secret_key,
				'response' => strip_tags($_POST['captcha_response'])
			);

			$verify = curl_init();
			curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
			curl_setopt($verify, CURLOPT_POST, true);
			curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
			//curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
			$verify_response = curl_exec($verify);
			curl_close($verify);

            $captcha_success = json_decode($verify_response);

            if ( $captcha_success && $captcha_success->success == false ) {
                echo json_encode( array('status' => 'error', 'messages' => array('recaptcha_not_valid') ) );
                die();
            }
        }


        $pre_post_id 	    = strip_tags($_POST['post_id']);
        $post_id 			= sanitize_title($pre_post_id);

        $current_language = isset($_POST['language']) ? strip_tags($_POST['language']) : 'en';
        $current_language = sanitize_title($current_language);

        if ( in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        //if( function_exists('icl_get_languages')){
            global $sitepress;
            $sitepress->switch_lang($current_language);
        }

        $fileErrors = array(
            0 => __( "There is no error, the file uploaded with success", 'job-postings' ),
            1 => __( "The uploaded file exceeds the upload_max_files in server settings", 'job-postings' ),
            2 => __( "The uploaded file exceeds the MAX_FILE_SIZE from html form", 'job-postings' ),
            3 => __( "The uploaded file uploaded only partially", 'job-postings' ),
            4 => __( "No file was uploaded", 'job-postings' ),
            6 => __( "Missing a temporary folder", 'job-postings' ),
            7 => __( "Failed to write file to disk", 'job-postings' ),
            8 => __( "A PHP extension stoped file to upload", 'job-postings' ),
        );


        $file_storage           = get_option( 'jobs_file_storage' );

        $disable_notification   = apply_filters('job-entry/notification', false);
        $disable_notification   = apply_filters('job-entry/notification_'.$post_id, $disable_notification);

        $position_title         = get_post_meta($post_id, 'position_title', true);
        $notification_email     = get_post_meta($post_id, 'job_confirmation_email', true);

        //$post_title 	= apply_filters('job-entry/title_prefix', _x('New entry: ', 'jobs-entry', 'job-postings')) . $position_title;
        $post_title 	= $position_title;

        $new_post = array(
            'post_type' 	=> 'job-entry',
            'post_title'    => sanitize_text_field( $post_title ),
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_parent' 	=> $post_id,
        );

        // Insert the post into the database
        $new_post_id = wp_insert_post( $new_post );

        if($new_post_id){

            $data = array(
                'job_id' 	=> $post_id,
                'position' 	=> $position_title,
            );

            $data['entry']['entry_id'] = $new_post_id;

            update_post_meta($new_post_id, 'job_entry_viewed', 'no');

            if($notification_email) $data['notification_email'] = $notification_email;

            $labels = array();

            // Max file size
            $max_filesize_mb 	= get_option( 'jobs_max_filesize' ) ? get_option( 'jobs_max_filesize' ) : 10;
            $max_filesize 		= $max_filesize_mb * (1024 * 1024);

            $filesize_placeholder = __('File %2$s exceeds the allowed file size of %1$s MB.', 'job-postings');
            $filesize_exceeded = get_option( 'jobs_filesize_validation_'.Job_Postings::$lang );
            $filesize_exceeded = $filesize_exceeded ? $filesize_exceeded : $filesize_placeholder;


            if(!empty($apply_advanced['modal'])){
                foreach ($apply_advanced['modal'] as $key => $field) {

                    $field_type 	= isset($field['field_type']) ? $field['field_type'] : '';
                    $label 			= isset($field['label_'.Job_Postings::$lang]) ? $field['label_'.Job_Postings::$lang] : '';
                    $san_label 		= sanitize_title( $label );

                    $field_key 		= $field_type . '_' . $san_label;

                    if( $field_type == 'name' ){
                        $field_key 	= 'job_applicant_' . $field_type;
                    }

                    $labels['input_'.$field_key] = $label;


                    switch ($field_type) {
                        case 'file':
                        case 'file_multi':
                            //skip
                            break;

                        case 'name':
                            if( isset($_POST[$field_key]) ){
                                $value = sanitize_text_field($_POST[$field_key]);
                                $data['entry']['fields'][$label] = $value;
                                $value = array('label' => $label, 'value' => $value);

                                update_post_meta($new_post_id, $field_key, $value);
                            }
                            break;

                        case 'email':
                            if( isset($_POST['input_'.$field_key]) ){
                                $value = sanitize_email($_POST['input_'.$field_key]);

                                if( $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    //echo 'Not_valid_email';
                                    //echo json_encode(array('status' => 'Not_valid_email'));
                                    echo json_encode( array('status' => 'error', 'messages' => array('Not_valid_email') ) );
                                    wp_delete_post( $new_post_id, true );
                                    die();
                                }

                                $data['entry']['fields'][$label] = $value;
                                $value = array('label' => $label, 'value' => $value);

                                update_post_meta($new_post_id, $field_key, $value);
                                
                            }
                            break;  

                        case 'phone':
                            if( isset($_POST['input_'.$field_key]) ){
                                $value = sanitize_text_field($_POST['input_'.$field_key]);

                                $validate_phone = apply_filters('job-postings/validate_phone', true);

                                if( $validate_phone && $value && !preg_match('/((?:\+|00)[17](?: |\-)?|(?:\+|00)[1-9]\d{0,2}(?: |\-)?|(?:\+|00)1\-\d{3}(?: |\-)?)?(0\d|\([0-9]{3}\)|[1-9]{0,3})(?:((?: |\-)[0-9]{2}){4}|((?:[0-9]{2}){4})|((?: |\-)[0-9]{3}(?: |\-)[0-9]{4})|([0-9]{7})|([0-9]{3,}\s[0-9]{3,})|([0-9]{3,}-[0-9]{3,})|([0-9]{2,}\s[0-9]{2,}\s[0-9]{2,}\s[0-9]{2,})|([0-9]{2,}\s[0-9]{2,}\s[0-9]{2,}))/', $value)) {
                                    //echo 'Not_valid_phone_number';
                                    echo json_encode( array('status' => 'error', 'messages' => array('Not_valid_phone_number') ) );
                                    wp_delete_post( $new_post_id, true );
                                    die();
                                }

                                $data['entry']['fields'][$label] = $value;
                                $value = array('label' => $label, 'value' => $value);

                                update_post_meta($new_post_id, $field_key, $value);
                            }
                                
                            break;

                        case 'checkbox':

                            if( isset($_POST) ){

                                //var_dump($_POST);
                                $opt_key = 'checkbox_'.$san_label;
                                $options = array();

                                foreach ($_POST as $key => $postdata) {
                                    if( strpos($key, 'field-checkbox-') !== false ){

                                        if(strpos($key, '__') !== false){
                                            $exp_key = explode('__', $key);
                                            if( $opt_key != $exp_key[0] ) continue;
                                        }

                                        $options[$key] = $postdata;
                                    }
                                }

                                //var_dump($field_key);

                                $data['entry']['fields'][$label] = $options;
                                $value = array('label' => $label, 'options' => $options);

                                update_post_meta($new_post_id, $field_key, $value);
                            }
                            break;

                        case 'radio':

                            if( isset($_POST) ){

                                //var_dump($_POST);
                                $opt_key = 'radio_'.$san_label;
                                $options = array();

                                foreach ($_POST as $key => $postdata) {
                                    if( strpos($key, 'field-radio-') !== false ){
                                        
                                        if(strpos($key, '__') !== false){
                                            $exp_key = explode('__', $key);
                                            if( $opt_key != $exp_key[0] ) continue;
                                        }

                                        $options[$key] = $postdata;
                                    }
                                }

                                //var_dump($field_key);

                                $data['entry']['fields'][$label] = $options;
                                $value = array('label' => $label, 'options' => $options);

                                update_post_meta($new_post_id, $field_key, $value);
                            }
                            break;

                        case 'select':

                            if( isset($_POST) ){

                                $opt_key = 'select_'.$san_label;
                                $options = array();

                                foreach ($_POST as $key => $postdata) {
                                    if( strpos($key, '__field-select') !== false ){
                                        
                                        if(strpos($key, '__') !== false){
                                            $exp_key = explode('__', $key);
                                            if( $opt_key != $exp_key[0] ) continue;
                                        }

                                        $options[$key] = $postdata;
                                    }
                                }

                                //var_dump($field_key);

                                $data['entry']['fields'][$label] = $options;
                                $value = array('label' => $label, 'options' => $options);

                                update_post_meta($new_post_id, $field_key, $value);
                            }
                            break;

                        default:

                            if( isset($_POST['input_'.$field_key]) ){
                                $value = sanitize_text_field($_POST['input_'.$field_key]);
                                $data['entry']['fields'][$label] = $value;
                                $value = array('label' => $label, 'value' => $value);

                                update_post_meta($new_post_id, $field_key, $value);
                            }

                            break;
                    }



                }
            }

            $uploaded   = array();
            $errors     = array();

            foreach ($file_data as $key => $file) {

                $response = array();
                $_FILES[$key]['name'] = self::transliterate( $file['name'] );
                $filename = $_FILES[$key]['name'];

                // Check filesize before uploading
                if($file['size'] >= $max_filesize){
                    $errors[] = str_replace(' ', '_', sprintf($filesize_exceeded, $max_filesize_mb, $filename));
                    $uploaded[] = $filename;
                    continue;
                }

                $attachment_id = media_handle_upload( $key, $new_post_id );

                if ( is_wp_error( $attachment_id ) ) {
                    /*
                    $response[$key]['response'] = "ERROR";
                    $response[$key]['error'] = $fileErrors[ $data[$key]['error'] ];
                    */
                } else {
                    $fullsize_path = get_attached_file( $attachment_id );
                    $pathinfo = pathinfo( $fullsize_path );
                    $url = wp_get_attachment_url( $attachment_id );
                    

                    // Add meta to attachment
                    update_post_meta($attachment_id, 'jobs_plugin_attachment', 'true');

                    $response[$key]['response'] = "SUCCESS";
                    $response[$key]['filename'] = $pathinfo['filename'];
                    $response[$key]['url'] = $url;
                    $type = $pathinfo['extension'];
                    if( $type == "jpeg"
                    || $type == "jpg"
                    || $type == "png"
                    || $type == "gif" ) {
                        $type = "image/" . $type;
                    }
                    $response[$key]['type'] 	= $type;
                    $response[$key]['attachment_id'] = $attachment_id;
                    $response[$key]['post_id']	= $post_id;

                    $lbl = isset($labels[$key]) ? $labels[$key] : '';
                    
                    $response[$key]['label']	= $lbl;

                    if( strpos($key, 'file_multi') !== false ){
                        $trimmed_key = preg_replace("/\d+$/","",$key);
                        $trimmed_key = substr($trimmed_key, 0, -1);
                        $lbl = $labels[$trimmed_key];
                    }

                    $url = esc_url_raw($url);
                    $entry_data = array('label' => $lbl, 'value' => $url, 'path' => $fullsize_path);

                    switch ($file_storage) {
                        case 'media':
                            update_post_meta($new_post_id, 'jobs_attachment_'.$key, serialize($entry_data));
                            break;
                        
                        default:
                            // Secure Location
                            self::relocate_file( $key, $entry_data, $attachment_id, $new_post_id );
                            break;
                    }

                    $uploaded[] = $filename;
                }
            }

            if(!empty($response)) $data['entry']['files'] = $response;

            //print_r($file_data);
            //print_r($labels);



            // DATA
            if( isset($_POST['input_job_fullname']) ){
                $fullname = sanitize_text_field($_POST['input_job_fullname']);
                $data['entry']['fullname'] = $fullname;
                update_post_meta($new_post_id, 'job_fullname', $fullname);
            }

            if( isset($_POST['input_job_email']) ){
                $email = sanitize_email($_POST['input_job_email']);

                if( $email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    //echo 'Not_valid_email';
                    echo json_encode( array('status' => 'error', 'messages' => array('Not_valid_email') ) );
                    wp_delete_post( $new_post_id, true );
                    die();
                }

                $data['entry']['email'] = $email;
                update_post_meta($new_post_id, 'job_email', $email);
            }

            if( isset($_POST['input_job_phone']) ){
                $phone = sanitize_text_field($_POST['input_job_phone']);

                if( $phone && !preg_match('/((?:\+|00)[17](?: |\-)?|(?:\+|00)[1-9]\d{0,2}(?: |\-)?|(?:\+|00)1\-\d{3}(?: |\-)?)?(0\d|\([0-9]{3}\)|[1-9]{0,3})(?:((?: |\-)[0-9]{2}){4}|((?:[0-9]{2}){4})|((?: |\-)[0-9]{3}(?: |\-)[0-9]{4})|([0-9]{7}))/', $phone)) {
                    //echo 'Not_valid_phone_number';
                    echo json_encode(array('status' => 'error', 'messages' => array('Not_valid_phone_number') ));
                    wp_delete_post( $new_post_id, true );
                    die();
                }

                $data['entry']['phone'] = $phone;
                update_post_meta($new_post_id, 'job_phone', $phone);
            }



            // FILES
            $response = array();
            if( $file_data && empty($uploaded) ){
                $i = 1;
                foreach ($file_data as $key => $file) {
                    $_FILES[$key]['name'] = self::transliterate( $file['name'] );
                    $filename = $_FILES[$key]['name'];

                    if( in_array($filename, $uploaded) ) continue;

                    // Check filesize before uploading
                    if($file['size'] >= $max_filesize){
                        $errors[] = str_replace(' ', '_', sprintf($filesize_exceeded, $max_filesize_mb, $filename));
                        continue;
                    }

                    $attachment_id = media_handle_upload( $key, $new_post_id );


                    if ( is_wp_error( $attachment_id ) ) {
                        /*
                        $response[$key]['response'] = "ERROR";
                        $response[$key]['error'] = $fileErrors[ $data[$key]['error'] ];
                        */
                    } else {
                        $fullsize_path = get_attached_file( $attachment_id );
                        $pathinfo = pathinfo( $fullsize_path );
                        $url = wp_get_attachment_url( $attachment_id );

                        update_post_meta($attachment_id, 'jobs_plugin_attachment', 'true');

                        $response[$key]['response'] = "SUCCESS";
                        $response[$key]['filename'] = $pathinfo['filename'];
                        $response[$key]['url'] = $url;
                        $type = $pathinfo['extension'];
                        if( $type == "jpeg"
                        || $type == "jpg"
                        || $type == "png"
                        || $type == "gif" ) {
                            $type = "image/" . $type;
                        }
                        $response[$key]['type'] 	= $type;
                        $response[$key]['attachment_id'] = $attachment_id;
                        $response[$key]['post_id']	= $post_id;

                        $url = esc_url_raw($url);
                        $entry_data = array('label' => 'Attachment', 'value' => $url, 'path' => $fullsize_path);

                        switch ($file_storage) {
                            case 'media':
                                update_post_meta($new_post_id, 'jobs_attachment_'.$key, serialize($entry_data));
                                break;
                            
                            default:
                                // Secure Location
                                self::relocate_file( $key, $entry_data, $attachment_id, $new_post_id);
                                break;
                        }
                    }

                    $i++;
                }

                if(!empty($response)) $data['entry']['files'] = $response;
            }

            //if errors, die with printed json
            if( !empty($errors) ){

                //as we faced errors, we have to remove any already saved
                wp_delete_post( $new_post_id, true );

                //echo implode('|', $errors);
                echo json_encode(array('status' => 'error', 'messages' => $errors), true);
                die();
            }


            // use add_action( 'job-entry/after_submit', 'your_function_name', 10 );
            do_action( 'job-entry/after_submit', $data );

            // Send email to admin
            if( $disable_notification == false ){
                $sent = JobNotifications::sendEntryEmail( $post_id, $new_post_id );
				
				if( $sent ){
					// All good, die in peace
                    //echo 'ok';
                    
                    echo json_encode(array('status' => 'ok'), true);
				}else{
                    $store_user_data = 'off';
                    echo json_encode(array('status' => 'error', 'messages' => array('Error_sending_notification') ), true);
					//echo 'Error_sending_notification_message,_but_dont_worry,_We_stored_Your_data_securelly._Thank_you.';
				}
            }
            
            if( $store_user_data == 'on' ){
                self::delete_uploaded_files( $new_post_id, true );
                wp_delete_post( $new_post_id, true );
            }
        }

        die();
    }

    public static function transliterate( $string ){
        $translit = array(
            "А"=>"a", "Б"=>"b", "В"=>"v", "Г"=>"g", "Д"=>"d",
            "Е"=>"e", "Ё"=>"yo", "Ж"=>"zh", "З"=>"z", "И"=>"i", 
            "Й"=>"j", "К"=>"k", "Л"=>"l", "М"=>"m", "Н"=>"n", 
            "О"=>"o", "П"=>"p", "Р"=>"r", "С"=>"s", "Т"=>"t", 
            "У"=>"u", "Ф"=>"f", "Х"=>"kh", "Ц"=>"ts", "Ч"=>"ch", 
            "Ш"=>"sh", "Щ"=>"sch", "Ъ"=>"", "Ы"=>"y", "Ь"=>"", 
            "Э"=>"e", "Ю"=>"yu", "Я"=>"ya", "а"=>"a", "б"=>"b", 
            "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e", "ё"=>"yo", 
            "ж"=>"zh", "з"=>"z", "и"=>"i", "й"=>"j", "к"=>"k", 
            "л"=>"l", "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p", 
            "р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u", "ф"=>"f", 
            "х"=>"kh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"sch", 
            "ъ"=>"", "ы"=>"y", "ь"=>"", "э"=>"e", "ю"=>"yu", "я"=>"ya", 
            " "=>"-",  ":"=>"-", ";"=>"-","—"=>"-", "–"=>"-",
            "š"=>"s", "č"=>"c", "đ"=>"d", "č"=>"c", "ć"=>"c", "ž"=>"z", "ñ"=>"n",
            "Š"=>"s", "Č"=>"c", "Đ"=>"d", "Č"=>"c", "Ć"=>"c", "Ž"=>"z", "Ñ"=>"n",
            "љ"=>"l", "њ"=>"n", "џ"=>"u",
            "Љ"=>"l", "Њ"=>"n", "Џ"=>"u"
        );
        
        $string = strtr($string, $translit);

        return $string;
    }

    public static function relocate_file( $key, $entry_data, $attachment_id = 0, $entry_id = 0  ){
        if( !$attachment_id || !$entry_id ) return false;

        // Secure file directory
        $filedir = apply_filters('job-postings/uploaded-files-path', JOBPOSTINGSFILESDIR);

        // Create directory if not yet exists
        if (!file_exists($filedir)) {
            mkdir($filedir, 0744, true);
        }

        // Get file path and filename
        $file_path      = get_attached_file( $attachment_id );
        $timestamp      = time();
        $filename       = $timestamp . '-' . basename ( $file_path );
        $new_file_location = $filedir .'/' . $filename;

        $entry_data['value'] = trailingslashit(get_home_url()) . 'job-postings-get-file/' . $filename;
        $entry_data['path'] = $new_file_location;

        // Move file to secure location
        rename($file_path, $new_file_location);

        update_post_meta($entry_id, 'jobs_attachment_'.$key, serialize($entry_data));

        // Force Delete file from WP Media
        self::delete_uploaded_files( $entry_id, false );
        

    }

    public static function delete_uploaded_files( $entry_id = 0, $delete_secure = false ){
        if( !$entry_id ) return;

        // Force Delete file from WP Media
        //wp_delete_attachment( $attachment_id, true );
        $attachments = get_posts( array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_parent' => $entry_id
        ) );

        if ( $attachments ) {
            foreach ( $attachments as $attachment ) {
                $uploads = wp_get_upload_dir();
                $file_meta = wp_get_attachment_metadata( $attachment->ID );
                if( $file_meta && isset($file_meta['sizes']) ){
                    foreach ($file_meta['sizes'] as $key => $meta) {
                        @unlink( trailingslashit( $uploads['path'] ) . $meta['file'] );
                    }
                }
                $delete = get_attached_file( $attachment->ID );
                @unlink( $delete );
                wp_delete_attachment( $attachment->ID, true );
            }
        }


        if( $delete_secure ){
            $post_meta = get_post_meta($entry_id);
            if( $post_meta ){
                foreach ($post_meta as $key => $meta) {
                    if( strpos($key, 'jobs_attachment_') !== false && isset($meta[0]) && !empty($meta[0]) ){
	    				if( Job_Postings_Helper::is_serialized($meta[0]) ){
                            $meta = unserialize($meta[0]);
                        }
	    				if( Job_Postings_Helper::is_serialized($meta) ){
                            $meta = unserialize($meta);
                        }
                        if( isset($meta['path']) && !empty($meta['path']) ){
                            $delete = $meta['path'];
                            @unlink( $delete );
                        }
                    }
                }
            }
            
        }

    }
    
}