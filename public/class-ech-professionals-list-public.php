<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Ech_Professionals_List
 * @subpackage Ech_Professionals_List/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ech_Professionals_List
 * @subpackage Ech_Professionals_List/public
 * @author     Toby Wong <tobywong@prohaba.com>
 */
class Ech_Professionals_List_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	public $vp_body;
	public $vp_title;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ech-professionals-list-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ech-professionals-list-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-pagination', plugin_dir_url( __FILE__ ) . 'js/ech-pl-pagination.js', array( 'jquery' ), $this->version, false );

	}


	// Shortcode function
	public function echpl_display_profess_list($atts) {
		$paraArr = shortcode_atts(array(
			'ppp' => 12,
			'channel_id' => 4,
		), $atts);

		$ppp = (int)$paraArr['ppp'];
		$channel_id = (int)$paraArr['channel_id'];
	
		//$GLOBALS['ECHDr_ppp'] = $ppp;
		//$GLOBALS['ECHDr_channel_id'] = $channel_id;

	
		$api_args = array(
			'page_size'=>$ppp,
			'channel_id' => $channel_id,
		);

	
		$api_link = $this->ECHPL_gen_profList_api_link($api_args);

		$output = '';
		
		$output .= '<div class="ech_dr_big_wrap">'; 
		$output .= '<div class="echdr_page_anchor"></div>'; // anchor
		
		
		/*********** FITLER ************/
		$output .= '<div class="ech_dr_filter_container">';
		$output .= $this->ECHPL_get_regions();
		$output .= $this->ECHPL_get_spec();
		$output .= '<div class="dr_filter_btn_container"><div class="dr_filter_btn">'.$this->ECHPL_echolang(['Submit', '提交', '提交']).'</div></div>';
		$output .= '</div>'; //ech_dr_filter_container
		/*********** (END) FITLER ************/
		
		
		/*********** POST LIST ************/
		$output .= '<div class="ech_dr_container" >';
		$get_drList_json = $this->ECHPL_curl_json($api_link);
		$json_arr = json_decode($get_drList_json, true);
		/*** loading div ***/
		$output .= '<div class="loading_div"><p>'. $this->ECHPL_echolang(['Loading...','載入中...','载入中...']).'</p></div>';
		/*** (end) loading div ***/

		$output .= '<div class="all_drs_container" data-ppp="'.$ppp.'" data-channel="'.$channel_id .'" data-brandid="" data-region="" data-specialty="">';
			foreach ($json_arr['result'] as $dr) {
				$output .= $this->ECHPL_load_card_template($dr);
			}
		$output .= '</div>'; //all_posts_container


		/*** pagination ***/
		$total_posts = $json_arr['count'];
		$max_page = ceil($total_posts/$ppp);
		
		
		$output .= '<div class="ech_dr_pagination" data-current-page="1" data-max-page="'.$max_page.'" data-topage="" data-ajaxurl="'. get_admin_url(null, 'admin-ajax.php') .'"></div>';

		$output .= '</div>'; //ech_dr_container

		/*********** (END) POST LIST ************/

		$output .= '</div>'; //ech_dr_big_wrap


		return $output;
	} //echpl_display_profess_list



	/************************************
	 * Load more posts function
	 ************************************/
	public function ECHPL_load_more_dr() {
		$ppp = $_POST['ppp'];
		$toPage = $_POST['toPage'];
		$channel_id = $_POST['filterChannel'];
		$brand_id = $_POST['filterBrand'];
		$filterRegion = $_POST['filterRegion'];
		$filterSp = $_POST['filterSp'];
	
		$api_args = array(
			'page_size'=>$ppp,
			'page' => $toPage,
			'channel_id' => $channel_id,
			'brand_id' => $brand_id,
			'region' => $filterRegion,
			'specialty_id' => $filterSp,
		);
		$api_link = $this->ECHPL_gen_profList_api_link($api_args); 
	
		$get_dr_json = $this->ECHPL_curl_json($api_link); 
		$json_arr = json_decode($get_dr_json, true);
		
		$html = '';
		$max_page = '';
	
		if(isset($json_arr['result']) && $json_arr['count'] != 0 ) {
			$total_posts = $json_arr['count'];
			$max_page = round($total_posts/$ppp, 0);
	
			foreach ($json_arr['result'] as $dr) {
				$html .= $this->ECHPL_load_card_template($dr);
			}
		} else {
			$html .= $this->ECHPL_echolang(['No info ...' , '沒有資料', '没有资料']);
		}
	
		echo json_encode(array('html'=>$html, 'max_page' => $max_page), JSON_UNESCAPED_SLASHES);
	
		wp_die();
	}



	/***=========================== FILTERS ===========================***/
	public function ECHPL_get_regions() {
		$full_api = $this->ECHPL_get_api_domain() . '/v1/api/get_location_list?region_key=香港特别行政区';
		$get_regions_json = $this->ECHPL_curl_json($full_api);
		$json_arr = json_decode($get_regions_json, true);

		$html = '';

		$html .= '<div class="filter_regions_container">';
			$html .= '<div class="echdr_filter_dropdown_checkbox">';
				$html .= '<lable class="anchor">'.$this->ECHPL_echolang([ 'Select Region', '選擇地區', '选择地区']).'</lable>';
				$html .= '<ul class="echdr_dropdown_checkbox_list">';
					$html .= '';
					foreach($json_arr['result'] as $region) {
						$html .= '<li><label class="echdr_dropdown_checkbox_item"><input type="checkbox" name="region" class="region" value="'.$region['id'].'" data-region-value="" /> '.$this->ECHPL_echolang([ $region['en_atitle'], $region['tc_atitle'], $region['atitle']]).'</lable></li>';
					}
				$html .= '</ul>';
			$html .= '</div>';
		$html .= '</div>'; //filter_regions_container

		return $html;
	}


	public function ECHPL_get_spec() {
		$full_api = $this->ECHPL_get_api_domain() . '/v1/api/get_specialty_list?get_type=4&channel_id=4';
		$get_sp_json = $this->ECHPL_curl_json($full_api);
		$json_arr = json_decode($get_sp_json, true);

		$html = '';

		$html .= '<div class="filter_spec_container">';
			$html .= '<select name="spec" class="filter_spec">';
			$html .= '<option value="">'.$this->ECHPL_echolang(['All Specialty', '全部範圍', '全部范围']).'</option>';
			foreach($json_arr['result']['result'] as $sp) {
				$html .= '<option value="'.$sp['forever_specialty_id'].'">'.$this->ECHPL_echolang([ $sp['en_name'], $sp['tc_name'], $sp['cn_name'] ]).'</option>';
			}
			$html .= '</select>';
		$html .= '</div>'; //filter_spec_container

		return $html;
	}



	public function ECHPL_filter_dr_list() {
		$ppp = $_POST['ppp'];
		$filter_spec = $_POST['filter_spec'];
		$filter_region = $_POST['filter_region'];
	
		$api_args = array(
			'page_size'=>$ppp,
			'specialty_id' => $filter_spec,
			'region' => $filter_region
		);
		$api_link = $this->ECHPL_gen_profList_api_link($api_args);
		
	
		$get_dr_json = $this->ECHPL_curl_json($api_link);
		$json_arr = json_decode($get_dr_json, true);
		$html = '';
	
		
		$max_page = '';
		if(isset($json_arr['result']) && $json_arr['count'] != 0 ) {
			$total_posts = $json_arr['count'];
			$max_page = round($total_posts/$ppp, 0);
			foreach ($json_arr['result'] as $post) {
				$html .= $this->ECHPL_load_card_template($post);
			}
		} else {
			$html .= $this->ECHPL_echolang(['No posts ...' , '沒有文章', '没有文章']);
		}
		
		echo json_encode(array('html'=>$html, 'max_page' => $max_page, 'api' => $api_link), JSON_UNESCAPED_SLASHES);
	
		wp_die();
	}


	/***=========================== (END)FILTERS ===========================***/




	/***=========================== API LINKS ===========================***/
	
	/*********************************************
	 * Based on the dashboard settings, get api domain
	 *********************************************/
	public function ECHPL_get_api_domain() {
		$getApiEnv = get_option( 'ech_pl_apply_api_env' );
		if ( $getApiEnv == "0") {
			$api_domain = 'https://globalcms-api-uat.umhgp.com';
		} else {
			$api_domain = 'https://globalcms-api.umhgp.com';
		}

		return $api_domain;
	}

	/****************************************
	 * Filter and merge value and return a full API Doctor List link. 
	 * Array key: page, page_size, channel_id, specialty_id, brand_id, region
	 ****************************************/
	public function ECHPL_gen_profList_api_link(array $args) {
		

		$full_api = $this->ECHPL_get_api_domain() . '/v1/api/basic_doctor_list?';

		if(!empty($args['page'])) {
			$full_api .= 'page=' . $args['page'];
		} else {
			$full_api .= 'page=1';
		}
	
	
		if(!empty($args['page_size'])) {
			$full_api .= '&';
			$full_api .= 'page_size=' . $args['page_size'];
		} else {
			$full_api .= '&';
			$full_api .= 'page_size=12';
		}
	
	
		if(!empty($args['channel_id'])) {
			$full_api .= '&';
			$full_api .= 'channel_id=' . $args['channel_id'];
		} else {
			$full_api .= '&';
			$full_api .= 'channel_id=4';
		}
	
	
		if(!empty($args['specialty_id'])) {
			$full_api .= '&';
			$full_api .= 'specialty_id=' . $args['specialty_id'];
		}
	
		if(!empty($args['brand_id'])) {
			$full_api .= '&';
			$full_api .= 'brand_id=' . $args['brand_id'];
		}
	
		if(!empty($args['region'])) {
			$full_api .= '&';
			$full_api .= 'region=' . $args['region'];
		}

		

		return $full_api;
	}

	public function ECHPL_gen_single_dr_api_link(array $args){
		$full_api = $this->ECHPL_get_api_domain() . '/v1/api/basic_doctor?';
	
		if(!empty($args['therapistid'])) {
			$full_api .= 'therapistid=' . $args['therapistid'];
		} 
	
		if(!empty($args['personnel_id'])) {
			$full_api .= '&';
			$full_api .= 'personnel_id=' . $args['personnel_id'];
		}
	
		if(!empty($args['version'])) {
			$full_api .= '&';
			$full_api .= 'version=' . $args['version'];
		} 
	
		return $full_api;
	} 
	


	/***===========================(END) API LINKS ===========================***/

	
	/****************************************
	 * Load Single Post Template
	 ****************************************/
	public function ECHPL_load_card_template($dr) {
		$html = '';

		$avatar = $dr['avatar'];
		if($avatar == "") { $avatar = "../assets/img/medical-team.png"; }

		/***** SPECIALTY *****/
		$spArrEn = array();
		$spArrZH = array();
		$spArrSC = array();
		foreach($dr['specialty_list'] as $sp) {
			array_push($spArrEn, array('type'=>'sp', 'sp_id'=>$sp['specialty_id'], 'sp_name'=> $sp['en_name']) );
			array_push($spArrZH, array('type'=>'sp', 'sp_id'=>$sp['specialty_id'], 'sp_name'=> $sp['tc_name']));
			array_push($spArrSC, array('type'=>'sp', 'sp_id'=>$sp['specialty_id'], 'sp_name'=> $sp['cn_name']));
		}
		/***** (END) SPECIALTY *****/


		$html .= '<div class="single_dr_card">';
		
			$html .= '<div class="dr_avatar"><a href="'.site_url().'/healthcare-professionals/professional-profile/'.$dr['therapistid'].'"><img src="'.$avatar.'" /></a></div>';
			$html .= '<div class="dr_name"><a href="'.site_url().'/healthcare-professionals/professional-profile/'.$dr['therapistid'].'">'.$this->ECHPL_echolang([ $dr['en_salutation'].' '.$dr['en_name'], $dr['tc_name'].$dr['tc_salutation'],  $dr['cn_name'].$dr['cn_salutation']]).'</a></div>';
			$html .= '<div class="specialty"><strong>'.$this->ECHPL_echolang(['Specialist','專科','专科']).': </strong>'.$this->ECHPL_echolang([$this->ECHPL_apply_comma_from_array($spArrEn), $this->ECHPL_apply_comma_from_array($spArrZH), $this->ECHPL_apply_comma_from_array($spArrSC)]).'</div>';
			
		$html .= '</div>'; //single_dr_card

		return $html;
	}


	



	/**************************************
	 * Translation function
	 **************************************/
	public function ECHPL_echolang($stringArr) {
		global $TRP_LANGUAGE;
	
		switch ($TRP_LANGUAGE) {
			case 'zh_HK':
				$langString = $stringArr[1];
				break;
			case 'zh_CN':
				$langString = $stringArr[2];
				break;
			default:
				$langString = $stringArr[0];
		}
	
		if(empty($langString) || $langString == '' || $langString == null) {
			$langString = $stringArr[1]; //zh_HK
		}
	
		return $langString;
	
	}

	/****************************************
	 * This function is used to create a comma sparated list from an array for list categories / tags display
	 ****************************************/
	public function ECHPL_apply_comma_from_array($langArr) {
		$prefix = $commaList = '';

		foreach($langArr as $itemArr) {
			if($itemArr['type'] == 'sp') {
				$commaList .= $prefix . '<a href="'.site_url().'/healthcare-professionals/specialist-categories/'.$itemArr['sp_id'].'">' . $itemArr['sp_name']. '</a>';
			} else {
				$type = 'cate_id=';
			}

			$prefix = ", ";
		}

		return $commaList;
	}



    public function ECHPL_replace_newline($s) {
        $s = preg_replace("/\r\n|\r|\n/", '<br/>', $s);
	    return $s;
    }


    public function ECHPL_clinic_locations($addressArr) {
        $location = '';
        for($i = 0; $i < count($addressArr); $i++) {
            $location .= $addressArr[$i];
        }
        
        return $location;
    }



	/**************************************
	 * Curl function
	 **************************************/
	public function ECHPL_curl_json($api_link) {
		$ch = curl_init();
	
		$api_headers = array(
			'accept: application/json',
			'version: v1',
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $api_headers);
		curl_setopt($ch, CURLOPT_URL, $api_link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
	
		return $result;
	}


}

