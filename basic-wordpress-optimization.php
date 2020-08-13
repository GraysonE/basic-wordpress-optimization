<?php

namespace BasicWordpressOptimization;

/**
 * Plugin Name: Basic WordPress Optimization
 * Description: Hooks and filters to remove unnecessary WordPress features to speed up the site.
 * Version: 0.0.1
 * Author: Erhard Labs - Grayson Erhard
 */

/**
 * RSD (Really Simple Discovery) is needed if you intend to use XML-RPC client, pingback, etc. However, if you don’t
 * need pingback or remote client to manage post then get rid of this unnecessary header by adding the following code.
 */
//remove_action( 'wp_head', 'rsd_link' );

/**
 * Remove extra code related to emojis from WordPress which was added recently to support emoticons in an older browser.
 */
//remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
//remove_action( 'wp_print_styles', 'print_emoji_styles' );
//remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
//remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Starting from version 3, WordPress added shortlink (shorter link of web page address) in header code.
 * For ex:
 * <link rel='shortlink' href='https://geekflare.com/?p=187' />
 */
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

/**
 * Do you have a requirement to use WordPress API (XML-RPC) to publish/edit/delete a post, edit/list comments, upload
 * file? Also having XML-RPC enabled and not hardened properly may lead to DDoS & brute force attacks.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * This doesn’t help in performance but more to mitigate information leakage vulnerability. By default, WordPress adds
 * meta name generator with the version details which is visible in source code and HTTP header.
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Do you use tagging support with Windows live writer? Probably not.
 */
remove_action( 'wp_head', 'wlwmanifest_link' );

/**
 * WordPress added JQuery migration from version 3.6. This is not needed if you are using the latest version of JQuery
 * and themes/plugin are compatible with it.
 *
 * WARNING!!! THIS WILL BREAK FRONT ENDS. THE ROI OF DOING THIS IS FRANKLY NOT WORTH IT.
 */
function upgrade_jquery() {
	if ( ! is_admin() ) {
		wp_deregister_script( 'jquery' );
		wp_enqueue_script( 'jquery-updated', 'https://code.jquery.com/jquery-3.5.1.slim.min.js' );
		wp_enqueue_script( 'jquery-migrate', 'https://code.jquery.com/jquery-migrate-3.3.1.min.js' );
	}
}
//add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\upgrade_jquery' );

/**
 * I don’t know why you need the self-pingback details on your blog post and I know it’s not just I get annoyed. If you
 * are too then below code will help.
 */
function disable_pingback( &$links ) {
	foreach ( $links as $l => $link ) {
		if ( 0 === strpos( $link, get_option( 'home' ) ) ) {
			unset( $links[ $l ] );
		}
	}
}
add_action( 'pre_ping', __NAMESPACE__ . '\disable_pingback' );

/**
 * WordPress use heartbeat API to communicate with a browser to a server by frequently calling admin-ajax.php. This may
 * slow down the overall page load time and increase CPU utilization if on shared hosting.
 *
 * If you don’t have a requirement to use heartbeat API, then you can disable it by adding below.
 */
function stop_heartbeat() {
	wp_deregister_script( 'heartbeat' );
}
add_action( 'init', __NAMESPACE__ . '\stop_heartbeat', 1 );

/**
 * Dashicons are utilized in the admin console, and if not using them to load any icons on front-end then you may want
 * to disable it. By adding below, dashicons.min.css will stop loading on front-end.
 */
function wpdocs_dequeue_dashicon() {
	if ( current_user_can( 'update_core' ) ) {
		return;
	}
	wp_deregister_style( 'dashicons' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\wpdocs_dequeue_dashicon' );

/**
 * Using Contact Form 7 and noticed their CSS/JavaScript files are getting loaded on every page? Well, you are not
 * alone.
 */
//add_filter( 'wpcf7_load_js', '__return_false' );
//add_filter( 'wpcf7_load_css', '__return_false' );