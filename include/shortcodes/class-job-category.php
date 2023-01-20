<?php

if( !class_exists('JobCategory') ){
    class JobCategory
    {

        public static function do_job_categories_inline($atts = array(), $content = '' ){
            extract(shortcode_atts(
                array(
                    'category' => '',
                    'aligncategory' => 'left',
                    'hide_empty' => 'true',
                    'show_count' => 'false',
                    'multiselect' => 'false'
            ), $atts));

            wp_enqueue_style('jp-front-styles');

            $hide_empty = ($hide_empty == 'true') ? true:false;


            return self::do_job_categories( $aligncategory = 'left', $category = '', $hide_empty = true, $show_count, $multiselect );
        }

        public static function do_job_categories( $aligncategory = 'left', $category = '', $hide_empty = true, $show_count = false, $multiselect = false ){

            $class = array();

            switch ($aligncategory) {
                case 'right':
                    $class[] = 'align-right';
                    break;

                case 'center':
                    $class[] = 'align-center';
                    break;

                default:
                    $class[] = 'align-left';
                    break;
            }


            // We write output to this variable
            $out = '';

            $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

            // Check for the file in theme, if no file to override, use default from the plugin
            if ( $overridden_template = locate_template( 'jobs/include/job-categories.php' ) ) {
                include( $overridden_template );
            } else {
                include( JOBPOSTINGSPATH . 'templates/include/job-categories.php' );
            }

            return $out;

        }


        public static function do_job_category_list( $atts = array(), $content = '' ){
            extract(shortcode_atts(
                array(
                    'show_count' => 'false',
            ), $atts));

            wp_enqueue_style('jp-front-styles');
            Job_Postings::customStyles();

            $out = '';

            $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

            // Check for the file in theme, if no file to override, use default from the plugin
            if ( $overridden_template = locate_template( 'jobs/include/job-category-list.php' ) ) {
                include_once( $overridden_template );
            } else {
                include_once( JOBPOSTINGSPATH . 'templates/include/job-category-list.php' );
            }

            return $out;
        }

    }
}