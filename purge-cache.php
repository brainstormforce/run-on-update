<?php
/*
 * Plugin Name: Purge Cache
 * Plugin URI: http://www.brainstormforce.com/
 * Description: Notify webpagetest report to your slack channel
 * Author: Brainstorm Force
 * Version: 1.0.0
 * Author URI: http://www.brainstormforce.com/
 * Text Domain: purge-cache
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'BSFPurgeCache' ) ) {

	class BSFPurgeCache {

		private static $instance;

		public static function instance() {
			
			if ( ! isset( self::$instance ) ) {
				self::$instance = new BSFPurgeCache();
				self::$instance->hooks();
			}
			return self::$instance;
		}

		public function hooks() {
			// Runs when the plugin is upgraded.
			add_action( 'upgrader_process_complete', array( $this, 'bsf_purge_cache' ), 99 );
			add_action( 'save_post', array( $this, 'bsf_purge_cache' ), 99 );
			// Fires once an attachment has been added.
			add_action( 'add_attachment', array( $this, 'bsf_purge_cache' ), 99 );
		}

		public function bsf_purge_cache() {

			if ( class_exists( 'FLBuilderModel' ) ) {

				FLBuilderModel::delete_asset_cache_for_all_posts();
			}

			if ( class_exists( 'Nginx_Helper_WP_CLI_Command' ) ) {
				
				global $rt_wp_nginx_purger;
				$rt_wp_nginx_purger->true_purge_all();
			}
		}
	}
}
function bsf_purge_cache_init_plugin() {

	$BSFPurgeCache = BSFPurgeCache::instance();
}
add_action( 'plugins_loaded', 'bsf_purge_cache_init_plugin' );