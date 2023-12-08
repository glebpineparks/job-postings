<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('JobApplyForm') ){
    class JobApplyForm
    {

    public static function get_apply_modal( $post_id ){
        global $post;

        $out = '';

        $out .= '<div class="jobs-modal hide">';
            $out .= '<div class="jobs-modal-table"><div class="jobs-modal-table-cell">';
                $out .= self::get_apply_form( false, '', '', '', $post_id );
            $out .= '</div></div>';
        $out .= '</div>';

        return $out;
    }

    public static function get_apply_form( $inline = false, $custom_title = '', $name = '', $show_title = true, $post_id = '' ){
        if( $post_id == '' ){
            global $post;
            $post_id = $post->ID;
        }

        $name               = htmlspecialchars($name);
        $custom_title       = htmlspecialchars($custom_title);
        //add_action('job-postings/front_enqueue_scripts', array('JobDependencies', 'reCaptchaScripts'));

        $out = '';

        $apply_advanced 	= get_option( 'jobs_apply_advanced' );
        $confirmation 		= get_post_meta( $post_id, 'job_notification_message', true );
        $postition_title 	= apply_filters('jp-modal-position-title', get_post_meta($post_id, 'position_title', true));
        $postition_title    = htmlspecialchars($postition_title);
        $close_img 			= apply_filters('jp-modal-close-image', '<img src="'.JOBPOSTINGSURL.'images/close.svg" alt="Close modal window">');


        $site_key           = get_option( 'jobs_recaptcha_site_key' );
        $secret_key         = get_option( 'jobs_recaptcha_secret_key' );
		$re_type            = get_option( 'jobs_recaptcha_type' );

        $out .= '<div class="jobs-modal-content">';

            if(!$inline) $out .= '<span class="modal-close">'.$close_img.'</span>';

            $out .= '<div class="jobs-modal-form clearfix">';
            $out .= '<form id="jobs-modal-form" method="post" enctype="multipart/form-data">';

                if( $custom_title && $name ){
                    if($show_title) $out .= '<div class="modal-title-small">'.apply_filters('jp-modal-header', $name).'</div>';
                }else{
                    if($show_title) $out .= '<div class="modal-title-small">'.apply_filters('jp-modal-header', _x('Apply now', 'jobs-modal', 'job-postings')).'</div>';
                }

                if(!$inline) $out .= '<div class="modal-title">'.apply_filters('jp-modal-position', _x('Position: ', 'jobs-modal', 'job-postings')) . $postition_title.'</div>';

                $form = '';
                if(!empty($apply_advanced['modal'])){

                    $has_required = false;

                    foreach ($apply_advanced['modal'] as $key => $field) {
                        // print_r('<pre>');
                        // print_r($field);
                        // print_r(Job_Postings::$lang);
                        // print_r('</pre>');

                        $field_type 	= isset($field['field_type']) ? $field['field_type'] : '';
                        $label 			= isset($field['label_'.Job_Postings::$lang]) ? htmlspecialchars($field['label_'.Job_Postings::$lang]) : '';
                        $san_label 		= sanitize_title( $label );
                        $placeholder 	= isset($field['placeholder_'.Job_Postings::$lang]) ? htmlspecialchars($field['placeholder_'.Job_Postings::$lang]) : '';
                        $required 		= isset($field['required']) ? true : false;

                        if($required && !$has_required) $has_required = true;

                        $field_key 		= $field_type . '_' . $san_label;

                        $options 		= null;
                        $preselected 	= null;

                        $accepted 		= null;
                        $accepted_message = null;

                        if( $field_type == 'checkbox' ){
                            $options 	    = isset($field['check_options_'.Job_Postings::$lang]) ? htmlspecialchars($field['check_options_'.Job_Postings::$lang]) : null;
                            $preselected 	= isset($field['check_preselected_'.Job_Postings::$lang]) ? htmlspecialchars($field['check_preselected_'.Job_Postings::$lang]) : null;
                        }

                        if( $field_type == 'radio' ){
                            $options 	    = isset($field['radio_options_'.Job_Postings::$lang]) ? htmlspecialchars($field['radio_options_'.Job_Postings::$lang]) : null;
                            $preselected 	= isset($field['radio_preselected_'.Job_Postings::$lang]) ? htmlspecialchars($field['radio_preselected_'.Job_Postings::$lang]) : null;
                        }


                        if( $field_type == 'select' ){
                            $options 	    = isset($field['select_options_'.Job_Postings::$lang]) ? htmlspecialchars($field['select_options_'.Job_Postings::$lang]) : null;
                            $preselected 	= isset($field['select_preselected_'.Job_Postings::$lang]) ? htmlspecialchars($field['select_preselected_'.Job_Postings::$lang]) : null;
                        }

                        if( $field_type == 'file' ){
                            $accepted 	= isset($field['files_accepted']) ? htmlspecialchars($field['files_accepted']) : null;
                            $accepted 	= $accepted ? preg_replace('/\s+/', '', $accepted) : '';
                            $accepted_message 	= isset($field['files_accepted_message_'.Job_Postings::$lang]) ? htmlspecialchars($field['files_accepted_message_'.Job_Postings::$lang]) : null;
                        }

                        if( $field_type == 'file_multi' ){
                            $accepted 	= isset($field['multi_files_accepted']) ? htmlspecialchars($field['multi_files_accepted']) : null;
                            $accepted 	= $accepted ? preg_replace('/\s+/', '', $accepted) : '';
                            $accepted_message 	= isset($field['multi_files_accepted_message_'.Job_Postings::$lang]) ? htmlspecialchars($field['multi_files_accepted_message_'.Job_Postings::$lang]) : null;
                        }


                        $with_empty_label = array('section');

                        if( $label || in_array($field_type, $with_empty_label) ){
                            $args = array(
                                    'label' 		=> apply_filters('jp-modal-letter-label', $label),
                                    'placeholder' 	=> apply_filters('jp-modal-letter-holder', $placeholder),
                                    'key' 			=> $field_key,
                                    'type' 			=> $field_type,
                                    'required' 		=> $required,
                                    'options' 		=> $options,
                                    'preselected' 	=> $preselected,
                                    'accepted' 		=> $accepted,
                                    'accepted_message' => $accepted_message
                                );

                            if($field_type == 'file_multi'){
                                $args['type'] = 'file';
                                $args['multiple'] = true;
                            }

                            $form .= self::get_modal_input($args);
                        }
                    }


                    if( $has_required ){
                        $form .= self::get_modal_input(
                            array(
                                'label' => '<span class="field_required">*</span>' . _x('Required fields', 'jobs-modal', 'job-postings'),
                                'placeholder' => '',
                                'type' => 'paragraph',
                                'class' => 'required_field_notice'
                            )
                        );
                    }
                }

                if( $form != '' ){
                    $out .= $form;
                }else{
                    // Legacy support
                    $out .= self::get_modal_input(
                        array(
                            'label' => apply_filters('jp-modal-letter-label', _x('Name', 'jobs-modal', 'job-postings')),
                            'placeholder' => apply_filters('jp-modal-letter-holder', _x('Your name', 'jobs-modal', 'job-postings')),
                            'key' => 'job_fullname',
                            'required' => true
                        )
                    );


                    $out .= self::get_modal_input(
                            array(
                                'label' => apply_filters('jp-modal-letter-label', _x('E-mail', 'jobs-modal', 'job-postings')),
                                'placeholder' => apply_filters('jp-modal-letter-holder', _x('Your e-mail address', 'jobs-modal', 'job-postings')),
                                'key' => 'job_email',
                                'required' => true
                            )
                        );

                    $out .= self::get_modal_input(
                            array(
                                'label' => apply_filters('jp-modal-letter-label', _x('Phone', 'jobs-modal', 'job-postings')),
                                'placeholder' => apply_filters('jp-modal-letter-holder', _x('Your phone number', 'jobs-modal', 'job-postings')),
                                'key' => 'job_phone',
                                'required' => true
                            )
                        );


                    $hide_letter_upload = get_option('jobs_hide_letter_upload');
                    $hide_cv_upload = get_option('jobs_hide_cv_upload');

                    if( $hide_letter_upload != 'on' ){
                        $out .= self::get_modal_input(
                                array(
                                    'label' => apply_filters('jp-modal-letter-label', _x('Letter', 'jobs-modal', 'job-postings')),
                                    'placeholder' => apply_filters('jp-modal-letter-holder', _x('Your letter', 'jobs-modal', 'job-postings')),
                                    'key' => 'job_letter',
                                    'type' => 'file',
                                    'add_text' => apply_filters('jp-modal-letter-add', _x('Add', 'jobs-modal', 'job-postings'))
                                )
                            );
                    }


                    if( $hide_cv_upload != 'on' ){
                        $out .= self::get_modal_input(
                                array(
                                    'label' => apply_filters('jp-modal-cv-label', _x('CV & Documents', 'jobs-modal', 'job-postings')),
                                    'placeholder' => apply_filters('jp-modal-cv-holder', _x('Your CV & Documents', 'jobs-modal', 'job-postings')),
                                    'key' => 'job_cv',
                                    'type' => 'file',
                                    'add_text' => apply_filters('jp-modal-letter-add', _x('Add', 'jobs-modal', 'job-postings')),
                                    'multiple' => true
                                )
                            );
                    }
                }


                $out .= self::get_modal_input(
                    array(
                        'label' => _x('Phone', 'jobs-modal', 'job-postings'),
                        'placeholder' => _x('Phone', 'jobs-modal', 'job-postings'),
                        'key' => 'honeypot',
                        'required' => false
                    )
                );


                $out .= '<div class="jobs-modal-footer">';

                    if( $re_type == '' && $site_key && $secret_key) {
                        $out .= '<div id="jobs_google_recaptcha"></div>';
                    }

                    $out .= apply_filters('job-postings/modal-footer-top', '');

                    $out .= '<input type="hidden" name="action" value="jobslisting_apply_now">';
                    $out .= '<input type="hidden" name="language" value="'.Job_Postings::$lang.'">';
                    $out .= '<input type="hidden" name="post_id" value="'.$post_id.'">';

                    if( $site_key && $secret_key) {
                        $out .= '<input type="hidden" name="captcha_response" value="">';
                    }

                    $out .= '<img class="jobs-sending" src="'.JOBPOSTINGSURL.'/images/loading.svg"  alt="Loading...">';

                    $out .= apply_filters('job-postings/modal-footer-before-aplly_button', '');

                    $out .= '<button class="button job-submit" type="submit">'.apply_filters('jp-modal-submit', _x('Send Application', 'jobs-modal', 'job-postings')).'</button>';

                    $out .= '<p class="jobs-submit-validation"></p>';

                    $out .= apply_filters('job-postings/modal-footer-bottom', '');
                    /*
                    $out .= '<div class="progress-button" data-result="true">';
                        $out .= '<button><span>Submit</span></button>';
                        $out .= '<svg class="progress-circle" width="50" height="50"><path d="m35,2.5c17.955803,0 32.5,14.544199 32.5,32.5c0,17.955803 -14.544197,32.5 -32.5,32.5c-17.955803,0 -32.5,-14.544197 -32.5,-32.5c0,-17.955801 14.544197,-32.5 32.5,-32.5z"/></svg>';
                        $out .= '<svg class="checkmark" width="50" height="50"><path d="m31.5,46.5l15.3,-23.2"/><path d="m31.5,46.5l-8.5,-7.1"/></svg>';
                        $out .= '<svg class="cross" width="50" height="50"><path d="m35,35l-9.3,-9.3"/><path d="m35,35l9.3,9.3"/><path d="m35,35l-9.3,9.3"/><path d="m35,35l9.3,-9.3"/></svg>';
                    $out .= '</div>';
                    */
                $out .= '</div>';


            $out .= '</form>';


            $out .= '<div id="job-apply-confirmation">';
                $out .= wpautop($confirmation);
            $out .= '</div>';

            $out .= '</div>';
        $out .= '</div>';

        return $out;
    }

    public static function get_modal_input( $options = array() ){

        if( empty($options) ) return;

        extract( self::default_atts(
                array(
                    'label' 		=> __( 'Label', 'job-postings' ),
                    'placeholder' 	=> __( 'Placeholder', 'job-postings' ),
                    'key' 			=> 'key',
                    'type' 			=> 'text',
                    'add_text' 		=> _x('Add file', 'jobs-modal', 'job-postings'),
                    'multiple' 		=> false,
                    'required' 		=> false,
                    'options' 		=> array(),
                    'preselected'  	=> '',
                    'accepted'  	=> '',
                    'accepted_message' => '',
                    'class' 		=> ''
            ), $options));

        $req = '';
        $label_req = '';
        if( $required ) {
            $req = 'input-reqired';
            $label_req = '<span class="field_required">*</span>';
        }

        $out = '';

        $out .= '<div class="jobs-modal-input modal-input-'.$key.' '.$type.'_field '.$class.'" data-control="">';
            switch ($type) {
                case 'name':
                    $out .= '<label class="input-label" for="input-'.$key.'">'.$label.$label_req.'</label>';
                    $out .= '<input id="input-'.$key.'" type="text" name="job_applicant_'.$type.'" data-jobinput="'.$key.'" class="modal-input-text input-'.$key.' '.$req.'" value="" placeholder="'.$placeholder.'">';

                    break;
                case 'email':
                    $out .= '<label class="input-label" for="input-'.$key.'">'.$label.$label_req.'</label>';
                    $out .= '<input id="input-'.$key.'" type="text" name="input_'.$key.'" data-jobinput="'.$key.'" class="modal-input-text input-job_email input-'.$key.' '.$req.'" value="" placeholder="'.$placeholder.'">';
                    break;

                case 'textarea':
                    $out .= '<label class="input-label" for="input-'.$key.'">'.$label.$label_req.'</label>';
                    $out .= '<textarea id="input-'.$key.'" name="input_'.$key.'" data-jobinput="'.$key.'" class="modal-input-text input-'.$key.' '.$req.'" placeholder="'.$placeholder.'"></textarea>';
                    break;


                case 'checkbox':


                    if($options){
                        $options = explode("\n", $options);
                        $options = array_filter($options, 'trim');

                        $preselected = preg_replace("/[^0-9,]/", "", $preselected);
                        $preselected = explode(",", $preselected);


                        $out .= '<div class="input-label" for="input-'.$key.'">'.$label.$label_req.'</div>';
                        $k = 1;
                        foreach ($options as $index => $option) {
                            if($option == '' || !$option) continue;
                            if(substr($option, strlen($option)-1, strlen($option)) == ' ') $option = substr($option, 0, -1);
                            $san_option = sanitize_title($option);

                            $checked = '';
                            if( in_array($k, $preselected) ) $checked = 'checked="cheked"';

                            $out .= '<label class="checkbox-label" for="input-'.$key.'-'.$san_option.'">';
                                $out .= '<input '.$checked.' id="input-'.$key.'-'.$san_option.'" type="checkbox" name="'.$key.'__field-'.$type.'-'.$san_option.'[]" data-jobinput="'.$key.'" class="modal-input-checkbox input-'.$key.' '.$req.'" value="'.sanitize_text_field($option).'">';
                                $out .= '<span class="checkbox-text">'.htmlspecialchars_decode($option).'</span>';
                            $out .= '</label>';
                            $k++;
                        }
                    }

                    break;

                case 'radio':


                    if($options){
                        $options = explode("\n", $options);
                        $options = array_filter($options, 'trim');

                        $preselected = preg_replace("/[^0-9,]/", "", $preselected);
                        $preselected = explode(",", $preselected);

                        $out .= '<div class="input-label" for="input-'.$key.'">'.$label.$label_req.'</div>';
                        $k = 1;
                        foreach ($options as $index => $option) {
                            if($option == '' || !$option) continue;
                            if(substr($option, strlen($option)-1, strlen($option)) == ' ') $option = substr($option, 0, -1);
                            $san_label = sanitize_title($label);
                            $san_option = sanitize_title($option);

                            $checked = '';
                            if( in_array($k, $preselected) ) $checked = 'checked="cheked"';

                            $out .= '<label class="radio-label" for="input-'.$key.'-'.$san_option.'">';
                                $out .= '<input '.$checked.' id="input-'.$key.'-'.$san_option.'" type="radio" name="'.$key.'__field-'.$type.'-'.$san_label.'[]" data-jobinput="'.$key.'" class="modal-input-radio input-'.$key.' '.$req.'" value="'.sanitize_text_field($option).'">';
                                $out .= '<span class="radio-text">'.$option.'</span>';
                            $out .= '</label>';
                            $k++;
                        }
                    }

                    break;



                case 'select':

                    if($options){
                        $options = explode("\n", $options);
                        $options = array_filter($options, 'trim');

                        $preselected = preg_replace("/[^0-9,]/", "", $preselected);
                        $preselected = explode(",", $preselected);

                        $multiple = ''; //multiple

                        $out .= '<label class="input-label" for="input-'.$key.'">'.$label.$label_req.'</label>';
                        $k = 1;
                        $out .= '<select '.$multiple.' name="'.$key.'__field-'.$type.'[]" class="modal-input-select input-'.$key.' '.$req.'">';

                        foreach ($options as $index => $option) {
                            if($option == '' || !$option) continue;
                            if(substr($option, strlen($option)-1, strlen($option)) == ' ') $option = substr($option, 0, -1);
                            $san_option = sanitize_title($option);

                            $selected = '';
                            if( in_array($k, $preselected) ) $selected = 'selected="selected"';

                            $out .= '<option '.$selected.' value="'.sanitize_text_field($option).'">';
                                $out .= $option;
                            $out .= '</option>';
                            $k++;
                        }
                        $out .= '</select>';
                    }

                    break;

                case 'file':

                    $class = '';
                    if( $multiple ) $class = 'multiple';


                    $accept = '';
                    if( $accepted ) $accept =  'accept="'.$accepted.'"';

                    $accept_message = '';
                    if( $accepted_message ) $accept_message =  $accepted_message;


                    $forid = 'label-'.$key;

                    $remove = '<img class="remove" src="'.JOBPOSTINGSURL.'/images/remove.svg" alt="Remove file">';
                    $add = '<img class="add" src="'.JOBPOSTINGSURL.'/images/add.svg" alt="Add file">';

                    $add_text = apply_filters('job-modal/add_file_text', $add_text);

                    $out .= '<label class="input-label" for="'.$forid.'">'.$label.$label_req.'</label>';

                    $out .= '<div id="'.$key.'" class="modal-input-fileinput '.$class.'" data-files="0">';

                        if( $multiple ){
                            $out .= '<script type="javascript/html-template" id="file-input-tpl-'.$key.'">';
                                $out .= '<input id="{id}" type="file" '.$accept.' name="input_'.$key.'-{nr}" data-jobinput="{key}" class="jobgroup-{id} inputfile modal-input-file modal-input-multifile input-{key} '.$req.'">';
                            $out .= '</script>';

                            $out .= '<script type="javascript/html-template" id="file-label-tpl-'.$key.'">';
                                $out .= '<label for="{id}" id="label-{id}" class="jobgroup-{id} choose_file_multi"><span class="name">'._x('Select file', 'jobs-modal', 'job-postings').'</span>'.$remove.'</label>';
                            $out .= '</script>';

                            if( $required ) $out .= '<input id="'.$key.'-disabled" type="file" '.$accept.' name="" class="jobgroup-'.$key.'-disabled disabled-file-placeholder inputfile modal-input-file '.$req.'" disabled>';

                            $out .= '<label for="'.$forid.'" data-key="'.$key.'" class="choose_file_multi_add btn btn-secondary btn-sm">'.$add.$add_text.'</label>';

                        } else {
                            $out .= '<input id="'.$forid.'" type="file" '.$accept.' name="input_'.$key.'" data-jobinput="'.$key.'" class="inputfile modal-input-file input-'.$key.' '.$req.'" >';
                            $out .= '<label for="'.$forid.'" class="choose_file btn btn-secondary btn-sm"><span>'.$add.$add_text.'</span></label>';
                        }


                    $out .= '</div>';

                    $out .= '<p class="validation"></p>';

                    if($accept_message){
                        $out .= '<p class="message">'.$accept_message.'</p>';
                    }

                    break;

                case 'paragraph':
                        $out .= '<p class="input-paragraph" >'.$label.'</p>';
                    break;
                
                case 'section':
                    $out .= '<div class="jobs-section-row">';
                        if($label){
							
                            $out .= '<div class="jobs-section-heading">';
                                $out .= htmlspecialchars_decode($label);
                            $out .= '</div>';
                        }
                    $out .= '</div>';
                break;

                default:
                    $out .= '<label class="input-label" for="input-'.$key.'">'.$label.$label_req.'</label>';
                    $out .= '<input id="input-'.$key.'" type="text" name="input_'.$key.'" data-jobinput="'.$key.'" class="modal-input-text input-'.$key.' '.$req.'" value="" placeholder="'.$placeholder.'">';

                    break;
            }
        $out .= '</div>';

        return $out;
    }

    public static function default_atts( $pairs, $atts ) {
        $atts = (array)$atts;
        $out = array();
        foreach ($pairs as $name => $default) {
                if ( array_key_exists($name, $atts) )
                    $out[$name] = $atts[$name];
                else
                    $out[$name] = $default;
        }


        return $out;
    }
    }
}