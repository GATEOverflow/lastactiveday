<?php

if (!class_exists('Activeday_Constants')) {
    require_once 'activeday_constants.php';
}

function qa_db_points_calculations(){

	$orig =  qa_db_points_calculations_base();

	$options=qa_get_options(qa_db_points_option_names());

	$per_day = (int) qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY);
	$max= floor((int) qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY_MAX) / max($per_day, 1));

	$orig [Activeday_Constants::COLUMN_ACTIVEDAYS] =  array(
	'multiple' => $options['points_multiple'] * $per_day,
	'formula' => "COALESCE((SELECT LEAST(CAST(userid_src.content AS SIGNED),". $max.") AS ".Activeday_Constants::COLUMN_ACTIVEDAYS." FROM ^usermetas AS userid_src WHERE userid_src.title='".Activeday_Constants::COLUMN_ACTIVEDAYS."' AND userid_src.userid~), 0)",
	);	
	return $orig;
}
?>
