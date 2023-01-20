<?php

class JobsModuleVerify{
    public static $api_key = 'da34762c-6fd8-46e1-abb0-817127336c34';
    public static $cache_time = 60; //seconds
    public static $transient_key = '';
    public static $plugin = '';
    public static $url = 'http://licenses.dv';

    public static $message = array();

    public function __construct( $plugin = 'job-postings', $transient_key = 'job-postings-module-transient' ){
        session_start();

        self::$transient_key = $transient_key;
        self::$plugin = $plugin;
        //self::$cache_time = 7 * DAY_IN_SECONDS;

        add_action('admin_init', array('JobsModuleVerify', 'catch_revalidate'));
    }

    public static function catch_revalidate(){
        // if( isset($_POST['jobswp_hrtools_validate']) && strip_tags($_POST['jobswp_hrtools_validate']) != '' && isset($_POST[HRJOBSWPEXTENSIONPURCHKEY]) && strip_tags($_POST[HRJOBSWPEXTENSIONPURCHKEY]) != '' ){ 
        //     $new_key = $_POST[HRJOBSWPEXTENSIONPURCHKEY];
        //     HR_JOBSWP::$verify::revalidate( $new_key );
        //     //die();
        // }
    }

    public function validate(){
        //print_r(self::$transient_key);
        if ( false === ( $response = get_transient( self::$transient_key ) ) ) {
            $response = self::recheck_validation();
            set_transient( self::$transient_key, $response, self::$cache_time );
        }

        // $response = get_option( self::$transient_key.'-response' );
        // if( isset($response['time']) && (time() - $response['time']) >= self::$cache_time  ){
        //     $response = self::recheck_validation();
        // }

        return $response;
    }

    public static function revalidate( $new_key ){

        //delete_transient( self::$transient_key );

        $response = self::recheck_validation( $new_key );
        if($response) set_transient( self::$transient_key, $response, self::$cache_time );
        return $response;
    }

    public static function recheck_validation( $purchase_code = '' ){
        if(!$purchase_code) {
            $purchase_code = get_option( self::$transient_key );
        }

        if( $purchase_code ){

            $domain = $_SERVER['HTTP_HOST'];

            $body = array(
                "purchase_key" => $purchase_code,
                "plugin" => self::$plugin,
                "domain" => $domain
            );

            $response = wp_remote_post( self::$url . '/verify/?key='.self::$api_key, array(
                'method' => 'POST',
                'body' => json_encode($body)
                )
            );

            $response = json_decode( wp_remote_retrieve_body( $response ), true );
            

            if( isset($response['response']['buyer']) && strip_tags($response['response']['buyer']) != '' ){
                
                $response['response']['time'] = time();
                //print_r($response);

                update_option( self::$transient_key.'-response', $response['response'] );
                
                // Save valid transient
                //delete_transient( self::$transient_key );
                //set_transient( self::$transient_key, true, self::$cache_time );
                
                return true;
            }else{

                delete_option( self::$transient_key.'-response' );
                delete_option( self::$transient_key );

                $errors = array();
                switch($response['response']){

                    case 'wrong_key':
                        $errors[] = __('Purchase code is from other product.  Please check and try again with correct one.', 'job-postings');
                    break;

                    case 'invalid_purchase_code':
                    case 'not_valid':
                        $errors[] = __('Not valid purchase code provided. Please check and try again.', 'job-postings');
                    break;
                }

                //print_r($errors);
                if(!empty($errors)) update_option(self::$transient_key.'-errors', $errors);

            }
        }

        // Save not valid transient
        //delete_transient( self::$transient_key );
        //set_transient( self::$transient_key, false, self::$cache_time );
        return false;
    }


    public static function deactivate_validation(){
        $purchase_code = get_option( self::$transient_key );

        if( $purchase_code ){

            $domain = $_SERVER['HTTP_HOST'];

            $body = array(
                "purchase_key" => $purchase_code,
                "plugin" => self::$plugin,
                "domain" => $domain
            );

            $response = wp_remote_post( self::$url . '/deactivate/?key='.self::$api_key, array(
                'method' => 'POST',
                'body' => $body
                )
            );

            if( isset($response['response']['buyer']) && strip_tags($response['response']['buyer']) != '' ){

                delete_option( self::$transient_key.'-response' );
                delete_option( self::$transient_key );
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public static function get_messages(){
        $errors = get_option(self::$transient_key.'-errors');
        if(!empty($errors)){
            $out = '<ul class="jobs-verify-messages">';
            foreach ($errors as $msg) {
                $out .= '<li class="jobs-verify-message">'.$msg.'</li>';
            }
            $out .= '</ul>';

            delete_option( self::$transient_key.'-errors' );
            return $out;
        }
    }
}