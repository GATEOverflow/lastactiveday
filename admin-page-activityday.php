<?php
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

if (!class_exists('Activeday_Constants')) {
    require_once 'activeday_constants.php';
}

class admin_page_activityday
{
 	function option_default($option)
	{
		switch ($option) {
			case Activeday_Constants::OPT_POINTS_PER_DAY:
				return 10;

			case Activeday_Constants::OPT_POINTS_PER_DAY_MAX:
				return 10000;

			default:
				return null;
		}
	}


	function allow_template($template)
	{
		return ($template!='admin');
	}

	public function init_queries($tableslc)
	{
			$queries = array();
			$tablename = qa_db_add_table_prefix('userpoints');


			$sqlCols = 'SHOW COLUMNS FROM '.$tablename;
			$fields = qa_db_read_all_values(qa_db_query_sub($sqlCols));
			$newcolumns = array(Activeday_Constants::COLUMN_ACTIVEDAYS);
			foreach ($newcolumns as $column){
				if(!in_array($column, $fields)) {
					$queries [] =  'ALTER TABLE '.$tablename.' ADD '.$column. ' MEDIUMINT(9)  NOT NULL DEFAULT 0;';
				}
			}
			return $queries;
	}

	//This function is for syncing the points with the profile chart plugin.
	function update_activeday_point_flags_pc($cap) {
		$users = pupi_pc_get_users_with_activeday_event();

		foreach ($users as $userId) {
			$rows = pupi_pc_get_activeday_events_for_user($userId);

			foreach ($rows as $index => $row) {
				$param = ($index < $cap) ? '1' : '0';
				pupi_pc_update_event_param_by_id($row['id'], $param);
			}
		}
	}



	function admin_form(&$qa_content)
	{                       


		// Process form input

		$ok = null;

		if (qa_clicked('qa_activityday_save')) {
			require_once QA_INCLUDE_DIR . 'app/users.php'; // Needed for point update
			require_once QA_INCLUDE_DIR . 'db/points.php'; // Needed for points recalculation
			
			qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY,(int)qa_post_text('points_per_activeday'));
			qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY_MAX,(int)qa_post_text('points_per_activeday_max'));
			
			//forcefully recalculate activedays points once points per activeday or Maximum number of points are updated
			$users = qa_db_read_all_values(qa_db_query_sub('SELECT userid FROM ^users'));
			foreach ($users as $userid) {
				qa_db_points_update_ifuser($userid, Activeday_Constants::COLUMN_ACTIVEDAYS);
			}
			
			//Update the active day history in the pupi_pc_history table for syncying with the profile chart
			$max_days_to_be_availed=floor(((int) qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY_MAX)) / max((int) qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY), 1));
			$this->update_activeday_point_flags_pc($max_days_to_be_availed);
			
			
			$ok = qa_lang('activeday/savemsg');
		}

		

		// Create the form for display

		$fields = array();
		$fields[] = array(
			'label' => qa_lang_html('activeday/points_per_day'),
			'tags' => 'name="points_per_activeday"',
			'value' => qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY),
			'type' => 'number',
			);
		$fields[] = array(
			'label' => qa_lang('activeday/points_per_day_max'),
			'tags' => 'name="points_per_activeday_max"',
			'value' => qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY_MAX),
			'type' => 'number',
			);

		return array(           
				'ok' => ($ok && !isset($error)) ? qa_lang_html('activeday/savemsg') : null,

				'fields' => $fields,

				'buttons' => array(
					array(
						'label' => qa_lang('activeday/save_button'),
						'tags' => 'NAME="qa_activityday_save"',
						 ),
					),
				);
	}

}
