<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Add settings page
 */
function wpsos_kdc_add_settings_menu(){
	global $WPSOS_KDC;
	$page = add_options_page(
			'Keyword Count & Density Calc',
			'Keyword Count & Density Calc',
			'manage_options',
			'wpsos-kdc-settings',
			'wpsos_kdc_display_settings'
			);
	if( isset( $_POST['wpsos-kdc-settings']) ){
		//Add action to call save general settings
		add_action( "admin_head-$page", array( $WPSOS_KDC, 'save_settings' ) );
	}
}
add_action( 'admin_menu', 'wpsos_kdc_add_settings_menu' );

function wpsos_kdc_display_settings(){
	global $WPSOS_KDC;
	$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
	$settings = $WPSOS_KDC->get_settings();
	?>
	<div id="wpsos" class="wrap wpsos_kdc">
		<h2>Keyword Counter & Density Calculator</h2>
		<h2 class="nav-tab-wrapper">
			<?php foreach( $WPSOS_KDC->tabs as $tab_key => $tab_caption ): ?>
        		<?php $active = $current_tab == $tab_key ? 'nav-tab-active' : ''; ?>
        		<a class="nav-tab <?php echo $active; ?>" href="?page=wpsos-kdc-settings&tab=<?php echo $tab_key; ?>"><?php echo $tab_caption; ?></a>
    		<?php endforeach; ?>
    	</h2>
    	<?php if( $current_tab == 'general' || $current_tab == 'advanced' ): ?>
		<div class="form-wrapper">
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<?php wp_nonce_field( 'wpsos-kdc' ); ?>
				<?php if( $current_tab == 'general' ): ?>
				<h3>General Settings</h3>
				<table class="form-table">
					<tr>
						<th>Exclude small words</th>
						<td><label>
								<input type="radio" class="enable" name="wpsos-kdc-exclude-small" value="1" <?php echo $settings['wpsos_kdc_exclude_small'] ? 'checked="checked"' : ''; ?>/>Exclude
							</label><br/>
							<label>
								<input type="radio" class="disable" name="wpsos-kdc-exclude-small" value="0" <?php echo !$settings['wpsos_kdc_exclude_small'] ? 'checked="checked"' : ''; ?> />Include
							</label>
							<p class="subnote">This will include/exclude small words, such as "a", "it", "the", "and", "but", etc.</p>
						</td>
					</tr>
					<tr>
						<th>Words to exclude<p class="subnote">Accepts single words only</p><p class="subnote">Free version allows up to 3 words. <a target="_blank" href="https://www.wpsos.io/wordpress-plugin-keyword-counter-and-density-calculator/">Upgrade now!</a></p></th>
						<td>
							<textarea name="wpsos-kdc-words-to-exclude"><?php echo $settings['words_to_exclude']; ?></textarea>
							<p class="subnote">Comma separated list of words which will be excluded from counting.</p>
						</td>
					</tr>
				</table>
				<?php endif; ?>
				<?php if( $current_tab == 'advanced' ): ?>
				<h3>Advanced Settings</h3>
				<strong>These features are available in the premium version only and listed here for informational purposes. <a target="_blank" href="https://www.wpsos.io/wordpress-plugin-keyword-counter-and-density-calculator/">Upgrade now!</a></strong>
				<table class="form-table">
					<tr>
						<th>How many words to list?<p class="subnote">Premium feature.</p></th>
						<td><label>
								<input type="text" disabled class="disabled" value="8" />
							</label><br/>
							<p class="subnote">Configure how many first words will be shown on counting.</p>
						</td>
					</tr>
					<tr>
						<th>Add/remove default small words to exclude<p class="subnote">Premium feature.</p></th>
						<td>
							<textarea disabled><?php echo $settings['small_words']; ?></textarea>
							<p class="subnote">Comma separated list of words considered "small words".</p>
						</td>
					</tr>
					<tr>
						<th>Keywords of more than 1 word to look for<p class="subnote">Premium feature.</p></th>
						<td><textarea disabled class="disabled" placeholder="wordpress security,barack obama,green button,for example"></textarea>
							<p class="subnote">Comma separated keywords consisting of more than one word</p>
						</td>
					</tr>
					<tr>
						<th>Word stemming<p class="subnote">Premium feature.</p></th>
						<td><label>
								<input type="radio" class="enable" disabled value="1" />Enable
							</label><br/>
							<label>
								<input type="radio" class="disable" disabled value="0" />Disable
							</label>
							<p class="subnote"> Enabling stemming will attempt to group together all plurals, conjugations, gerund and possessive words, to look only at the root. This is only an estimation, so it might occasionally result in roots that aren't words.</p>
						</td>
					</tr>
				</table>
				<h3>Specific stemming configuration</h3>
				<p class="subnote">Considered only if stemming is enabled.<p class="subnote">Premium feature.</p></p>
				<table class="form-table">
					<tr>
						<th>Word stemming: Gerund</th>
						<td><label>
								<input type="radio" class="enable" disabled value="1" />Enable
							</label><br/>
							<label>
								<input type="radio" class="disable" disabled value="0" />Disable
							</label>
							<p class="subnote"> Will attempt to group together all gerund words, to look only at the root. This is only an estimation, so it might occasionally result in roots that aren't words.</p>
						</td>
					</tr>
					<tr>
						<th>Word stemming: Conjugations</th>
						<td><label>
								<input type="radio" class="enable" disabled value="1" />Enable
							</label><br/>
							<label>
								<input type="radio" class="disable" disabled value="0" />Disable
							</label>
							<p class="subnote"> Will attempt to group together all gerund words, to look only at the root. This is only an estimation, so it might occasionally result in roots that aren't words.</p>
						</td>
					</tr>
					<tr>
						<th>Word stemming: Plural and Possessive</th>
						<td><label>
								<input type="radio" class="enable" disabled value="1" />Enable
							</label><br/>
							<label>
								<input type="radio" class="disable" disabled value="0" />Disable
							</label>
							<p class="subnote"> Will attempt to group together all plurals and possessive words, to look only at the root. This is only an estimation, so it might occasionally result in roots that aren't words.</p>
						</td>
					</tr>
				</table>
				<?php endif; ?>
				<p><input class="submit" type="submit" value="<?php _e( 'Save' ); ?>" id="wpsos-kdc-settings" name="wpsos-kdc-settings"></p>
			</form>
		</div>
		<?php endif; ?>
		<?php if( $current_tab == 'instructions' ): ?>
			<?php include_once('instructions.tpl.php'); ?>
		<?php endif; ?>
		<?php if( $current_tab == 'support' ): ?>
			<?php include_once('support.tpl.php'); ?>
		<?php endif; ?>
	</div>
	<?php
}
/**
 * Add links to WPSOS
 */
function wpsos_kdc_set_plugin_meta( $links, $file ) {

	if ( strpos( $file, 'keyword-counter-density-calculator.php' ) !== false ) {

		$links = array_merge( $links, array( '<a href="' . get_admin_url() . 'options-general.php?page=wpsos-kdc-settings">Settings</a>' ) );
		$links = array_merge( $links, array( '<a href="https://www.wpsos.io/">' . __( 'WPSOS - WordPress Security & Hack Repair' ) . '</a>' ) );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'wpsos_kdc_set_plugin_meta', 10, 2 );
