<?php
/*
Plugin Name: Turkbox Custom Widget Loader
Plugin URI: http://turkbox.io/publishers/widget/wp
Description: Declares a plugin that will create a custom post type to display Turkbox widget.
Version: 1.0
Author: Turkbox, Inc.
Author URI: http://turkbox.io/
License: MIT
*/


session_start();

/* Test: .... */
function add_random_shit_in_content( $content ) {

    if ( is_single() && in_the_loop() && is_main_query() && in_category( 'turkbox-article' ) ) {
        $newContent = substr($content,0,15);
        if (is_user_logged_in()){
            $user = wp_get_current_user();
            if ( in_array( 'paid_subscriber', $user->roles, true ) ) {
                return $content;
            }
            else {
                $redirect = get_permalink();
                $abc = '<br><p style="padding:2px 6px 4px 6px; color: #555555; background-color: #eeeeee; border: #dddddd 2px solid">This is a premium article. Please upgrade your membership using the link below to access:<a href="/index.php?page_id=58&rd='.$redirect.'">  Upgrade Membership</a></p>';
                return $newContent.$abc;
            }
        }
        else {
            $redirect = get_permalink();
            $abc = '<br><p style="padding:2px 6px 4px 6px; color: #555555; background-color: #eeeeee; border: #dddddd 2px solid">This is a premium article. Please log in or sign up using the link below to access:<a href="/index.php?page_id=52&rd='.$redirect.'">  Log in/Sign up</a></p>';
            return $newContent.$abc;
        }   
    }
    return $content;
}


function wporg_simple_role()
{

    add_role(
        'paid_subscriber',
        'Paid Subscriber',
        [
            'read'         => true,
            'edit_posts'   => false,
            'upload_files' => false
        ]
    );

    // gets the simple_role role object
    $simpleRole = get_role('subscriber');
    $simpleRole->add_cap('read_paid', false);

    $paidRole = get_role('paid_subscriber');
    $paidRole->add_cap('read_paid', true);
    $user = wp_get_current_user();
    add_user_meta($user->ID,'_expires_','expired');
}

function turkbox_login(){
    $redirect = $_GET['rd'];
    $results = '';
    $results .= '<div id="turkbox-paywall-login">';
    $args = array(
        'echo' => true,
        'redirect' => $redirect,
    );
    $results .= wp_login_form($args);
    $results .= '</div>';
    return $results;
}

function turkbox_upgrade(){
    $user = wp_get_current_user();
    $redirect = $_GET['rd'];
    return '<div style = "height: 300px; width: 700px; background:rgb(220,220,220,0.8); position: relative;"><div style = "top: 0px; left: 0%; height: 100%; width: 30%; position: absolute; padding-left:6%; display: block;">WEEKLY<br><br><a href="/index.php?page_id=64&plan=weekly&rd='.$redirect.'">Buy Now</a></div><div style = "top: 0px; left: 33%; height: 100%; width: 30%; position: absolute; padding-left:6%; display: block;">MONTHLY<br><br><a href="/index.php?page_id=64&plan=monthly&rd='.$redirect.'">Buy Now</a></div><div style = "top: 0px; left: 66%; height: 100%; width: 30%; position: absolute; padding-left:6%; display: block;">YEARLY<br><br><a href="/index.php?page_id=64&plan=yearly&rd='.$redirect.'">Buy Now</a></div></div>';
    }

function turkbox_checkout() {
    include('payment.php');
}


    
function prefix_admin_add_foobar() {
    $redirect = $_GET['rd'];
    $user = wp_get_current_user();
    if ( in_array( 'paid_subscriber', $user->roles, true ) ) {
        $user->remove_role('paid_subscriber');
        $user->add_role('subscriber');
    }
    if ( in_array( 'subscriber', $user->roles, true ) ) {
        $user->remove_role('subscriber');
        $user->add_role('paid_subscriber');
    };
    $user_meta=get_userdata($user->ID);
    $user_roles=$user_meta->roles; 
    wp_redirect($redirect);
    }




add_action( 'admin_post_change_role', 'prefix_admin_add_foobar' );
add_action( 'init', 'wporg_simple_role' );
add_filter( 'the_content', 'add_random_shit_in_content' );
add_shortcode('turkbox_login', 'turkbox_login');
add_shortcode('turkbox_upgrade','turkbox_upgrade');
add_shortcode('turkbox_checkout','turkbox_checkout');

?>