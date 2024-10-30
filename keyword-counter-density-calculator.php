<?php
/*
 * Plugin Name: Keyword Counter And Density Calculator (Free)
 * Plugin URI: https://www.wpsos.io/wordpress-plugin-keyword-counter-and-density-calculator/
 * Description: The Keyword Counter & Density Calculator plugin calculates how many times and how commonly each keyword is used in a post or a page.
 * Version: 1.0
 * Author: WPSOS
 * Author URI: http://www.wpsos.io/
 * License: GPLv2 or later
 */
 
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

define('WPSOS_KDC_FILE', __FILE__);
require_once( __DIR__ . '/settings-page.php' );
global $WPSOS_KDC;
class WPSOS_KDC {
	
	public $tabs, $settings, $small_words;
	
	function __construct(){
		$this->tabs = array('general'=>'General Settings','advanced'=>'Pro Features','instructions'=>'Instructions','support'=>'Support');
		$this->settings = $this->get_settings();
		$this->small_words = explode(',', $this->settings['small_words'] );
		add_action( 'media_buttons', array($this, 'add_count_button'), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_plugin_scripts' ) );
		add_action( 'wp_ajax_get_word_counts', array( $this, 'get_word_counts' ) );
	}
	/**
	 * Add media button to the posts/pages edit interface
	 */
	function add_count_button(){
		global $post;
		echo '<a href="'. get_edit_post_link() . '&kdc-count=1" class="button wpsos_kdc_media_link">Count Keywords</a><div style="display:none;" id="wpsos-keyword-count"><p></p></div>';
		if( isset( $_GET['kdc-count'] ) ){
			echo '<div>';
			$text = strtolower( $post->post_content );
			echo $this->count_words( $text );
			echo '</div>';
		}
	}
	/**
	 * Load scripts
	 * @param String $hook_suffix
	 */
	function load_plugin_scripts( $hook ){
		if($hook == 'settings_page_wpsos-kdc-settings' || $hook == 'post.php' ) {
			wp_enqueue_style( 'wpsos-kdc-style', plugin_dir_url( WPSOS_KDC_FILE ) . 'css/style.css' );	
		}

		if( $hook == 'post.php' ){
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'wpsos-kdc-script', plugin_dir_url( WPSOS_KDC_FILE ) . 'js/script.js', array( 'jquery' ) );
		}
	}
	/**
	 * Get plugin settings
	 * @return mixed
	 */
	function get_settings(){
		$settings = get_option( 'wpsos_kdc_settings' );
		return unserialize( $settings );
	}
	
	function save_settings(){
		check_admin_referer( 'wpsos-kdc' );
		if( current_user_can( 'manage_options' ) ){
			if( isset( $_POST['wpsos-kdc-exclude-small'] ) ){
				$this->settings['wpsos_kdc_exclude_small'] = $_POST['wpsos-kdc-exclude-small'];
			}
			if( isset( $_POST['wpsos-kdc-words-to-exclude'] ) ){
				$to_exclude = strtolower( preg_replace("/\s+/", '', sanitize_text_field( $_POST['wpsos-kdc-words-to-exclude'] ) ) );
				$to_exclude = explode( ',', $to_exclude, 4);
				if( count($to_exclude > 3 ) ){
					array_pop( $to_exclude );
				}
				$this->settings['words_to_exclude'] = implode(',', $to_exclude );
			}
			update_option( 'wpsos_kdc_settings', serialize( $this->settings ) );
		}
	}
	
	function get_word_counts(){
		if(isset($_POST['text'])){
			$text = strtolower( $_POST['text'] );
			echo $this->count_words( $text );
		}
		wp_die();
	}
	
