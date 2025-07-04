<?php

if (!class_exists('Activeday_Constants')) {
    require_once 'activeday_constants.php';
}

class qa_html_theme_layer extends qa_html_theme_base
{
    public function initialize()
    {
		
        parent::initialize(); // Always call the parent
		require_once QA_INCLUDE_DIR . 'db/points.php';
		
		error_log(Activeday_Constants::ACTIVEDAY_Event_name);
        if (qa_get_logged_in_userid()) {
            $userid = qa_get_logged_in_userid();
            $today = date('Y-m-d');
			$cookie_name = 'lastvisit_updated_' . $userid;
			$points_multiple = (int)qa_opt('points_multiple');
			$points_per_activeday = (int) qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY);
			$points_per_activeday_max = (int) qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY_MAX);
			$max_days_to_be_availed = floor($points_per_activeday_max / max($points_per_activeday, 1));


            if (!isset($_COOKIE[$cookie_name]) || $_COOKIE[$cookie_name] !== $today) {				
				$lastactivedate = qa_db_usermetas_get($userid, Activeday_Constants::usermeta_title_ACTIVEDAYS);
				
				if($lastactivedate !== $today)
				{	
					qa_db_usermetas_set($userid, Activeday_Constants::usermeta_title_ACTIVEDAYS, $today);
					$Availed_days = (int)(qa_db_usermetas_get($userid, Activeday_Constants::COLUMN_ACTIVEDAYS) ?? 0);
					qa_db_usermetas_set($userid, Activeday_Constants::COLUMN_ACTIVEDAYS, $Availed_days+1);
					qa_db_points_update_ifuser($userid, Activeday_Constants::COLUMN_ACTIVEDAYS);
					
					//To sync with the profile chart plugin. 1 means points are given on that day. 0 means points not given.
					$points_counted="1";
					if($Availed_days+1>$max_days_to_be_availed)
						$points_counted="0";
					$params = [
						'points_counted' => $points_counted
					];
					
					//report the event to process					
					qa_report_event(Activeday_Constants::ACTIVEDAY_Event_name, $userid,null,null,$params);
					
					setcookie($cookie_name, $today, time() + 86400, '/');
				}
			}
        }
    }
}
