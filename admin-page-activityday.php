<?php
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}
class admin_page_activityday
{
 	function option_default($option) {

		switch($option) {
			case 'points_per_activeday': 
				return 10;
			case 'points_per_activeday_max': 
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
			$newcolumns = array('activedays');
			foreach ($newcolumns as $column){
				if(!in_array($column, $fields)) {
					$queries [] =  'ALTER TABLE '.$tablename.' ADD '.$column. ' MEDIUMINT(9)  NOT NULL DEFAULT 0;';
				}
			}
			return $queries;
	}

	function admin_form(&$qa_content)
	{                       

		// Process form input

		$ok = null;

		if (qa_clicked('qa_activityday_save')) {
			qa_opt('points_per_activeday',qa_post_text('points_per_activeday'));
			qa_opt('points_per_activeday_max',qa_post_text('points_per_activeday_max'));
			$ok = qa_lang('bp_lang/savemsg');
		}

		// Create the form for display

		$fields = array();
		$fields[] = array(
			'label' => qa_lang_html('activeday/points_per_day'),
			'tags' => 'name="points_per_activeday"',
			'value' => qa_opt('points_per_activeday'),
			'type' => 'number',
			);
		$fields[] = array(
			'label' => qa_lang_html('activeday/points_per_day_max'),
			'tags' => 'name="points_per_activeday_max"',
			'value' => qa_opt('points_per_activeday_max'),
			'type' => 'number',
			);

		return array(           
				'ok' => ($ok && !isset($error)) ? qa_lang_html('activeday/savemsg') : null,

				'fields' => $fields,

				'buttons' => array(
					array(
						'label' => qa_lang_html('activeday/save_button'),
						'tags' => 'NAME="qa_activityday_save"',
						 ),
					),
				);
	}

}
