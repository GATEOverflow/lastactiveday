<?php

function qa_db_points_calculations(){

	$orig =  qa_db_points_calculations_base();

	$options=qa_get_options(qa_db_points_option_names());


	$orig ['activedays'] =  array(
    'multiple' => $options['points_multiple'] * (int) qa_opt('points_per_activeday'),
    'formula' => "LEAST(CAST(userid_src.content AS SIGNED), " . floor(((int) qa_opt('points_per_activeday_max')) / max((int) qa_opt('points_per_activeday'), 1)) . ") AS activedays FROM ^usermetas AS userid_src WHERE userid_src.title='activedays' AND userid_src.userid~",
	);	
	return $orig;
}
?>
