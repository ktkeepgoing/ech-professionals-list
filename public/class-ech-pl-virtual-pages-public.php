<?php

class Ech_PL_Virtual_Pages_Public {


    public static function dr_profile_output() {
		//$therapistid = get_query_var('therapistid', 'therapistid not set');
		
		/* $post = get_page_by_path('healthcare-professionals/professional-profile');
		if($post) {
			$pid = $post->ID;
		} else {
			$pid = 'ID not found';
		} */
		$therapistid = get_query_var('therapistid', 'none');

		if ( $therapistid === 'none' ) { echo '<script>window.location.replace("/healthcare-professionals");</script>'; }

		include('partials/ech-pl-single-profess-view.php');

		

		//return $html;
	}  //--end dr_profile_output()



    public function dr_category_list_output() {
        $specialistid = get_query_var('specialistid', 'none');

		if ( $specialistid === 'none' ) { echo '<script>window.location.replace("/healthcare-professionals");</script>'; }

		include('partials/ech-pl-category-list-view.php');
    } //dr_category_list_output


} // class