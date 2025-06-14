<?php
if (!defined('QA_VERSION')) {
    header('Location: ../../');
    exit;
}

require_once 'activeday_constants.php';


qa_register_plugin_phrases('activeday-lang.php', 'activeday');
qa_register_plugin_layer('qa-lastvisit-layer.php', 'Runs on every page to check the last active day');
qa_register_plugin_module('page', 'admin-page-activityday.php', 'admin_page_activityday', 'DB and options Setup');
qa_register_plugin_overrides('qa-activeday-overrides.php', 'To override the points calculation function');
qa_register_plugin_module('process', 'PC_Integration_Activeday.php', 'PC_Integration_Activeday', 'Activeday points display in the Profile Chart');   


//db functions to be used in this plugin
function qa_db_usermetas_get($userid, $title) {
    return qa_db_read_one_value(
        qa_db_query_sub(
            'SELECT content FROM ^usermetas WHERE userid = # AND title = $',
            $userid, $title
        ),
        true
    );
}

function qa_db_usermetas_set($userid, $title, $content) {
    qa_db_query_sub(
        'REPLACE INTO ^usermetas (userid, title, content) VALUES (#, $, $)',
        $userid, $title, $content
    );
}

function qa_db_usermetas_clear($userid, $title) {
    qa_db_query_sub(
        'DELETE FROM ^usermetas WHERE userid = # AND title = $',
        $userid, $title
    );
}

//These db functions used for updating the history tables according to the updated maximum points - used for syncing the profile charts

function pupi_pc_get_users_with_activeday_event() {
    return qa_db_read_all_values(
        qa_db_query_sub(
            'SELECT DISTINCT user_id FROM ^pupi_pc_history WHERE event_fired = $',
            Activeday_Constants::ACTIVEDAY_Event_name
        )
    );
}


function pupi_pc_get_activeday_events_for_user($userId) {
    return qa_db_read_all_assoc(
        qa_db_query_sub(
            'SELECT id FROM ^pupi_pc_history
             WHERE user_id = # AND event_fired = $
             ORDER BY DATE(event_time)',
            $userId, Activeday_Constants::ACTIVEDAY_Event_name
        )
    );
}


function pupi_pc_update_event_param_by_id($rowid, $param) {
    qa_db_query_sub(
        'UPDATE ^pupi_pc_history SET params = $ WHERE id = #',
        $param, $rowid
    );
}
