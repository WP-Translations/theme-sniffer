<?php
/**
 * Admin functions
 *
 * @package Theme_Sniffer
 */

/**
 * Add go to theme check page link on plugin page.
 *
 * @since 0.1.3
 *
 * @param array $links Array of plugin action links.
 * @return array Modified array of plugin action links.
 */
function theme_sniffer_plugin_settings_link( $links ) {
	$theme_sniffer_link = '<a href="themes.php?page=theme-sniffer">' . esc_attr__( 'WP-Translations Sniffer Page', 'theme-sniffer' ) . '</a>';
	array_unshift( $links, $theme_sniffer_link );
	return $links;
}
add_filter( 'plugin_action_links_' . THEME_SNIFFER_BASENAME, 'theme_sniffer_plugin_settings_link' );

/**
 * Register admin menu.
 *
 * @since 0.1.0
 */
function theme_sniffer_admin_menu() {
	add_theme_page(
		esc_html__( 'WP-T Sniffer', 'theme-sniffer' ),
		esc_html__( 'WP-T Sniffer', 'theme-sniffer' ),
		'manage_options',
		'theme-sniffer',
		'theme_sniffer_render_admin_page'
	);
}
add_action( 'admin_menu', 'theme_sniffer_admin_menu' );

/**
 * Callback for admin page.
 *
 * @since 0.1.0
 */
function theme_sniffer_render_admin_page() {
	?>
	<div class="wrap theme-sniffer">
		<h1><?php esc_html_e( 'WP-Translations Sniffer', 'theme-sniffer' ); ?></h1>
		<hr />
		<?php theme_sniffer_render_form(); ?>
	</div>
	<?php
}

/**
 * Load admin scripts and styles.
 *
 * @since 0.1.2
 *
 * @param string $hook Admin hook name.
 */
function theme_sniffer_admin_scripts( $hook ) {
	if ( 'appearance_page_theme-sniffer' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'theme-sniffer-admin', THEME_SNIFFER_URL . '/css/admin.css', array(), '0.1.3c' );
	wp_enqueue_script( 'theme-sniffer-admin', THEME_SNIFFER_URL . '/js/admin.js', array( 'jquery', 'underscore' ), '0.1.4' );
	wp_localize_script( 'theme-sniffer-admin', 'localizationObject', array(
		'sniff_error'      => __( 'The check has failed. This could happen due to running out of memory. Either reduce the file length or increase PHP memory.', 'theme-sniffer' ),
		'percent_complete' => __( 'Percent completed: ', 'theme-sniffer' ),
		'check_starting'   => __( 'Check starting...', 'theme-sniffer' ),
		'check_failed'     => __( 'Check has failed :(', 'theme-sniffer' ),
		'check_done'       => __( 'All done!', 'theme-sniffer' ),
	));
}
add_action( 'admin_enqueue_scripts', 'theme_sniffer_admin_scripts' );

/**
 * Render form.
 *
 * @since 0.1.0
 */
function theme_sniffer_render_form() {

	$standards = theme_sniffer_get_standards();

	$all_themes = wp_get_themes();
	$themes = array();

	if ( ! empty( $all_themes ) ) {
		foreach ( $all_themes as $key => $theme ) {
			$themes[ $key ] = $theme->get( 'Name' );
		}
	}

	if ( empty( $themes ) ) {
		return;
	}

	$current_theme = get_stylesheet();
	if ( ! empty( $_POST['themename'] ) ) {
		$current_theme = $_POST['themename'];
	}

	$minimum_php_version = '5.2';
	if ( ! empty( $_POST['minimum_php_version'] ) ) {
		$minimum_php_version = $_POST['minimum_php_version'];
	}

	$hide_warning = 0;
	if ( isset( $_POST['hide_warning'] ) && 'true' === $_POST['hide_warning'] ) {
		$hide_warning = 1;
	}

	$raw_output = 0;
	if ( isset( $_POST['raw_output'] ) && 'true' === $_POST['raw_output'] ) {
		$raw_output = 1;
	}

	$standard_status = wp_list_pluck( $standards, 'default' );

	if ( isset( $_POST['_wp_http_referer'] ) ) {
		foreach ( $standards as $key => $standard ) {
			if ( isset( $_POST[ $key ] ) && 'true' === $_POST[ $key ] ) {
				$standard_status[ $key ] = 1;
			} else {
				$standard_status[ $key ] = 0;
			}
		}
	}
	?>
	<form action="<?php echo esc_url( admin_url( 'themes.php?page=theme-sniffer' ) ); ?>" method="post" class="frm-theme-sniffer">
		<?php wp_nonce_field( 'wp_rest', 'theme_sniffer_nonce' ); ?>
		<div class="theme-switcher-wrap">
			<h2><?php esc_html_e( 'Select Theme', 'theme-sniffer' ); ?></h2>
			<label for="themename">
				<select name="themename">
					<?php foreach ( $themes as $key => $value ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_theme, $key ); ?>><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<span id="check-status" class="button button-secondary"><?php esc_attr_e( 'Go', 'theme-sniffer' ); ?></span>
		</div><!-- .theme-switcher-wrap -->
		<div class="standards-wrap">
			<h2><?php esc_html_e( 'Select Standard', 'theme-sniffer' ); ?></h2>
			<?php foreach ( $standards as $key => $standard ) : ?>
				<label for="<?php echo esc_attr( $key ); ?>" title="<?php echo esc_attr( $standard['description'] ); ?>">
					<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $standard_status[ $key ], 1 ); ?> />
					<?php echo '<strong>' . esc_html( $standard['label'] ) . '</strong>: ' . esc_html( $standard['description'] ); ?>
				</label><br>
			<?php endforeach; ?>
		</div><!-- .standards-wrap -->
		<div class="options-wrap">
			<h2><?php esc_html_e( 'Options', 'theme-sniffer' ); ?></h2>
			<label for="hide_warning"><input type="checkbox" name="hide_warning" id="hide_warning" value="1" <?php checked( $hide_warning, 1 ); ?> /><?php esc_html_e( 'Hide Warnings', 'theme-sniffer' ); ?></label>
			&nbsp;<label for="raw_output"><input type="checkbox" name="raw_output" id="raw_output" value="1" <?php checked( $raw_output, 1 ); ?> /><?php esc_html_e( 'Raw Output', 'theme-sniffer' ); ?></label>&nbsp;
			<?php $php_versions = theme_sniffer_get_php_versions(); ?>
			<label for="minimum_php_version">
				<select name="minimum_php_version">
				<?php foreach ( $php_versions as $version ) : ?>
					<option value="<?php echo esc_attr( $version ); ?>" <?php selected( $minimum_php_version, $version ); ?>><?php echo esc_html( $version ); ?></option>
				<?php endforeach; ?>
				</select>
				<?php esc_html_e( 'Minimum PHP Version', 'theme-sniffer' ); ?>
			</label>
		</div><!-- .options-wrap -->
	</form>
	<div class="theme-sniffer-report"></div><!-- .theme-sniffer-report -->
	<?php
}
