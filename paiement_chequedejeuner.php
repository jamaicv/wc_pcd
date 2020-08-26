<?php
/*
Plugin Name: Paiement par chèque déjeuner
Description: Ce plugin paiement d'apporter la possibilité au client de payer une partie ou la totalité de sa commande par chèque déjeuner
Version: 1.0.0
Author: Jamaïca Servier
Author URI: https://olympdesign.fr
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit;
}
require(dirname(__FILE__) . '/paiement_chequedejeuner_settings.php');

function wc_pcd_scripts() {
    $plugin_url = plugin_dir_url(__FILE__);

    wp_enqueue_style('wc_pcd', $plugin_url . 'includes/css/paiement_chequedejeuner.css');
    wp_enqueue_script('wc_pcd', $plugin_url . '/includes/js/paiement_chequedejeuner.js', array('jquery'));
    wp_localize_script('wc_pcd', 'wc_pcd_ajax', admin_url('admin-ajax.php'));
}
add_action('woocommerce_after_checkout_form', 'wc_pcd_scripts');

function wc_pcd_add_payment_option() {
    $plugin_url = plugin_dir_url(__FILE__);
    $wc_pcd_enabled = get_option('wc_pcd_enabled', '1');
    $wc_pcd_title_text = get_option('wc_pcd_title_text', 'Utiliser un moyen de paiement secondaire');
    $wc_pcd_intro_text = get_option('wc_pcd_intro_text', 'Vous pouvez régler une partie de votre commande par chèque déjeuner.');
    $wc_pcd_label_text = get_option('wc_pcd_label_text', 'Montant des tickets restaurants');
    $wc_pcd_price_text = get_option('wc_pcd_price_text', 'Il vous reste %prix%€ à payer');

    /* if ($wc_pcd_enabled) {
        echo 
        '<div id="wc_pcd" class="wc_pcd">
            <h3 id="wc_pcd_title">' . $wc_pcd_title_text . '</h3> <input id="wc_pcd_display" class="toggle-button" type="checkbox">
            <div id="hidden_content">
                <small class="text-muted">' . $wc_pcd_intro_text . '</small>
                <label for="wc_pcd_montant">' . $wc_pcd_label_text . '</label>
                <input id="wc_pcd_montant" name="wc_pcd_montant" type="number" step="0.01"/> ' . '<small id="hidden_montant" style="display: none">' . $wc_pcd_price_text . '</small>
            </div>
        </div>';
    } */
    if ($wc_pcd_enabled) {
        echo strip_tags('<pre><div id="wc_pcd" class="wc_pcd">
        <img src="' . $plugin_url . '/includes/img/logo.png' . '" style="width: 6vw;display: inline;margin-right: 10px"> <h5 id="wc_pcd_title">' . $wc_pcd_title_text . '</h5> <input id="wc_pcd_display" class="toggle-button" type="checkbox">
        <div id="hidden_content">
        <small class="text-muted" style="display: none; font-size: 100%">' . $wc_pcd_intro_text . '</small><div>', '<div><img><small><input><h5><pre>');
        woocommerce_form_field('wc_pcd_montant', array(
            'type'          => 'number',
            'class'         => array('wc_pcd_montant form-row-wide'),
            'label'         => $wc_pcd_label_text,
            'custom_attributes'    => [
                'step'          => '0.01',
                'min'           => '0'
            ]
        ), WC()->checkout->get_value('wc_pcd_montant'));
        echo strip_tags('</div><small id="hidden_montant">' . $wc_pcd_price_text . '</small></div></div></pre>', '<small><div><pre>');
    }
}
add_action('woocommerce_review_order_before_payment', 'wc_pcd_add_payment_option');

function wc_pcd_compute_new_price() {
    global $woocommerce;
    
    $wc_pcd_price_text = get_option('wc_pcd_price_text', 'Il vous reste %prix%€ à payer');
    $montant_cd = $_POST['pcd_montant'];
    $cart_subtotal = $woocommerce->cart->get_subtotal();
    $cart_subtotal = $cart_subtotal - $montant_cd >= 0 ? $cart_subtotal - $montant_cd : 0;
    $wc_pcd_price_text = str_replace('%prix%', $cart_subtotal, $wc_pcd_price_text);

    echo $wc_pcd_price_text;
    wp_die();
}
add_action('wp_ajax_compute_new_price', 'wc_pcd_compute_new_price');
add_action('wp_ajax_nopriv_compute_new_price', 'wc_pcd_compute_new_price');

function wc_pcd_add_fee($cart) {
    if (!$_POST || (is_admin() && !is_ajax())) {
        return;
    }

    if (isset($_POST['post_data'])) {
        parse_str($_POST['post_data'], $post_data);
    } else {
        $post_data = $_POST; // fallback for final checkout (non-ajax)
    }

    if (isset($post_data['wc_pcd_montant']) && $post_data['wc_pcd_montant'] > 0) {
        $wc_pcd_fee_label_text = get_option('wc_pcd_fee_label_text', 'Chèque(s) déjeuner');
        $extracost = 0 - $post_data['wc_pcd_montant'];
        WC()->cart->add_fee($wc_pcd_fee_label_text, $extracost);
    }
}
add_action('woocommerce_cart_calculate_fees', 'wc_pcd_add_fee');
