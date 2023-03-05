<?php

/**
 * Fired during plugin activation
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Ech_Professionals_List
 * @subpackage Ech_Professionals_List/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ech_Professionals_List
 * @subpackage Ech_Professionals_List/includes
 * @author     Toby Wong <tobywong@prohaba.com>
 */
class Ech_Professionals_List_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// connect to LIVE API when first activate
		$getApiEnv = get_option( 'ech_pl_apply_api_env' );
		if(empty($getApiEnv) || !$getApiEnv ) {
			add_option( 'ech_pl_apply_api_env', 1 );
		}

        // set post per page to 12
        $getPPP = get_option( 'ech_pl_ppp' );
        if(empty($getPPP) || !$getPPP ) {
            add_option( 'ech_pl_ppp', 12 );
        }

		// Create VP
        self::createVP('Healthcare Professional Profile', 'professional-profile', '[dr_profile_output]');
        self::createVP('Healthcare Professional Categories', 'specialist-categories', '[dr_category_list_output]');		

	} //activate


    private function createVP($pageTitle, $pageSlug, $pageShortcode) {
        if ( current_user_can( 'activate_plugins' ) ) {
			// Get parent page and get its id
			$get_parent_page = get_page_by_path('healthcare-professionals');
	
			$v_page = array(
				'post_type' => 'page',
				'post_title' => $pageTitle,
				'post_name' => $pageSlug,
				'post_content' => $pageShortcode,  // shortcode from this plugin
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
				'post_parent' => $get_parent_page->ID
			);
	
			$vp_id = wp_insert_post($v_page, true);
	
		} else {
			return;
		}
    } // createVP






}
