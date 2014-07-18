<?php
/*
Plugin Name: Content Locker
Plugin URI: http://www.smoothwebs.co.uk
Description: Hide Content from users until we like your facebook page
Version: 1.0
Author: Payam Behjat
Author URI: http://www.smoothwebs.co.uk
License: GPLv2
*/

/* register the shortcode that accepts one parameter */

add_shortcode ( 'premium-content', 'wptuts_content_locker' );
/* shortcode function */

function wptuts_content_locker( $atts, $content ) {
 
    extract( shortcode_atts( array (
        'method' => ''
    ), $atts ) );
     
    global $post;
     
    /* if the method is not 'facebook', then we check for logged user */
    
    if ( 'facebook' != $method ) {
     
        if ( is_user_logged_in() ) {
         
            /* We return the content */
            
            return do_shortcode($content);
             
        } else {
         
            /* We return a login link that will redirect to this post after user is logged */
            return '<div class="wptuts-content-locker">You need to <a href="' . wp_login_url( get_permalink( $post->ID ) ) . '">Log in</a> to see this content</div>';
             
        }
         
    /* We are using the facebook method */
    } else {
         
        /* Check if we have a cookie already set for this post */
        if ( isset( $_COOKIE['content-lock'][$post->ID] ) ) {
            	return do_shortcode( $content );
         
        /* We ask the user to like post to see content */
        } else {
             
            return'<div id="fb-root"></div><div class="content-locker">Please share this post to see the content<div class="fb-like" data-href="' . get_permalink( $post->ID ) . '" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></div>';
   	         /*return'<div id="fb-root"></div><div class="wptuts-content-locker">Please share this post to see the content </div>';*/
         
        }
         
    }
     
}

/* Register stylesheet and javascript with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript */
add_action( 'wp_enqueue_scripts', 'content_locker_scripts' );
  
/*function that enqueue script only if shortcode is used */
function content_locker_scripts() {
 
    global $post;
    wp_register_style( 'content_locker_style', plugins_url( 'style.css', __FILE__ ) );
    wp_register_script( 'content_locker_js', plugins_url( 'content-locker.js', __FILE__ ), array( 'jquery' ), '', true );
     
    if( has_shortcode( $post->post_content, 'premium-content' ) ) {
     
        wp_enqueue_style( 'content_locker_style' );
        wp_enqueue_script( 'content_locker_js-fb', 'http://connect.facebook.net/en_US/all.js#xfbml=1', array( 'jquery' ),'',FALSE );
        wp_enqueue_script( 'content_locker_js' );
        wp_localize_script( 'content_locker_js', 'content_locker', array( 'ID'=> $post->ID ) );
         
    }  
     
}



?>