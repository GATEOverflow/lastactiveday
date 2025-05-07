<?php
if (!defined('QA_VERSION')) {
    header('Location: ../../');
    exit;
}



qa_register_plugin_layer('qa-last-visit.php', 'Runs on every page to check the last active day');
qa_register_plugin_module('page', 'admin-page-activityday.php', 'admin_page_activityday', 'DB and options Setup');
qa_register_plugin_overrides('qa-activeday-overrides.php', 'Points for each Active day');
qa_register_plugin_module('event', 'qa-event-logger.php', 'qa_activeday_event_logger', 'Active Day Event Logger');
qa_register_plugin_phrases('qa-lang-*.php', 'activeday');   

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
