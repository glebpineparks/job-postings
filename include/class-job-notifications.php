<?php

class JobNotifications
{
	// public function __construct(){
	// 	add_filter('job-postings/email/merge_tags', array('JobNotifications', 'getAllFields'), 10, 3);
	// }

    public static function sendEntryEmail( $job_id, $entry_id ) {

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
				$body = apply_filters('job-postings/email/merge_tags', $custom_message, $job_id, $entry_id);
			}else{
				$body = apply_filters('job-postings/email/merge_tags', '{all_fields}', $job_id, $entry_id);
			}

			$attachments = array();

			$post_meta = get_post_custom( $entry_id );

			unset($post_meta['job_entry_viewed']);
			unset($post_meta['_edit_lock']);

			$contact_email = '';

			$name = get_post_meta( $entry_id, 'job_applicant_name', true);
			if( $name ){
				$name = unserialize($name);
				$name = $name['value'];
			}else{
				$name = 'Applicant';
			}

			if( $post_meta ){
				foreach ($post_meta as $key => $meta) {

					$value = get_post_meta($entry_id, $key, true);
					$meta = unserialize($value);
	
					if( $meta ){
						$label = $meta['label'];
						$value = $meta['value'];
	
						if( strpos($key, 'jobs_attachment_') !== false ){
							// Add files to attachments
							$filepath = $meta['path'];
							//$filepath = Job_Postings_Helper::getFilePath( $value, $path );
							$attachments[] = $filepath;
						}


						if( strpos($key, 'email_') !== false ){
							if(filter_var($value, FILTER_VALIDATE_EMAIL)){
								$contact_email = $value;
							}
						}
					}
	
					if( $contact_email == '' ) $contact_email = $admin_email;
				}
	
	
				// Legacy support
				$dep_contact_email 	= isset($post_meta['job_email']) ? $post_meta['job_email'][0] : '';
				$letter = isset($post_meta['jobs_attachment_input_job_letter']) ? $post_meta['jobs_attachment_input_job_letter'][0] : '';
	
				if( $dep_contact_email || $letter ){
					$name 	= isset($post_meta['job_fullname']) ? $post_meta['job_fullname'][0] : 'Applicant';
					if( $letter ){
						$filepath = Job_Postings_Helper::getFilePath( $letter );
						$attachments[] = $filepath;
					}
					foreach ($post_meta as $key => $field) {
						if( strpos($key, 'jobs_attachment_input_multifile') !== false ){
							$filepath = Job_Postings_Helper::getFilePath( $field[0] );
							$attachments[] = $filepath;
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



				$send = wp_mail( $to, $subject, $body_html, self::getHeaders( $name, $contact_email, $job_id ), $attachments );

				$return = $send;
				//print_r( $body_html);
    			//print_r('sendEntryEmail');
			}

			do_action('job-postings/email-notification-sent', $job_id, $entry_id, $attachments);
		
		} // $email

		return $return;
	}


	public static function getHeaders( $name = 'Applicant', $contact_email, $job_id ){
		$contact_email = explode(',', $contact_email);
		$contact_email = $contact_email[0];

		$from 		= apply_filters('job-postings/from_email', $contact_email, $job_id);
		$from_name 	= apply_filters('job-postings/from_name', _x('Job entry: ', 'job-entry-email', 'job-postings') . $name, $job_id);
		$headers = "From: ".$from_name." <".$contact_email.">"."\r\n";
		$headers .= "Reply-To: ".$contact_email."\r\n";
		$headers .= "Return-Path: ".$contact_email."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

		return $headers;
	}

	public static function getAllFields( $output, $job_id, $entry_id ){

		$merge_tag = '{all_fields}';

		if( strpos($output, $merge_tag) !== false ){

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
			if( $name ){
				$name = unserialize($name);
				$name = $name['value'];
			}else{
				$name = 'Applicant';
			}


			if( $post_meta ){
				foreach ($post_meta as $key => $meta) {


					$value = get_post_meta($entry_id, $key, true);
					$meta = unserialize($value);

					if( $meta ){
						$label = $meta['label'];
						$value = $meta['value'];


						if( strpos($key, 'jobs_attachment_') !== false ){
							// Add files to attachments
							$filepath = Job_Postings_Helper::getFilePath( $value );
							$attachments[] = $filepath;
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

					if( $contact_email == '' ) $contact_email = $admin_email;
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
					if( $letter ){
						$filepath = Job_Postings_Helper::getFilePath( $letter );
						$attachments[] = $filepath;
					}
					foreach ($post_meta as $key => $field) {
						if( strpos($key, 'jobs_attachment_input_multifile') !== false ){
							$filepath = Job_Postings_Helper::getFilePath( $field[0] );
							$attachments[] = $filepath;
						}
					}
					if( $contact_email == '' ) $contact_email = $dep_contact_email;
				}
				//
			}

			$body .= '</table>';


			$output = str_replace( $merge_tag, $body, $output );
		}

		return $output;
	}
}