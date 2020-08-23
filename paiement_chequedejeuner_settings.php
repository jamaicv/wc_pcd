<?php

/* Settings for Google Autocomplete Address for WooCommerce plugin */

function wc_pcd_register_settings() {
    register_setting('wc_pcd_settings_group', 'wc_pcd_enabled', array('default' => '1'));
    register_setting('wc_pcd_settings_group', 'wc_pcd_title_text', array('default' => 'Utiliser un moyen de paiement secondaire'));
    register_setting('wc_pcd_settings_group', 'wc_pcd_intro_text', array('default' => 'Vous pouvez régler une partie de votre commande par chèque déjeuner.'));
    register_setting('wc_pcd_settings_group', 'wc_pcd_label_text', array('default' => 'Montant des tickets restaurants'));
    register_setting('wc_pcd_settings_group', 'wc_pcd_price_text', array('default' => 'Il vous reste %prix%€ à payer'));
    register_setting('wc_pcd_settings_group', 'wc_pcd_fee_label_text', array('default' => 'Chèque(s) déjeuner'));
}
add_action('admin_init', 'wc_pcd_register_settings');

function wc_pcd_settings_page() {
    if (!current_user_can('manage_options'))  { // Checking user can manage or not
        wp_die(__( 'Vous n\'avez pas les droits suffisants pour accéder à cette page.', 'pcdfw'));
    }
    ?>
    <div>
        <h2>Google Autocomplete Address for WooCommerce - Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('wc_pcd_settings_group'); ?>
            <table>
                <tr valign="top">
                    <th scope="row" style="text-align: left"><label for="wc_pcd_enabled">Activer</label></th>
                    <td style="text-align: right"><input type="checkbox" id="wc_pcd_enabled" name="wc_pcd_enabled" value="1" <?php checked('1', get_option('wc_pcd_enabled')); ?>/></td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="text-align: left"><label for="wc_pcd_title_text">Texte de titre</label></th>
                    <td style="text-align: right"><input style="width: 30vw;" type="text" id="wc_pcd_title_text" name="wc_pcd_title_text" value="<?php echo get_option('wc_pcd_title_text'); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="text-align: left"><label for="wc_pcd_intro_text">Texte d'introduction</label></th>
                    <td style="text-align: right"><input style="width: 30vw;" type="text" id="wc_pcd_intro_text" name="wc_pcd_intro_text" value="<?php echo get_option('wc_pcd_intro_text'); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="text-align: left"><label for="wc_pcd_label_text">Texte de l'étiquette du champ de saisie</label></th>
                    <td style="text-align: right"><input style="width: 30vw;" type="text" id="wc_pcd_label_text" name="wc_pcd_label_text" value="<?php echo get_option('wc_pcd_label_text'); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="text-align: left"><label for="wc_pcd_price_text">Texte du prix</label></th>
                    <td style="text-align: right"><input style="width: 30vw;" type="text" id="wc_pcd_price_text" name="wc_pcd_price_text" value="<?php echo get_option('wc_pcd_price_text'); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="text-align: left"><label for="wc_pcd_fee_label_text">Texte pour le résumé de la commande</label></th>
                    <td style="text-align: right"><input style="width: 30vw;" type="text" id="wc_pcd_fee_label_text" name="wc_pcd_fee_label_text" value="<?php echo get_option('wc_pcd_fee_label_text'); ?>"/></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function wc_pcd_register_settings_page() {
    add_options_page('Paramètres', 'Plugin \'Paiement par chèque déjeuner\'', 'manage_options', 'wc_pcd', 'wc_pcd_settings_page');
}
add_action('admin_menu', 'wc_pcd_register_settings_page');