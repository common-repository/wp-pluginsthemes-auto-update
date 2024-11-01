<?php
/**
 * Plugin Name: WP Plugins&Themes Auto Update
 * Description: Plugin to enable auto update for themes and plugins
 * Plugin URI: http://www.airaghi.net/en/2014/12/29/wordpress-auto-update-for-plugins-and-themes/
 * Version: 0.2.5
 * Author: Davide Airaghi
 * Author URI: http://www.airaghi.net
 * License: GPLv2
 */
 
defined('ABSPATH') or die("No script kiddies please!");

require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'languages.php' );

$lang = get_bloginfo('language','raw');
if (!isset($wpptau_lang[$lang])) {
		$lang = 'en-US';
}

$wpptau_lang = $wpptau_lang[$lang];

$wpptau_is_multisite = is_multisite();

if ($wpptau_is_multisite) {
	add_action('network_admin_menu', 'wpptau_admin');
} else {
	add_action('admin_menu', 'wpptau_admin');
}

function wpptau_admin() {
	global $wpptau_lang;	
	global $wpptau_is_multisite;
	add_submenu_page( 
		($wpptau_is_multisite ? 'settings.php' : 'options-general.php'),
		$wpptau_lang['ADMIN_PAGE_TITLE'] , $wpptau_lang['ADMIN_MENU_TITLE'], 
		($wpptau_is_multisite ? 'manage_network_options' : 'manage_options'), 
		'wp-pluginsthemes-auto-update-page', 
		'wpptau_admin_page'
    );
    add_action( 'admin_init', 'wpptau_settings' );    
    if ($wpptau_is_multisite) {
		if (get_site_option('wpptau_themes_auto_update')==false) {
			add_site_option('wpptau_themes_auto_update','1');
		}
		if (get_site_option('wpptau_plugins_auto_update')==false) {
			add_site_option('wpptau_plugins_auto_update','1');
		}	
	} else {
		if (get_option('wpptau_themes_auto_update')==false) {
			add_option('wpptau_themes_auto_update','1');
		}
		if (get_option('wpptau_plugins_auto_update')==false) {
			add_option('wpptau_plugins_auto_update','1');
		}
	}
}

function wpptau_settings() {
	register_setting('wp-pluginsthemes-auto-update-page','wpptau_themes_auto_update');
	register_setting('wp-pluginsthemes-auto-update-page','wpptau_plugins_auto_update');
}

function wpptau_admin_page() {
	global $wpptau_lang;
	global $wpptau_is_multisite;
	if (!current_user_can( ($wpptau_is_multisite ? 'manage_network_options' : 'manage_options'))) {
			wp_die('Permission denied');
	}
	if (isset($_POST['wpptau_themes_auto_update'])) {
			if ($wpptau_is_multisite) {
				update_site_option('wpptau_themes_auto_update',(int)$_POST['wpptau_themes_auto_update']);
			} else {
				update_option('wpptau_themes_auto_update',(int)$_POST['wpptau_themes_auto_update']);
			}
	}
	if (isset($_POST['wpptau_plugins_auto_update'])) {
			if ($wpptau_is_multisite) {
				update_site_option('wpptau_plugins_auto_update',(int)$_POST['wpptau_plugins_auto_update']);
			} else {
				update_option('wpptau_plugins_auto_update',(int)$_POST['wpptau_plugins_auto_update']);
			}
	}
	?>
	<div class="wrap">
		<h2><?php echo htmlentities($wpptau_lang['ADMIN_PAGE_TITLE']);?></h2>
		<form method="post" action="<?php echo ($wpptau_is_multisite ? 'settings.php' : 'options-general.php');?>?page=wp-pluginsthemes-auto-update-page">
			<?php settings_fields( 'wp-pluginsthemes-auto-update-page' ); ?>
			<?php do_settings_sections( 'wp-pluginsthemes-auto-update-page' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php echo htmlentities($wpptau_lang['LABEL_THEMES']);?></th>
					<td>
						<?php $val = ($wpptau_is_multisite ? get_site_option('wpptau_themes_auto_update','1') : get_option('wpptau_themes_auto_update','1')); ?>
						<select name="wpptau_themes_auto_update">
							<option value="1" <?php  if ($val=='1') echo 'selected="selected"'; ?> ><?php echo htmlentities($wpptau_lang['YES']);?></option>
							<option value="0" <?php  if ($val=='0') echo 'selected="selected"'; ?> ><?php echo htmlentities($wpptau_lang['NO']);?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo htmlentities($wpptau_lang['LABEL_PLUGINS']);?></th>
					<td>
						<?php $val = ($wpptau_is_multisite ? get_site_option('wpptau_plugins_auto_update','1') : get_option('wpptau_plugins_auto_update','1')); ?>
						<select name="wpptau_plugins_auto_update">
							<option value="1" <?php  if ($val=='1') echo 'selected="selected"'; ?> ><?php echo htmlentities($wpptau_lang['YES']);?></option>
							<option value="0" <?php  if ($val=='0') echo 'selected="selected"'; ?> ><?php echo htmlentities($wpptau_lang['NO']);?></option>
						</select>
					</td>
				</tr>				
			</table>    
			<?php submit_button(); ?>			
		</form>
	</div>
	<?php 
}

function wpptau_is_enabled_themes() {
	global $wpptau_is_multisite;
	$val = ($wpptau_is_multisite ? get_site_option('wpptau_themes_auto_update','1') : get_option('wpptau_themes_auto_update','1'));
	if ($val == '1') {
		return true;
	}
	return false;
}

function wpptau_is_enabled_plugins() {
	global $wpptau_is_multisite;
	$val = ($wpptau_is_multisite ? get_site_option('wpptau_plugins_auto_update','1') : get_option('wpptau_plugins_auto_update','1'));
	if ($val == '1') {
		return true;
	}
	return false;	
}

// if (function_exists('add_filter')) {
	if (wpptau_is_enabled_plugins()) {
		add_filter( 'auto_update_plugin', '__return_true',1);
	}
	if (wpptau_is_enabled_themes()) {
		add_filter( 'auto_update_theme', '__return_true',1);
	}
// }

?>