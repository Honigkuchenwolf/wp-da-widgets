<?php

add_action('admin_head',			'da_widgets_admin_head');
add_action('admin_menu',			'da_widgets_admin_menu');
add_filter('plugin_action_links',	'da_widgets_action_links', 9, 2);

if (!defined('DA_WIDGETS_ADMIN_PAGE'))
	define('DA_WIDGETS_ADMIN_PAGE', implode(DIRECTORY_SEPARATOR, array_slice(explode(DIRECTORY_SEPARATOR, __FILE__), -3)));

function da_widgets_action_links($links, $file) {

	if('da-widgets/da-widgets.php' == $file && function_exists("admin_url")) {
		$settings_link = '<a href="' . admin_url('plugins.php?page=' . DA_WIDGETS_ADMIN_PAGE) . '">' . __('Settings') . '</a>';
		array_unshift($links, $settings_link); // before other links
	}
	return $links;
}

function da_widgets_admin_head() {
	if (isset($_GET['page']) && $_GET['page'] == DA_WIDGETS_ADMIN_PAGE) {
		echo "<link rel='stylesheet' type='text/css' href='" . compat_get_base_plugin_url() .'/'. dirname(DA_WIDGETS_ADMIN_PAGE) . "/theme.css' />\n";
		echo "<script type='text/javascript' src='" . compat_get_base_plugin_url() .'/'. dirname(DA_WIDGETS_ADMIN_PAGE) . "/admin.js' />\n";
	}
}

function da_widgets_admin_menu() {
	add_submenu_page(
		'plugins.php',								// parent
		__( 'deviantArt Widgets', 'da-widgets' ),	// page_title
		__( 'deviantArt Widgets', 'da-widgets' ),	// menu_title
		8,											// capability required
		DA_WIDGETS_ADMIN_PAGE,						// file/handle
		'da_widgets_admin_page'						// function
	);
	add_action('admin_init', 'da_widgets_admin_settings');
}

function da_widgets_admin_settings() {
	// Register Cache Settings
	register_setting('da-widgets-settings', 'cache-enabled');
	register_setting('da-widgets-settings', 'cache-duration');

	// Register Thumbs Settings
	register_setting('da-widgets-settings', 'thumb-enabled', 'intval');
	register_setting('da-widgets-settings', 'thumb-size-x', 'intval');
	register_setting('da-widgets-settings', 'thumb-size-y', 'intval');
	register_setting('da-widgets-settings', 'thumb-format');
}

function da_widgets_admin_page() {
?>
<div id="da-widgets-settings" class="wrap">
	<div id="da-widgets-settings-icon" class="icon32"><br /></div>
	<h2><?php echo __('deviantArt Widgets Settings', 'da-widgets') ?></h2>
	<p id="da-widgets-version"> version <?php echo DA_Widgets::VERSION ?></p>

<?php 
	if (!is_writeable(realpath(ABSPATH . 'wp-content/cache')))
		printf('<div class="error">' . __('Sorry "%s" is not writeable') . '</div>', ABSPATH . 'wp-content/cache');
?>
	<form action="options.php" method="post">
		<?php wp_nonce_field('update-options'); ?>
		<?php settings_fields('da-widgets-settings'); ?>
		<fieldset>
			<legend><?php echo __('Cache Settings') ?></legend>
			<p><?php echo __('Defines whether you want to use cache or not.')?></p>
			<dl>
				<dt><label for="cache-enabled"><?php echo __('Enable cache') ?></label></dt>
				<dd><input <?php echo get_option('cache-enabled') ? 'checked="checked"' : '' ?> type="checkbox" id="cache-enabled" name="cache-enabled" value="1"/></dd>
				<dt><label <?php echo !get_option('cache-enabled') ? 'class="disabled"' : '' ?> for="cache-duration"><?php echo __('Cache duration') ?></label></dt>
				<dd>
					<select <?php echo !get_option('cache-enabled') ? 'disabled="disabled"' : '' ?> id="cache-duration" name="cache-duration">
						<option <?php echo selected(get_option('cache-duration'),   15)?> value="15">15 <?php echo __('minutes')?> (default)</option>
						<option <?php echo selected(get_option('cache-duration'),   30)?> value="30">30 <?php echo __('minutes')?></option>
						<option <?php echo selected(get_option('cache-duration'),   60)?> value="60">1 <?php echo __('hour')?></option>
						<option <?php echo selected(get_option('cache-duration'),  120)?> value="120">2 <?php echo __('hour')?></option>
						<option <?php echo selected(get_option('cache-duration'),  180)?> value="180">3 <?php echo __('hour')?></option>
						<option <?php echo selected(get_option('cache-duration'), 1440)?> value="1440">1 <?php echo __('day')?></option>
					</select>
				</dd>
			</dl>
		</fieldset>
		<fieldset>
			<legend><?php echo __('Thumbnails') ?></legend>
			<p><?php echo __('Defines thumbnails properties.')?></p>
			<dl>
				<dt><label for="thumb-enabled"><?php echo __('Thumbnail generation') ?></label></dt>
				<dd><input <?php echo get_option('thumb-enabled') ? 'checked="checked"' : '' ?> type="checkbox" id="thumb-enabled" name="thumb-enabled" value="1"/></dd>

				<dt><label <?php echo !get_option('thumb-enabled') ? 'class="disabled"' : '' ?> for="thumb-size-x"><?php echo __('Thumbnail size')?></label></dt>
				<dd>
					<input <?php echo !get_option('thumb-enabled') ? 'disabled="disabled"' : '' ?> id="thumb-size-x" name="thumb-size-x" type="text" size="3" maxlength="3" value="<?php echo get_option('thumb-size-x')?>" />
					x
					<input <?php echo !get_option('thumb-enabled') ? 'disabled="disabled"' : '' ?> id="thumb-size-y" name="thumb-size-y" type="text" size="3" maxlength="3" value="<?php echo get_option('thumb-size-y')?>" />
				</dd>

				<dt><label <?php echo !get_option('thumb-enabled') ? 'class="disabled"' : '' ?> for="thumb-format"><?php _e('Thumbnail format') ?></label></dt>
				<dd>
					<select <?php echo !get_option('thumb-enabled') ? 'disabled="disabled"' : '' ?> id="thumb-format" name="thumb-format">
						<option <?php echo selected(get_option('thumb-format'), IMG_PNG)?> value="<?php echo IMG_PNG?>">PNG (default)</option>
						<option <?php echo selected(get_option('thumb-format'), IMG_JPG)?> value="<?php echo IMG_JPG?>">JPEG</option>
						<option <?php echo selected(get_option('thumb-format'), IMG_GIF)?> value="<?php echo IMG_GIF?>">GIF</option>
					</select>
				</dd>
			</dl>
		</fieldset>

		<input type="submit" value="<?php echo __('Save')?>" class="button-primary" />

	</form>
</div>
<?
}


?>