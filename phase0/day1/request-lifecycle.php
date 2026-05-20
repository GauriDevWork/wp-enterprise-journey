<?php
/**
 *  Day 1 — WordPress Request Lifecycle 
 */ 

/**
 * Lifecycle Order
 * 
 * Index.php
 * -> wp-blog-header.php
 * -> wp-load.php
 * -> wp-config.php (Db credentials + Salt + Debug Settings etc)
 * -> wp-settings.php 
 *   -> muplugins_loaded
 *   -> plugins_loaded
 *   -> theme_setup
 *   -> after_theme_setup
 *   -> init
 *   -> wp_loaded
 *   -> wp() called
 *   -> Parse the Query
 *   -> pre_get_posts to modify the SQL if needed
 *   -> WP_Query()
 *   -> template_redirect()
 *   -> Template loads
 *   -> wp_header() and wp_footer() to add style and script on the page
 *   -> Page is loaded in form of HTML on browser.
 */


// ─── HOOK 1: muplugins_loaded ───────────────────────
// Only useful inside mu-plugins. Before regular plugins exist.
// Use for: autoloaders, environment config.
add_action( 'muplugins_loaded', function() {
    // At this point: mu-plugins loaded, nothing else
} );

// ─── HOOK 2: plugins_loaded ──────────────────────────
// All plugins are loaded. WP not fully initialised yet.
// Use for: compatibility checks, loading textdomains.
add_action( 'plugins_loaded', function() {
    // Safe to check: class_exists( 'WooCommerce' )
    // NOT safe to: register CPTs, access current user
} );

// ─── HOOK 3: init ────────────────────────────────────
// WordPress fully initialised. Current user available.
// Use for: CPTs, taxonomies, shortcodes, most setup.
add_action( 'init', function() {
    // register_post_type() goes here
    // register_taxonomy() goes here
} );

// ─── HOOK 4: pre_get_posts ───────────────────────────
// Fires before WP_Query hits the database.
// $query passed BY REFERENCE — no return needed.
add_action( 'pre_get_posts', function( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_home() ) {
        $query->set( 'posts_per_page', 5 );
    }
    // NO return statement — by reference
} );

// ─── HOOK 5: template_redirect ───────────────────────
// After query runs. Before template file loads.
// Last chance to redirect.
add_action( 'template_redirect', function() {
    if ( is_singular( 'portfolio_project' ) && ! is_user_logged_in() ) {
        wp_redirect( wp_login_url() );
        exit;
    }
} );
?>