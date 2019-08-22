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
function modify_content( $content ) {

    if ( is_single() && in_the_loop() && is_main_query() && in_category( 'turkbox-article' ) ) {
        $newContent = substr($content,0,100);
        if (is_user_logged_in()){
            $user = wp_get_current_user();
            if ( in_array( 'paid_subscriber', $user->roles, true ) ) {
                return $content;
            }
            else {
                $redirect = get_permalink();
                $turkbox_banner = create_banner_upgrade();
                return $newContent.$turkbox_banner;
            }
        }
        else {
            $redirect = get_permalink();
            $turkbox_banner = create_banner_login();
            return $newContent.$turkbox_banner;
        }   
    }
    return $content;
}

function create_banner_upgrade(){
    // return '<br><p style="padding:2px 6px 4px 6px; color: #555555; background-color: #eeeeee; border: #dddddd 2px solid">This is a premium article. Please upgrade your membership using the link below to access:<a href="/index.php?page_id='.$_SESSION['upgrade_page'].'&rd='.$redirect.'">  Upgrade Membership</a></p>';
    
    add_action('wp_enqueue_scripts','ava_test_init');
    return '
    <script>!function (l) { function e(e) { for (var r, t, n = e[0], o = e[1], u = e[2], f = 0, i = []; f < n.length; f++)t = n[f], p[t] && i.push(p[t][0]), p[t] = 0; for (r in o) Object.prototype.hasOwnProperty.call(o, r) && (l[r] = o[r]); for (s && s(e); i.length;)i.shift()(); return c.push.apply(c, u || []), a() } function a() { for (var e, r = 0; r < c.length; r++) { for (var t = c[r], n = !0, o = 1; o < t.length; o++) { var u = t[o]; 0 !== p[u] && (n = !1) } n && (c.splice(r--, 1), e = f(f.s = t[0])) } return e } var t = {}, p = { 1: 0 }, c = []; function f(e) { if (t[e]) return t[e].exports; var r = t[e] = { i: e, l: !1, exports: {} }; return l[e].call(r.exports, r, r.exports, f), r.l = !0, r.exports } f.m = l, f.c = t, f.d = function (e, r, t) { f.o(e, r) || Object.defineProperty(e, r, { enumerable: !0, get: t }) }, f.r = function (e) { "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, { value: "Module" }), Object.defineProperty(e, "__esModule", { value: !0 }) }, f.t = function (r, e) { if (1 & e && (r = f(r)), 8 & e) return r; if (4 & e && "object" == typeof r && r && r.__esModule) return r; var t = Object.create(null); if (f.r(t), Object.defineProperty(t, "default", { enumerable: !0, value: r }), 2 & e && "string" != typeof r) for (var n in r) f.d(t, n, function (e) { return r[e] }.bind(null, n)); return t }, f.n = function (e) { var r = e && e.__esModule ? function () { return e.default } : function () { return e }; return f.d(r, "a", r), r }, f.o = function (e, r) { return Object.prototype.hasOwnProperty.call(e, r) }, f.p = "/"; var r = window.webpackJsonp = window.webpackJsonp || [], n = r.push.bind(r); r.push = e, r = r.slice(); for (var o = 0; o < r.length; o++)e(r[o]); var s = n; a() }([])</script>
    <script>
    var slot = document.createElement("div");
    slot.setAttribute("id", "turkbox-banner-slot");
    document.body.append(slot)</script>';
}

function create_banner_login(){
    return '<br><p style="padding:2px 6px 4px 6px; color: #555555; background-color: #eeeeee; border: #dddddd 2px solid">This is a premium article. Please log in or sign up using the link below to access:<a href="/index.php?page_id='.$_SESSION['login_page'].'&rd='.$redirect.'">  Log in/Sign up</a></p>';
}


 // $user = wp_get_current_user();
    // add_user_meta($user->ID,'_expires_','expired');

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
    return '<div style = "height: 300px; width: 700px; background:rgb(220,220,220,0.8); position: relative;"><div style = "top: 0px; left: 0%; height: 100%; width: 30%; position: absolute; padding-left:6%; display: block;">WEEKLY<br><br><a href="/index.php?page_id='.$_SESSION['checkout_page'].'&plan=weekly&rd='.$redirect.'">Buy Now</a></div><div style = "top: 0px; left: 33%; height: 100%; width: 30%; position: absolute; padding-left:6%; display: block;">MONTHLY<br><br><a href="/index.php?page_id='.$_SESSION['checkout_page'].'&plan=monthly&rd='.$redirect.'">Buy Now</a></div><div style = "top: 0px; left: 66%; height: 100%; width: 30%; position: absolute; padding-left:6%; display: block;">YEARLY<br><br><a href="/index.php?page_id='.$_SESSION['checkout_page'].'&plan=yearly&rd='.$redirect.'">Buy Now</a></div></div>';
    }

function turkbox_checkout() {
    include('payment.php');
}


function prefix_admin_verify_payment(){
    include("config.php");
    $razorpayPaymentId = $_POST["razorpay_payment_id"];
    $razorpaySignature = $_POST["razorpay_signature"];
    $subscriptionId = $_POST["subscription_id"];
    $rd = $_POST['redirect'];
    $expectedSignature = hash_hmac('sha256', $razorpayPaymentId . '|' . $subscriptionId, $keySecret);

    if ($expectedSignature === $razorpaySignature)
    {
        $user = wp_get_current_user();
        $user->remove_role('subscriber');
        $user->add_role('paid_subscriber');
        add_user_meta($user->ID,'subscription_id',$subscriptionId);
        wp_redirect($rd);
    }
}

function myplugin_activate() {

    add_role(
        'paid_subscriber',
        'Paid Subscriber',
        [
            'read'         => true,
            'edit_posts'   => false,
            'upload_files' => false
        ]
    );
    $paidRole = get_role('paid_subscriber');
    $paidRole->add_cap('read_paid', true);

    $postType = 'page';
    $results = '[turkbox_login]';
    $new_post = array(
        'post_title' => 'Login',
        'post_content' => $results,
        'post_type' => $postType,
        'post_status' => 'publish'
        );

    $_SESSION['login_page'] = wp_insert_post($new_post);

    $postType = 'page';
    $results = '[turkbox_checkout]';
    $new_post = array(
        'post_title' => 'Checkout',
        'post_content' => $results,
        'post_type' => $postType,
        'post_status' => 'publish'
        );

    $_SESSION['checkout_page'] = wp_insert_post($new_post);

    $postType = 'page';
    $results = '[turkbox_upgrade]';
    $new_post = array(
        'post_title' => 'Upgrade',
        'post_content' => $results,
        'post_type' => $postType,
        'post_status' => 'publish'
        );

    $_SESSION['upgrade_page'] = wp_insert_post($new_post);
}

function myplugin_deactivate(){
    wp_delete_post($_SESSION['login_page']);
    wp_delete_post($_SESSION['upgrade_page']);
    wp_delete_post($_SESSION['checkout_page']);
}

function plugin_init(){
    if (is_user_logged_in()){
        $user = wp_get_current_user();
            if ( in_array( 'paid_subscriber', $user->roles, true ) ) {
                $subId = get_user_meta($user->ID,'subscription_id');
            }
    }
}


function ava_test_init() {
    wp_enqueue_script( 'script1', plugins_url( '/js/2.c285be09.chunk.js', __FILE__ ));
    wp_enqueue_script( 'script2', plugins_url( '/js/main.03a730a4.chunk.js', __FILE__ ));
    wp_enqueue_script( 'script3', plugins_url( '/js/runtime~main.a8a9905a.js', __FILE__ ));
    wp_register_style('mystyle',plugins_url( '/css/main.208599e9.chunk.css', __FILE__ ));
    wp_enqueue_style( 'mystyle' );
}
    
add_action('admin_post_verify_payment','prefix_admin_verify_payment');
add_action( 'init', 'plugin_init' );
add_filter( 'the_content', 'modify_content' );
add_shortcode('turkbox_login', 'turkbox_login');
add_shortcode('turkbox_upgrade','turkbox_upgrade');
add_shortcode('turkbox_checkout','turkbox_checkout');

register_activation_hook( __FILE__, 'myplugin_activate' );
register_deactivation_hook( __FILE__, 'myplugin_deactivate' );
add_action('wp_head','ava_test_init');



?>