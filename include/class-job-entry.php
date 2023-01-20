<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class JobEntry{
    public static function init(){
		add_action( 'add_meta_boxes', array('JobEntry', 'add_meta_boxes'), 10, 2 );
		add_filter( 'manage_edit-job-entry_columns', array('JobEntry', 'job_entry_columns')  );
		add_action( 'manage_posts_custom_column', array('JobEntry', 'job_entry_column_values') , 10, 2 );
		add_action( 'admin_menu', array('JobEntry', 'remove_publish_box') );
		add_filter(	'post_class', array('JobEntry', 'set_row_post_class'), 10, 3 );
		add_action( 'before_delete_post', array('JobEntry', 'remove_uploaded_files') );
	}

	public static function remove_publish_box() {
		remove_meta_box( 'submitdiv', 'job-entry', 'side' );
	}

	public static function get_entry_id() {
		global $post;
		return $post->ID;
	}

    public static function add_meta_boxes( $post_type, $post ) {	
        add_meta_box(
            'jos-entry-meta-box',
            __( 'Entry details', 'job-postings' ),
            array('JobEntry', 'render_job_entry_metabox'),
            'job-entry',
            'normal',
            'high'
		);

		do_action('job-posting/entry_meta_box', $post);
	}

    public static function render_job_entry_metabox( $post ){

        wp_nonce_field( 'job_postings_meta_box_nonce', 'jp-meta_box_nonce' );

		do_action('job-posting/entry_fields_before');

        echo '<div class="jobs-wrapper">';
            self::render_entry_fields( Job_Postings::$fields, $post );
		echo '</div>';
		

		do_action('job-posting/entry_fields_after');

        // entry viewed
        update_post_meta($post->ID, 'job_entry_viewed', 'yes');
    }



    public static function render_entry_fields( $fields, $post ){

    	$post_id = $post->ID;

		$post_meta = get_post_custom($post_id);

    	if( !empty($post_meta) ){

			
			//JobNotifications::sendEntryEmail( 535, $post_id );
			// print_r( '<pre>' );
			// print_r( $post_meta );
			// print_r( '</pre>' );

			echo '<div class="jobs-entry">';
			

				do_action('job-postings/before-entry-fields', $post_id);

	    		$depricated_fields = false;
	    		if( get_post_meta($post_id, 'job_fullname', true) != '' && get_post_meta($post_id, 'job_email', true) != '' && get_post_meta($post_id, 'job_phone', true) != '' ){
	    			$depricated_fields = true;
	    		}
	    		//////////////////
	    		/// New fields
	    		/////////////////


	    		if( !empty($post_meta) && !$depricated_fields ){

					//print_r( $post_meta );

	    			foreach ($post_meta as $key => $meta) {

	    				$meta = get_post_meta($post_id, $key, true);

	    				if( Job_Postings_Helper::is_serialized($meta) )
	    					$meta = unserialize($meta);

	    				if( $meta ){
	    					$label = isset($meta['label']) ? $meta['label'] : '';
	    					$value = isset($meta['value']) ? $meta['value'] : '';

	    					if( strpos($key, 'jobs_attachment_') !== false ){
	    						echo '<div class="jobs-row clearfix">';
									echo '<div class="jobs-row-label">';
						   		 		echo '<label for="entry-letter">'.$label.'</label>';
						   		 	echo '</div>';
						   		 	echo '<div class="jobs-row-input">';
						   		 		$pathinfo = pathinfo( $value );
						   		 		$basename = $pathinfo['basename'];
						   		 		echo '<a href="'.$value.'" target="_blank" class="button">'.$basename.'</a>';
						   		 	echo '</div>';
							    echo '</div>';
	    					}else if(strpos($key, 'checkbox_') !== false){
	    						$options = $meta['options'];
								
								if( (is_array($options) && empty($options)) && $value == '' ) continue;

						    	echo '<div class="jobs-row clearfix">';
									echo '<div class="jobs-row-label">';
						   		 		echo '<label for="entry-name">'.$label.'</label>';
						   		 	echo '</div>';
						   		 	if(is_array($options)){
						   		 		foreach ($options as $key => $option) {
						   		 			//$val = unserialize($val);
	    									//var_dump($val);
						   		 			echo '<div class="jobs-row-input">';
								   		 		echo $option[0];
								   		 	echo '</div>';
						   		 		}
						   		 	}else{
										echo '<div class="jobs-row-input">';
							   		 		echo $value;
							   		 	echo '</div>';
						   		 	}

							    echo '</div>';
							}else if(strpos($key, 'select_') !== false){
								$options = $meta['options'];
								
								if( (is_array($options) && empty($options)) && $value == '' ) continue;

						    	echo '<div class="jobs-row clearfix">';
									echo '<div class="jobs-row-label">';
						   		 		echo '<label for="entry-name">'.$label.'</label>';
						   		 	echo '</div>';
						   		 	if(is_array($options)){
						   		 		foreach ($options as $option) {
						   		 			//$val = unserialize($val);
	    									//var_dump($val);
											foreach ($option as $key => $opt) {
												echo '<div class="jobs-row-input">';
													echo $opt;
												echo '</div>';
											}
										}
						   		 	}else{
										echo '<div class="jobs-row-input">';
							   		 		echo $value;
							   		 	echo '</div>';
						   		 	}

							    echo '</div>';
							}else if(strpos($key, 'radio_') !== false){
								$options = $meta['options'];
								
								if( (is_array($options) && empty($options)) && $value == '' ) continue;

						    	echo '<div class="jobs-row clearfix">';
									echo '<div class="jobs-row-label">';
						   		 		echo '<label for="entry-name">'.$label.'</label>';
						   		 	echo '</div>';
						   		 	if(is_array($options)){
						   		 		foreach ($options as $key => $option) {
						   		 			//$val = unserialize($val);
	    									//var_dump($val);
						   		 			echo '<div class="jobs-row-input">';
								   		 		echo $option[0];
								   		 	echo '</div>';
						   		 		}
						   		 	}else{
										echo '<div class="jobs-row-input">';
							   		 		echo $value;
							   		 	echo '</div>';
						   		 	}

							    echo '</div>';
							}else{
								if( $value && $label ){
							    	echo '<div class="jobs-row clearfix">';
										echo '<div class="jobs-row-label">';
							   		 		echo '<label for="entry-name">'.$label.'</label>';
							   		 	echo '</div>';
							   		 	echo '<div class="jobs-row-input">';
							   		 		echo $value;
							   		 	echo '</div>';
								    echo '</div>';
								}
							}
						}
	    			}
	    		}

	    		//////////////////
	    		/// Legacy fields
	    		/////////////////

	    		if( $depricated_fields ){

						$attached = array();

			    	$name 	= get_post_meta($post_id, 'job_fullname', true);
			    	$email 	= get_post_meta($post_id, 'job_email', true);
			    	$phone 	= get_post_meta($post_id, 'job_phone', true);
			    	$letter = get_post_meta($post_id, 'jobs_attachment_input_job_letter', true);

			    	// Name
				    if( $name ){
				    	echo '<div class="jobs-row clearfix">';
							echo '<div class="jobs-row-label">';
				   		 		echo '<label for="entry-name">'._x('Name', 'job-entry-name', 'job-postings').'</label>';
				   		 	echo '</div>';
				   		 	echo '<div class="jobs-row-input">';
				   		 		echo $name;
				   		 	echo '</div>';
					    echo '</div>';
					}

				    // Email
				    if( $email ){
				    	echo '<div class="jobs-row clearfix">';
							echo '<div class="jobs-row-label">';
				   		 		echo '<label for="entry-email">'._x('E-mail', 'job-entry-name', 'job-postings').'</label>';
				   		 	echo '</div>';
				   		 	echo '<div class="jobs-row-input ">';
				   		 		echo $email;
				   		 	echo '</div>';
					    echo '</div>';
					}

				    // Phone
				    if( $phone ){
				    	echo '<div class="jobs-row clearfix">';
							echo '<div class="jobs-row-label">';
				   		 		echo '<label for="entry-phone">'._x('Phone', 'job-entry-name', 'job-postings').'</label>';
				   		 	echo '</div>';
				   		 	echo '<div class="jobs-row-input">';
				   		 		echo $phone;
				   		 	echo '</div>';
					    echo '</div>';
					}

				    // Phone
				    if( $letter ){
				    	echo '<div class="jobs-row clearfix">';
							echo '<div class="jobs-row-label">';
				   		 		echo '<label for="entry-letter">'._x('Letter', 'job-entry-name', 'job-postings').'</label>';
				   		 	echo '</div>';
				   		 	echo '<div class="jobs-row-input">';

								$data = @unserialize($letter);
								if ($letter === 'b:0;' || $data !== false) {
									$letter = $data['value'];
								}

				   		 		$pathinfo = pathinfo( $letter );
				   		 		$basename = $pathinfo['basename'];
				   		 		echo '<a href="'.$letter.'" target="_blank" class="button">'.$basename.'</a>';
									$attached[] = $letter;
				   		 	echo '</div>';
					    echo '</div>';
					}

					$values = get_post_custom( $post_id );
					if( $values ){
						foreach ($values as $key => $field) {

							if( strpos($key, 'jobs_attachment_') !== false ){

		    				$fielddata = get_post_meta($post_id, $key, true);

								$data = @unserialize($fielddata);
								if ($fielddata === 'b:0;' || $data !== false) {
									$fielddata = $data['value'];
								}

								$pathinfo = pathinfo( $fielddata );
								$basename = $pathinfo['basename'];

								if( in_array($fielddata, $attached) ) continue;

								$attached[] = $fielddata;

								echo '<div class="jobs-row clearfix">';
									echo '<div class="jobs-row-label">';
					   		 		echo '<label for="entry-letter">'._x('Attachment', 'job-entry-name', 'job-postings').'</label>';
					   		 	echo '</div>';
					   		 	echo '<div class="jobs-row-input">';
					   		 		echo '<a href="'.$fielddata.'" target="_blank" class="button">'.$basename.'</a>';
					   		 	echo '</div>';
						    echo '</div>';
							}
						}
					}
				} // $seen_fields


				do_action('job-postings/after-entry-fields', $post_id);

			echo '</div>';

		}
	}
	

	public static function job_entry_columns($columns) {
		if( !is_admin() ) return;

		$apply_advanced = get_option( 'jobs_apply_advanced' );

		$new = array();
		foreach($columns as $key => $title) {

			$new[$key] = $title;
			if ($key == 'title'){

				if(!empty($apply_advanced['modal'])){
					foreach ($apply_advanced['modal'] as $key => $field) {

						$field_type 	= isset($field['field_type']) ? $field['field_type'] : '';
						$label 			= isset($field['label_'.Job_Postings::$lang]) ? $field['label_'.Job_Postings::$lang] : '';
						$san_label 		= sanitize_title( $label );

						$field_key 		= $field_type . '_' . $san_label;


						if($field_type != 'file' && $field_type != 'file_multi'  && $field_type != 'checkbox' && $field_type != 'radio' && $field_type != 'select') {
							$new['job_applicant_'.$field_type.'_'.$key] = $label;
						}
					}
				}
			}
		}
		return $new;
	}

	public static function job_entry_column_values($column_name, $post_id) {
		if( !is_admin() ) return;

		$apply_advanced = get_option( 'jobs_apply_advanced' );

		$post_meta = get_post_custom($post_id);

		unset($post_meta['job_entry_viewed']);


		if(!empty($apply_advanced['modal'])){
			foreach ($apply_advanced['modal'] as $key => $field) {

				$field_type 	= isset($field['field_type']) ? $field['field_type'] : '';
				$label 			= isset($field['label_'.Job_Postings::$lang]) ? $field['label_'.Job_Postings::$lang] : '';
				$san_label 		= sanitize_title( $label );

				$field_key 		= $field_type . '_' . $san_label;

				if( $field_type == 'name' ){
					$field_key 	= 'job_applicant_' . $field_type;
				}

				$meta = get_post_meta($post_id, $field_key, true);

				if( $column_name == 'job_applicant_'.$field_type.'_'.$key ){
					
					if( Job_Postings_Helper::is_serialized($meta) )
						$meta = unserialize($meta);

					if( $meta ){
						//$label = $meta['label'];
						$value = isset($meta['value']) ? $meta['value'] : '';
						
						echo $value;
					}
				}

			}

		}

	}

	public static function set_row_post_class($classes, $class, $post_id){
		
		if( is_admin() ){
		  $screen = get_current_screen(); //verify which page we're on
		  if ('job-entry' == $screen->post_type && 'edit' == $screen->base ) {

			$entry_viewed = get_post_meta($post_id, 'job_entry_viewed', true);
	
			if( $entry_viewed == 'no' ){
			  $classes[] = 'jobs-new-entry';
			}
		  }
		}
		
		return $classes;
	  }

	public static function remove_uploaded_files( $post_id ){
		// We check if the global post type isn't ours and just return
		global $post_type;   
		if ( $post_type != 'job-entry' ) return;

		// Get all post meta
		$post_meta 		= get_post_custom($post_id);

		//Loop throuh meta data of the post
		if( !empty($post_meta) ){
			foreach ($post_meta as $key => $meta) {
				$meta = get_post_meta($post_id, $key, true);

				//Unserialize data if it is serialized
				if( Job_Postings_Helper::is_serialized($meta) )
					$meta = unserialize($meta);

				//Check if meta exists for key
				if( $meta ){
					$file_path 	= isset($meta['path']) ? $meta['path'] : '';

					//If we found attachment with path set, delete it from the server
					if( strpos($key, 'jobs_attachment_') !== false && $file_path != '' ){
						unlink( $file_path );
					}
				}
			}

			
		}
	}
}