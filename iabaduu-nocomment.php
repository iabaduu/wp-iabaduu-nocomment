<?php
/**
 * Plugin Name: iabaduu nocomment
 * Plugin URI: https://iabaduu.com
 * Description: Un plugin semplice per disabilitare completamente i commenti in WordPress.
 * Version: 1.0
 * Author: iabaduu srl
 * Author URI: https://iabaduu.com
 * License: GPL2
 */

// Impedisce l'accesso diretto al file
if (!defined('ABSPATH')) {
    exit;
}

class Disabilita_Commenti_Semplice {
    
    public function __construct() {
        // Inizializza il plugin
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Disabilita supporto commenti per tutti i post types
        $post_types = get_post_types(array(), 'names');
        if (is_array($post_types)) {
            foreach ($post_types as $post_type) {
                remove_post_type_support($post_type, 'comments');
                remove_post_type_support($post_type, 'trackbacks');
            }
        }
        
        // Chiude i commenti sui post esistenti
        add_filter('comments_open', '__return_false', 20, 2);
        add_filter('pings_open', '__return_false', 20, 2);
        
        // Nasconde commenti esistenti
        add_filter('comments_array', array($this, 'nascondi_commenti_esistenti'), 10, 2);
        
        // Rimuove voci di menu e widget relativi ai commenti
        add_action('admin_menu', array($this, 'rimuovi_menu_commenti'));
        add_action('wp_dashboard_setup', array($this, 'rimuovi_widget_commenti'));
        add_action('admin_init', array($this, 'rimuovi_supporto_commenti_dashboard'));
        
        // Rimuove CSS relativo ai commenti
        add_action('wp_enqueue_scripts', array($this, 'rimuovi_css_commenti'));
        
        // Rimuove commenti dal menu admin bar
        add_action('admin_bar_menu', array($this, 'rimuovi_commenti_admin_bar'), 999);
    }
    
    // Nasconde i commenti esistenti
    public function nascondi_commenti_esistenti($comments) {
        return array();
    }
    
    // Rimuove menu commenti dall'amministrazione
    public function rimuovi_menu_commenti() {
        remove_menu_page('edit-comments.php');
    }
    
    // Rimuove widget commenti dalla dashboard
    public function rimuovi_widget_commenti() {
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    }
    
    // Rimuove supporto commenti dalla dashboard
    public function rimuovi_supporto_commenti_dashboard() {
        // Rimuove opzioni commenti dalle impostazioni di discussione
        if (isset($_GET['page']) && $_GET['page'] == 'discussion') {
            wp_redirect(admin_url());
            exit;
        }
        
        // Rimuove commenti dalle opzioni rapide quando si modifica un post
        remove_meta_box('commentstatusdiv', 'post', 'normal');
        remove_meta_box('commentstatusdiv', 'page', 'normal');
    }
    
    // Rimuove CSS relativi ai commenti
    public function rimuovi_css_commenti() {
        wp_dequeue_style('wp-block-library-theme');
        wp_deregister_style('wp-block-library-theme');
    }
    
    // Rimuove commenti dalla barra di amministrazione
    public function rimuovi_commenti_admin_bar($wp_admin_bar) {
        $wp_admin_bar->remove_node('comments');
    }
}

// Inizializza il plugin
new Disabilita_Commenti_Semplice();