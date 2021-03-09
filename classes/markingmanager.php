<?php
global $CFG,$USER,$DB;
/**
 * Marking Manager.
 *
 * @package   mod_timelinetest
 * @copyright 2021 Ahnaf Muttaki
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class markingmanager{
    private $userid;
    private $timelinetestid;

    public function __construct($userid,$timelinetestid)
    {
        $this->userid = $userid;
        $this->timelinetestid = $timelinetestid;
    }

    /**
     * Called to initiate the marking
     *
     * @return void
     */
    public function initiatemarking(){
        global $DB;
        $score = new stdClass();
        $score->timelinetestid = $this->timelinetestid;
        $score->userid = $this->userid;
        $score->obtainedmark = 0;
        $score->timecreated = time();
        $DB->insert_record("timelinetotalmark", $score);
    }

    /**
     * Called to initiate the marking
     * @param $obtainedmark int Mark obtained in one phase.
     * @return void
     */
    public function updatemarking($obtainedmark){
        global  $DB;
        if(!$previousmark = $DB->get_record_sql("SELECT * FROM {timelinetotalmark} WHERE timelinetestid=:timelinetestid AND userid=:userid", array('timelinetestid'=>$this->timelinetestid,'userid'=>$this->userid))){
            // Timeline test not found
            throw new ddl_exception(get_string('error:invalid option', 'timelinetest'));
        }
        $totalmark = $previousmark->obtainedmark;
        $totalmark = $totalmark + $obtainedmark;

        $sql = "UPDATE {timelinetotalmark} 
                SET obtainedmark=:totalmark,
                timemodified=:timemodified 
                WHERE timelinetestid=:timelinetestid AND 
                userid=:userid";
        $params = array(
            "timelinetestid" => $this->timelinetestid,
            "userid" => $this->userid,
            "totalmark" => $totalmark,
            "timemodified" =>time()
        );
        $DB->execute($sql,$params);
    }

    /**
     * Called to initiate the marking
     *
     * @return int Current Mark
     */
    public function getmark(){
        global $DB;
        $previousmark = $DB->get_record_sql("SELECT * FROM {timelinetotalmark} WHERE timelinetestid=:timelinetestid AND userid=:userid", array('timelinetestid'=>$this->timelinetestid,'userid'=>$this->userid));
        return $previousmark->obtainedmark;
    }

}