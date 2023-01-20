<?php
/**
 * Job Postings.
 *
 * @package   Job_Postings_Helper
 * @author    Gleb Makarov <gmakarov@blueglass.com>
 * @license   GPL-2.0+
 * @link      http://blueglass.ee/
 * @copyright 2017 BlueGlass Interactive
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Job_Postings_Helper{


    public static function dateformat_PHP_to_jQueryUI($php_format){
	    $SYMBOLS_MATCHING = array(
	        // Day
	        'd' => 'dd',
	        'D' => 'D',
	        'j' => 'd',
	        'l' => 'DD',
	        'N' => '',
	        'S' => '',
	        'w' => '',
	        'z' => 'o',
	        // Week
	        'W' => '',
	        // Month
	        'F' => 'MM',
	        'm' => 'mm',
	        'M' => 'M',
	        'n' => 'm',
	        't' => '',
	        // Year
	        'L' => '',
	        'o' => '',
	        'Y' => 'yy',
	        'y' => 'y',
	        // Time
	        'a' => '',
	        'A' => '',
	        'B' => '',
	        'g' => '',
	        'G' => '',
	        'h' => '',
	        'H' => '',
	        'i' => '',
	        's' => '',
	        'u' => ''
	    );
	    $jqueryui_format = "";
	    $escaping = false;
	    for($i = 0; $i < strlen($php_format); $i++)
	    {
	        $char = $php_format[$i];
	        if($char === '\\') // PHP date format escaping character
	        {
	            $i++;
	            if($escaping) $jqueryui_format .= $php_format[$i];
	            else $jqueryui_format .= '\'' . $php_format[$i];
	            $escaping = true;
	        }
	        else
	        {
	            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
	            if(isset($SYMBOLS_MATCHING[$char]))
	                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
	            else
	                $jqueryui_format .= $char;
	        }
	    }
	    return $jqueryui_format;
	}


	public static function numbers_only($string){
		return preg_replace( '/[^0-9]/', '', $string );
	}

	public static function numbers_separator_only($string){
		return preg_replace( '/[^0-9,]/', '', $string );
	}


	public static function getRawSvg( $file, $width = true ){
		$svg = file_get_contents(plugin_dir_path( __FILE__ ) . '../images/' . $file );
		if($width) $svg = str_replace('<svg ', '<svg width="0px" height="0px" ', $svg);
		return preg_replace('/^.+\n/', '', $svg);
	}

	public static function getRawSvgfromUrl( $file ){
		$svg = @file_get_contents($file );
		$svg = preg_replace('/^.+\n/', '', $svg);
		return $svg;
	}


    public static function get_new_entries(){

		$new = 0; 
		
		if( is_admin() ){
			$args = array(
				'post_type' => 'job-entry',
				'meta_key' => 'job_entry_viewed',
				'meta_query' => array(
					array(
						'key'     => 'job_entry_viewed',
						'value'   => 'no',
						'compare' => '==',
					),
				),
				'posts_per_page' => -1
			);
			$new = new WP_Query( $args );
			$new = $new->found_posts;

			wp_reset_postdata();
			wp_reset_query();
		}

    	return $new;
    }


    public static function update_edit_form() {
	    echo ' enctype="multipart/form-data"';
	}

	public static function sortByOrder($a, $b) {
	    return $a['sort'] - $b['sort'];
	}


    public static function is_serialized($value, &$result = null){
		// Bit of a give away this one
		if (!is_string($value))
		{
			return false;
		}
		// Serialized false, return true. unserialize() returns false on an
		// invalid string or it could return false if the string is serialized
		// false, eliminate that possibility.
		if ($value === 'b:0;')
		{
			$result = false;
			return true;
		}
		$length	= strlen($value);
		$end	= '';
		if( isset($value[0]) ){
			switch ($value[0])
			{
				case 's':
					if ($value[$length - 2] !== '"')
					{
						return false;
					}
				case 'b':
				case 'i':
				case 'd':
					// This looks odd but it is quicker than isset()ing
					$end .= ';';
				case 'a':
				case 'O':
					$end .= '}';
					if ($value[1] !== ':')
					{
						return false;
					}
					switch ($value[2])
					{
						case 0:
						case 1:
						case 2:
						case 3:
						case 4:
						case 5:
						case 6:
						case 7:
						case 8:
						case 9:
						break;
						default:
							return false;
					}
				case 'N':
					$end .= ';';
					if ($value[$length - 1] !== $end[0])
					{
						return false;
					}
				break;
				default:
					return false;
			}
		}
		if (($result = @unserialize($value)) === false)
		{
			$result = null;
			return false;
		}
		return true;
	}
	
	public static function add_current_nav_class($classes, $item) {
		global $post;

		if( !$post ) return $classes; 

		if ( $post->post_type == 'jobs' && $item->object_id == $post->post_parent ) {
			   $classes[] = 'current-menu-parent';
			   $classes[] = 'current-page-ancestor';
		}

		// Return the corrected set of classes to be added to the menu item
		return $classes;
	}

	public static function getFilePath( $file_url ){
		if( strpos($file_url, '/job-postings-get-file/') ){
			$filedir 	= apply_filters('job-postings/uploaded-files-path', JOBPOSTINGSFILESDIR);
			$siteurl 	= get_option('siteurl');
			$file_path 	= str_replace(trailingslashit($siteurl), trailingslashit($filedir), $file_url);
			$file_path 	= str_replace('/job-postings-get-file', '', $file_path);
			$file_path 	= apply_filters('job-postings/file_path', $file_path, $file_url);
		}else{
			$upload_dir = wp_upload_dir();
			$file_path 	= str_replace(trailingslashit($upload_dir['url']), trailingslashit($upload_dir['path']), $file_url);
			$file_path 	= apply_filters('job-postings/file_path', $file_path, $file_url);
		}
		
		return $file_path;
	}

	public static function get_onoff_switch( $label, $name, $id = '', $is_checked = false, $conditional = '', $conditional2 = '', $conditional_invert = false, $class = '' ){
		global $post;

		$value 	= get_post_meta( $post->ID, $name, true );
		$the_id = $id ? $id : $name;
		$checked = $is_checked ? 'checked' : '';

		if( $value == 'on' ) $checked = 'checked';

		$logic = '';
		if( $conditional ) $logic = 'data-conditional-logic="'.$conditional.'"';
		if( $conditional2 ) $logic .= ' data-conditional-logic2="'.$conditional2.'"';

		if( $conditional_invert ) {
			$logic .= ' data-conditional-logic-invert="invert"';
		}else{
			$logic .= ' data-conditional-logic-invert="normal"';
		}

		$out = '';

		$out .= '<div class="setting-field-row clearfix job_row_name-'.$name.'">';
			$out .= '<label class="toggle-check '.$class.'">';
				$out .= '<input type="checkbox" name="'.$name.'" class="toggle-check-input" id="'.$the_id.'" '.$checked.' value="on" '.$logic.'/>';
				$out .= '<span class="toggle-check-text"></span>';
				$out .= '<span class="text-label">' . $label . '</span>';
			$out .= '</label>';
		$out .= '</div>';

		return $out;
	}

}