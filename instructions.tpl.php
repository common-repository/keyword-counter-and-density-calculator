<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="instructions">
	<div class="form-wrapper">
		<h3>How does this work? We'll step you through it.</h3>
		<ol>
			<li>On the "General Settings" tab you can configure:
			<p>- If you want to exclude/include small words from keyword count;</p>
			<p>- The amount of top keywords that should be listed (defaults to 8);</p>
			<p>- Additional words to exclude. Note that these are separate from "small words" and the field accepts only single words not expressions.</p>
			<p>- Add/remove/modify "small words".</p>
			<p>- Manage keywords/keyphrases that consist of more than 1 word. By default the plugin only counts single words, for example if you'd like to look for a keyword "WordPress security" you can configure it here.</p>
			<img src="<?php echo plugin_dir_url( WPSOS_KDC_FILE ); ?>img/general-settings.png" alt="" /></li>
			<li>On the "Advanced" Tab you can configure word stemming. Enabling stemming will attempt to group together all plurals, conjugations, gerund and/or possessive words, to look only at the root. This is only an estimation, so it might occasionally result in roots that aren't words.
			<img src="<?php echo plugin_dir_url( WPSOS_KDC_FILE ); ?>img/advanced-settings.png" alt="" /></li>
			<li>For counting the keywords on a specific page/post, a button "Count Keywords" is added to the post/page edit view.
			<img src="<?php echo plugin_dir_url( WPSOS_KDC_FILE ); ?>img/page-edit.png" alt="" /></li>
			<li>Once you have clicked on the button, Keyword Density & Counter will display you the results.
			<img src="<?php echo plugin_dir_url( WPSOS_KDC_FILE ); ?>img/count.png" alt="" /></li>
		</ol> 
	</div>
	<div class="form-wrapper">
		<h3>For Developers</h3>
		<h4>Which filters can be used with the plugin?</h4>
		<ul>
			<li><strong>'wpsos_kdc_processed_word'</strong> - Filters the word after it has been processed by stemming</li>
			<li><strong>'wpsos_kdc_unprocessed_word'</strong> - Filters the word before it has been processed by stemming</li>
		</ul>	
	</div>
</div>