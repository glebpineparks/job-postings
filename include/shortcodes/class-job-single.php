<?php

if( !class_exists('JobSingle') ){
    class JobSingle
    {
    
        public static function do_job_single( $atts = array(), $content = '' ){
            extract(shortcode_atts(
                array(
                    'id' => ''
            ), $atts));

            if( $id == '' ) return;

            ob_start();

            get_job_fields( $id );

            $output_string = ob_get_contents();
            ob_end_clean();

            return $output_string;
        }
    }
}
