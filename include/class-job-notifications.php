<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JobNotifications
{
	// public function __construct(){
	// 	add_filter('job-postings/email/merge_tags', array('JobNotifications', 'getAllFields'), 10, 3);
	// }

    public static function sendEntryEmail( $job_id, $entry_id ) {
		
		$file_storage       = get_option( 'jobs_file_storage' );
		
		$admin_email 		= apply_filters('job-postings/email_recipient', get_post_meta($job_id, 'job_confirmation_email', true), $job_id);

		$return = false;

		// proceed only if we have email specified
		if( $admin_email ){

			$position_title = get_post_meta($job_id, 'position_title', true);

	    	$to 		= $admin_email;
			$subject 	= apply_filters('job-postings/email_title_prefix', _x('New entry for ', 'job-entry-email', 'job-postings')) . $position_title;

			$custom_notification = get_post_meta($job_id, 'job_notify_custom_message', true);
			if( $custom_notification && $custom_notification == 'on' ){
				$custom_message = get_post_meta($job_id, 'job_notify_custom_message_editor', true);
				$body = apply_filters('job-postings/email/merge_tags', $custom_message, $job_id, $entry_id, false);
			}else{
				$body = apply_filters('job-postings/email/merge_tags', '{all_fields}', $job_id, $entry_id, true);
			}

			$attachments = array();

			$post_meta = get_post_custom( $entry_id );

			unset($post_meta['job_entry_viewed']);
			unset($post_meta['_edit_lock']);

			$contact_email = '';

			$name = get_post_meta( $entry_id, 'job_applicant_name', true);
			if( $name && isset($name['value']) && $name['value'] != '' ){
				$name = $name['value'];
			}else{
				$name = __( 'Applicant', 'job-postings' );
			}

			if( $post_meta ){

				foreach ($post_meta as $key => $meta) {
					$meta = isset($meta[0]) ? $meta[0] : $meta;
					//$value = get_post_meta($entry_id, $key, true);
					//$meta = unserialize($meta[0]);

					if( Job_Postings_Helper::is_serialized($meta) )
						$meta = unserialize($meta);
					
					if( Job_Postings_Helper::is_serialized($meta) ) 
						$meta = unserialize($meta);


					if( $meta ){

						$label = isset($meta['label']) ? $meta['label'] : '';
						$value = isset($meta['value']) ? $meta['value'] : '';
	
						if( strpos($key, 'jobs_attachment_') !== false ){
							// Add files to attachments
							switch ($file_storage) {
								case 'media':
									$filepath = Job_Postings_Helper::getFilePath( $value );
									$attachments[] = $filepath;
									break;
								
								default:
									$filepath = $meta['path'];
									$attachments[] = $filepath;
									break;
							}
						}

						if( strpos($key, 'name_') !== false ){
							$name = $value;
						}

						if( strpos($key, 'email_') !== false ){
							if(filter_var($value, FILTER_VALIDATE_EMAIL)){
								$contact_email = $value;
							}
						}
					}
					
	
					//if( $contact_email == '' ) $contact_email = $admin_email;
				}
	
				//var_dump( $attachments );
				//die();

				// Legacy support
				$dep_contact_email 	= isset($post_meta['job_email']) ? $post_meta['job_email'][0] : '';
				$letter = isset($post_meta['jobs_attachment_input_job_letter']) ? unserialize($post_meta['jobs_attachment_input_job_letter'][0]) : '';
	
				if( $dep_contact_email || $letter ){
					$name 	= isset($post_meta['job_fullname']) ? $post_meta['job_fullname'][0] : 'Applicant';
					if( $letter ){
						if( Job_Postings_Helper::is_serialized($letter) ) $letter = unserialize($letter);
						$filepath = Job_Postings_Helper::getFilePath( $letter['value'] );
						$attachments[] = $filepath;
					}
					foreach ($post_meta as $key => $field) {
						if( strpos($key, 'jobs_attachment_input_job_cv') !== false ){
							$field = isset($field[0]) ? $field[0] : $field;
							if( Job_Postings_Helper::is_serialized($field) ) $field = unserialize($field);
							if( Job_Postings_Helper::is_serialized($field) ) $field = unserialize($field);
							//var_dump( $field );
							switch ($file_storage) {
								case 'media':
									$filepath = Job_Postings_Helper::getFilePath( $field );
									$attachments[] = $filepath;
									break;
								
								default:
									$filepath = $field['path'];
									$attachments[] = $filepath;
									break;
							}
							//$filepath = Job_Postings_Helper::getFilePath( $field['value'] );
							//$attachments[] = $filepath;
						}
					}
					if( $contact_email == '' ) $contact_email = $dep_contact_email;
				}
				//
			
    			
                
                $body           = apply_filters('job-postings/email_body', $body, $job_id, $entry_id);
                $attachments    = apply_filters('job-postings/email_attachments', $attachments, $job_id, $entry_id);

				if( $contact_email == '' ) $contact_email = $to;

				update_post_meta($entry_id, '_job_posting_entry_contact_email', $contact_email);
				update_post_meta($entry_id, '_job_posting_entry_contact_name', $name);

				$body_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml">
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
								 	<meta name="viewport" content="width=device-width">
								<title>'.$subject.'</title>
							</head><body>'.$body.'<br/><br/></body>';



				$send = wp_mail( $to, $subject, $body_html, self::getHeaders( $contact_email, $job_id, $name ), $attachments );

				$return = $send;
				//print_r( $body_html);
    			//print_r('sendEntryEmail');
			}

			do_action('job-postings/email-notification-sent', $job_id, $entry_id, $attachments);
		
		} // $email

		return $return;
	}


	public static function getHeaders( $contact_email, $job_id, $name = 'Applicant' ){
		$contact_email = explode(',', $contact_email);
		$contact_email = $contact_email[0];

		$from_email	= apply_filters('job-postings/from_email', $contact_email, $job_id);
		$from_name 	= apply_filters('job-postings/from_name', $name, $job_id);

		$headers = "From: ".$from_name." < ".$from_email." >\n";
		$headers .= "X-Sender: ".$contact_email."\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		$headers .= "X-Priority: 1\n"; // Urgent message!
		$headers .= "Reply-To: ".$from_name." < ".$from_email." >\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\n";

		return $headers;
	}

	public static function convertMergeFields( $output, $job_id, $entry_id, $all_fields = true ){
	
		$apply_advanced 	= get_option( 'jobs_apply_advanced' );

		//if(!empty($apply_advanced['modal'])){
			$merge_tags 	= array();
			$merge_tags[] 	= '{all_fields}';
			$merge_tags[] 	= '{position_title}';

			if(!empty($apply_advanced['modal'])){
				foreach ($apply_advanced['modal'] as $key => $field) {
					$field_type 	= isset($field['field_type']) ? $field['field_type'] : '';
					$label 			= isset($field['label_'.Job_Postings::$lang]) ? $field['label_'.Job_Postings::$lang] : '';
					$san_label 		= sanitize_title( $label );
					$field_key 		= $field_type . '_' . $san_label;

					if( $field_type == 'name' ){
						$field_key 	= 'job_applicant_' . $field_type;
					}
					
					$merge_tags[] = '{'.$field_key.'}';
				}
			}

			if( !empty($merge_tags) ){
				foreach($merge_tags as $key => $tag){
					
					if( strpos($output, $tag) !== false && $tag == '{all_fields}' ){

						$output = self::getAllFields( $output, $job_id, $entry_id );

					}else if( strpos($output, $tag) !== false && $tag == '{position_title}' ){

						$position_title = get_post_meta($job_id, 'position_title', true);
						
						$output = str_replace( $tag, $position_title, $output );

					}else if( strpos($output, $tag) !== false ){
						$meta_key = str_replace('{','',$tag);
						$meta_key = str_replace('}','',$meta_key);
						$tag_value = '';
						$meta = get_post_meta($entry_id, $meta_key, true);

						if( Job_Postings_Helper::is_serialized($meta) )
							$meta = unserialize($meta);
						if( Job_Postings_Helper::is_serialized($meta) )
							$meta = unserialize($meta);

						if( $meta ){
							$label = isset($meta['label']) ? $meta['label'] : '';
							$value = isset($meta['value']) ? $meta['value'] : '';

							if( strpos($meta_key, 'jobs_attachment_') !== false ){
								$pathinfo = pathinfo( $value );
								$basename = $pathinfo['basename'];
								$tag_value = '<a href="'.$value.'" target="_blank" class="button">'.$basename.'</a>';
							}else if(strpos($meta_key, 'checkbox_') !== false || strpos($meta_key, 'radio_') !== false || strpos($meta_key, 'select_') !== false){
								$options = $meta['options'];
								if( (is_array($options) && empty($options)) && $value == '' ) continue;
								if(is_array($options)){
									$values = array();
									foreach ($options as $key => $option) {
										$values[] =  $option[0];
									}
									if(!empty($values)) $tag_value = implode(', ', $values);
								}else{
									$tag_value = $value;
								}
							}else{
								if( $value ){
									$tag_value = $value;
								}
							}
						}
						
						$output = str_replace( $tag, $tag_value, $output );

					}
				}
			}
		//}
	

		return wpautop($output);
	}

	public static function getAllFields( $output, $job_id, $entry_id, $all_fields = true ){
		

		$merge_tag = '{all_fields}';

		if( strpos($output, $merge_tag) !== false  ){
			$position_title = get_post_meta($job_id, 'position_title', true);

			$body = '<table border="1" cellpadding="5" cellspacing="0" width="100%" style="margin-top: 20px; margin-bottom: 20px;">';
			
			$body .= '<tr bgcolor="#C0C0C0">';
			$body .= '<th style="font-weight: bold;" align="left" width="40%">'. _x('Field', 'job-entry-email', 'job-postings') . '</th><th style="font-weight: bold;" align="left" width="60%">' . _x('Value', 'job-entry-email', 'job-postings') . "</th>";
			$body .= '</tr>';

			$body .= apply_filters('job-postings/email-position', '<td style="font-weight: bold;">'. _x('Position', 'job-entry-email', 'job-postings')) . '</td><td>' . $position_title . "</td>";
			
			$post_meta 	= get_post_custom( $entry_id );

			unset($post_meta['job_entry_viewed']);
			unset($post_meta['_edit_lock']);

			$contact_email = '';

			$name = get_post_meta( $entry_id, 'job_applicant_name', true);
			if( $name && isset($name['value']) && $name['value'] != '' ){
				$name = $name['value'];
			}else{
				$name = __( 'Applicant', 'job-postings' );
			}


			if( $post_meta ){
				// skip system fields
				unset( $post_meta['_wpml_media_duplicate'] );
				unset( $post_meta['_job_posting_entry_contact_email'] );
				unset( $post_meta['_job_posting_entry_contact_name'] );

				// print_r(  $post_meta );
				// die();

				foreach ($post_meta as $key => $meta) {
					//$meta = isset($meta[0]) ? $meta[0] : $meta;

					$meta = get_post_meta($entry_id, $key, true);

					if( Job_Postings_Helper::is_serialized($meta) )
						$meta = unserialize($meta);
					if( Job_Postings_Helper::is_serialized($meta) ) 
						$meta = unserialize($meta);

					if( $meta ){
						$label = isset($meta['label']) ? $meta['label'] : '';
						$value = isset($meta['value']) ? $meta['value'] : '';

						if( !$label && !$value ) continue;

						if( strpos($key, 'jobs_attachment_') !== false ){
							// // Add files to attachments
							// $filepath = Job_Postings_Helper::getFilePath( $value );
							// $attachments[] = $filepath;
						}else if(strpos($key, 'checkbox_') !== false){
							$options = $meta['options'];
							//var_dump($options);

							if( (is_array($options) && empty($options)) && $value == '' ) continue;

							$body .= '<tr>';
							if(is_array($options)){
								$body .= '<td style="font-weight: bold;">'.$label .'</td>';
								$body .= "<td>";
								foreach ($options as $key => $option) {
									$body .= '- ' . $option[0] . "<br/>";
								}
								$body .= "</td>";
							}else{
								$body .= '<td style="font-weight: bold;">'.$label .'</td><td>' . $value . "</td>";
							}
							$body .= '</tr>';

						}else if(strpos($key, 'select_') !== false){
							$options = $meta['options'];
							//var_dump($options);

							if( (is_array($options) && empty($options)) && $value == '' ) continue;

							$body .= '<tr>';
							if(is_array($options)){
								$body .= '<td style="font-weight: bold;">'.$label .'</td>';
								$body .= "<td>";
								foreach ($options as $option) {
									foreach ($option as $key => $opt) {
										$body .= '- ' . $opt . "<br/>";
									}
								}
								$body .= "</td>";
							}else{
								$body .= '<td style="font-weight: bold;">'.$label .'</td><td>' . $value . "</td>";
							}
							$body .= '</tr>';

						}else if(strpos($key, 'radio_') !== false){
							$options = $meta['options'];
							//var_dump($options);

							if( (is_array($options) && empty($options)) && $value == '' ) continue;

							$body .= '<tr>';
							if(is_array($options)){
								$body .= '<td style="font-weight: bold;">'.$label .'</td>';
								$body .= "<td>";
								foreach ($options as $key => $option) {
									$body .= '- ' . $option[0] . "<br/>";
								}
								$body .= "</td>";
							}else{
								$body .= '<td style="font-weight: bold;">'.$label .'</td><td>' . $value . "</td>";
							}
							$body .= '</tr>';
						}else{
							// Check if value is email or not
							if(filter_var($value, FILTER_VALIDATE_EMAIL)){
								$contact_email = $value;
								$value = '<a href="mailto:'.$contact_email.'">'.$contact_email.'</a>';
							}
							$body .= '<tr>';
							$body .= '<td style="font-weight: bold;">'.$label .'</td><td>' . $value . "</td>";
							$body .= '</tr>';
						}
					}

					//if( $contact_email == '' ) $contact_email = $admin_email;
				}


				// Legacy support
				$name 	= isset($post_meta['job_fullname']) ? $post_meta['job_fullname'][0] : '';
				$dep_contact_email 	= isset($post_meta['job_email']) ? $post_meta['job_email'][0] : '';
				$phone 	= isset($post_meta['job_phone']) ? $post_meta['job_phone'][0] : '';
				$letter = isset($post_meta['jobs_attachment_input_job_letter']) ? $post_meta['jobs_attachment_input_job_letter'][0] : '';

				if( $name || $dep_contact_email || $phone || $letter ){
					if($name){
						$body .= '<tr>';
						$body .= '<td style="font-weight: bold;">'.apply_filters('job-postings/email-name', 'Name') .'</td><td>' . $name . "</td>";
						$body .= '</tr>';
					}
					if($dep_contact_email){
						$body .= '<tr>';
						$body .= '<td style="font-weight: bold;">'.apply_filters('job-postings/email-email', 'E-mail') .'</td><td><a href="mailto:'.$dep_contact_email.'">'.$dep_contact_email.'</a></td>';
						$body .= '</tr>';
					}
					if($phone){
						$body .= '<tr>';
						$body .= '<td style="font-weight: bold;">'.apply_filters('job-postings/email-phone', 'Phone') .'</td><td>' . $phone . "</td>";
						$body .= '</tr>';
					}
					// if( $letter ){
					// 	$filepath = Job_Postings_Helper::getFilePath( $letter );
					// 	$attachments[] = $filepath;
					// }
					// foreach ($post_meta as $key => $field) {
					// 	if( strpos($key, 'jobs_attachment_input_multifile') !== false ){
					// 		$filepath = Job_Postings_Helper::getFilePath( $field[0] );
					// 		$attachments[] = $filepath;
					// 	}
					// }
					//if( $contact_email == '' ) $contact_email = $dep_contact_email;
				}
				//
			}

			$body .= '</table>';


			$output = str_replace( $merge_tag, $body, $output );
		}

		return $output;
	}
}