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
                                'show_count' => 'false'
            ), $atts));

            $hide_empty = ($hide_empty == 'true') ? true:false;

            return self::do_job_categories( $aligncategory = 'left', $category = '', $hide_empty = true, $show_count );
        }

        public static function do_job_categories( $aligncategory = 'left', $category = '', $hide_empty = true, $show_count ){

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

            Job_Postings::customStyles();

            $out = '';

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