<?php

if (!class_exists('Activeday_Constants')) {
    require_once 'activeday_constants.php';
}

class PC_Integration_Activeday
{
    /**
     * This method name is used for this module to be found in the profile chart plugin.
     */
    public function pupi_pc_point_recalculation()
    {
    }

    /**
     * @return int
     */
    public function getFormulaId()
    {
        return Activeday_Constants::ACTIVEDAY_formula_id;
    }

    /**
     * @param int $formulaId
     * @param int $pointSign
     * @param string $eventFired
     * @param string $params
     *
     * @return int
     */
    public function getPoints($formulaId, $pointSign, $eventFired, $params)
    {
        return (int)((int) qa_opt('points_multiple')*((int) qa_opt(Activeday_Constants::OPT_POINTS_PER_DAY)) * (int)($params));
    }

    /**
     * @param int $formulaId
     * @param int $pointSign
     * @param string $eventFired
     * @param array $params
     *
     * @return string
     */
    public function getEventText($formulaId, $pointSign, $eventFired, $params)
    {
		$text_to_display="";
        if ((int)($params) == 1) {
            $text_to_display=qa_lang("activeday/Points_Added");
        } else if ((int)($params) ==0) {
           $text_to_display=qa_lang("activeday/Points_not_Added");
        } else {
            // should not happen
            $text_to_display = '';
        }

        return $text_to_display;
    }

    /**
     * Check if the event need to be processed. Return null if it doesn't need to or the full array with the info of
     * each event to log.
     *
     * @param string $event
     * @param int $userId
     * @param array $params
     *
     * @return array|null
     */
    public function checkEvent($event, $userId, $params)
    {
        if ($event !== Activeday_Constants::ACTIVEDAY_Event_name) {
            return null;
        }

        $pointSign = 1;

        return array(
            array(
                'event_time' => qa_opt('db_time'),
                'user_id' => $userId,
                'post_id' => null,
                'event_fired' => $event,
                'point_sign' => $pointSign,
                'formula' => $this->getFormulaId(),
                'params' => $params['points_counted'],
                ));
    }
}
