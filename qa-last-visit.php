<?php
class qa_html_theme_layer extends qa_html_theme_base
{
    public function initialize()
    {
		
        parent::initialize(); // Always call the parent

        if (qa_get_logged_in_userid()) {
            $userid = qa_get_logged_in_userid();
            $today = date('Y-m-d');
			$cookie_name = 'lastvisit_updated_' . $userid;
			$points_multiple = (int)qa_opt('points_multiple');
			$points_per_activeday = (int)qa_opt('points_per_activeday');
			$points_per_activeday_max = (int)qa_opt('points_per_activeday_max');

            if (!isset($_COOKIE[$cookie_name]) || $_COOKIE[$cookie_name] !== $today) {				
				$lastactivedate = qa_db_usermetas_get($userid, 'lastactivedate');
				
				setcookie($cookie_name, $lastactivedate, time() + 86400, '/');
				
				if($lastactivedate !== $today)
				{	
					qa_db_usermetas_set($userid, 'lastactivedate', $today);
					$Availed_days = (int)(qa_db_usermetas_get($userid, 'activedays') ?? 0);

					if((($Availed_days+1)*$points_per_activeday) <= $points_per_activeday_max)
						qa_db_usermetas_set($userid, 'activedays', $Availed_days+1);
						qa_db_query_sub('UPDATE ^userpoints SET activedays = #, points = points + # WHERE userid = #',$Availed_days+1,($points_per_activeday * $points_multiple	), $userid);
					
					qa_report_event('activeday_event', $userid,null,null,null);
					
					setcookie($cookie_name, $today, time() + 86400, '/');

					error_log("Updated lastactivedate for user". $userid ." in theme");
				}
			}
        }
    }
}