	function count_words( $text ){
		$text = preg_replace( "/\s+/", " ", $text );
		$words_array = explode(' ', trim( $text ));
		$wordcounts = array();
		$count=$count_wo_small=0;
		$excluded_words = $this->get_excluded_words();

		//Get keywords consisting of more than 1 word
		$expressions = array_map( 'trim', explode(',', $this->settings['multikeywords'] ) );
		$prev_word = false;
		foreach( $words_array as $word ){
			//Remove non-alphabetical characters
			$word = strtolower( preg_replace('~[^\p{L}\p{N}]++~u', '', $word) );
			//If the word remains empty, continue
			if( !strlen( trim( $word ) ) ) continue;
			
			$word = apply_filters( 'wpsos_kdc_processed_word', $word );
			$count++;
			if( !in_array( $word, $this->small_words ) ){
				$count_wo_small++;
			}
			if( in_array( $word, $excluded_words ) ) continue;
			if( isset( $wordcounts[$word] ) ){
				$wordcounts[$word]++;
			}
			else{
				$wordcounts[$word]=1;
			}
		}

		arsort( $wordcounts );
		$wordcounts = array_slice($wordcounts, 0, 8);
		$resp = $this->create_words_table( $wordcounts, $count, $count_wo_small );
		return $resp;
	}
	
	function create_words_table( $wordcounts, $total_count, $count_wo_small ){
		
		$resp = '<table id="kdc-counts"><thead><th>Keyword<br/></th><th>Count<br/></th><th>Density (excluding small words)<p class="subnote">Premium feature. <a target="_blank" href="https://www.wpsos.io/wordpress-plugin-keyword-counter-and-density-calculator/">Upgrade now!</a></p></th><th>Density (including small words)<p class="subnote">Premium feature. <a target="_blank" href="https://www.wpsos.io/wordpress-plugin-keyword-counter-and-density-calculator/">Upgrade now!</a></p></th>
				<th>SEO Friendliness<p class="subnote">Premium feature. <a target="_blank" href="https://www.wpsos.io/wordpress-plugin-keyword-counter-and-density-calculator/">Upgrade now!</a></p></th></thead>';
		$i=1;
		foreach( $wordcounts as $word=>$count ){
			if( $i<=3 ){
				$perc = round( ( $count/$total_count)*100, 3) . '%';
				$perc_wo_small = round( ( $count/$count_wo_small )*100, 2 ) . '%';
				$class='';
				if( $perc_wo_small < 0.667 ){
					$class='gray';
					$seo_comm='Irrelevant for keyword purposes.';
				}
				elseif( $perc_wo_small <= 1.333 ){
					$class='green';
					$seo_comm='Perfect!';
				}
				elseif( $perc_wo_small <= 2 ){
					$class='yellow';
					$seo_comm='Good but don\'t use it any more.';
				}
				else {
					$class='red';
					$seo_comm = 'Overuse alert!';
				}
			}
			else {
				$perc = $perc_wo_small = $seo_comm = 'Premium Only.';
				$class = '';
			}
			$resp.='<tr><td>'.$word.'</td><td>'.$count.'</td><td>'.$perc_wo_small.'</td><td>'.$perc.'</td><td class="'.$class.'">'.$seo_comm.'</td></tr>';
			$i++;
		}
		
		$resp.= '</table>';
		return $resp;
	}
	
	
	function get_excluded_words(){
		$excluded_words = '';
		if( $this->settings['wpsos_kdc_exclude_small'] ){
			$excluded_words .= $this->settings['small_words'];
		}
		return explode( ',', $excluded_words );
	}
	
	/**
	 * On plugin activation
	 */
	function activate(){
		//Create the default options
		$options = array( 'wpsos_kdc_exclude_small'=>1, 'words_to_list'=>8, 'words_to_exclude'=>'', 'multikeywords'=>'',
				'small_words'=>'in,the,at,by,of,for,to,a,an,have,had,their,there,my,your,his,her,it,its,its,too,that,both,is,was,are,were,i,you,he,she,him,we,they,us,more,here,other,another,and,this,these,those,any,some,but,be,not,do,dont,with,as,so,im,into,on,or,while,during,what,where,who,when,why,from,up,if,than,before,after,about,has,our,will,am,been,then,me,also,even,all,can,may,most,such,within,between,among,amongst,himself,herself,itself,themselves,theirselves,which,them,nor,toward,towards,could,always,anything,everything,like,out,would,now,only,almost,should,yet,often,shall,upon,own,till,thus,therefore,let,each,will,no,already',
				'stemming'=>0,'stemming_plural'=>1,'stemming_gerund'=>1,'stemming_conj'=>1
		);
		update_option( 'wpsos_kdc_settings', serialize( $options ), true );
	}
}
$WPSOS_KDC = new WPSOS_KDC();

//Register activation/deactivation functions
register_activation_hook( __FILE__, array( $WPSOS_KDC, 'activate' ) );
