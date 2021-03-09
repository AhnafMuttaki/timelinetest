<?php
global $CFG,$USER,$DB;


/**
 * Attempttest log.
 *
 * @package   mod_timelinetest
 * @copyright 2021 Ahnaf Muttaki
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class attempttestlog {
    private $userid;
    private $timelinetestid;

    public function __construct($userid,$timelinetestid)
    {
        $this->userid = $userid;
        $this->timelinetestid = $timelinetestid;
    }

    /**
     * Called to get all the attempt logs of a user for a timelinetest
     *
     * @return array of StdClass
     */
    public function getlogs(){
        global $DB;
        $previousAttemptLogs = $DB->get_records_sql("SELECT * FROM {timelineattemptlog} WHERE userid=:userid AND timelinetestid=:timelinetestid", array('userid'=>$this->userid,'timelinetestid'=>$this->timelinetestid));
        return $previousAttemptLogs;
    }


    /**
     * Save log in DB
     * @param
     * @param $timelinetestid int ID of the timeline test
     * @param $timelinephase int ID of the timelinephase
     * @param $userid int ID of the USER
     * @param $phaseresponse String response of user in that attempt
     * @param $nextphase int ID of the next phase
     * @param $status int Status of the attempt. 0 for viewed and 1 for attempted
     * @param $obtainedmark int obtained mark in this attempt
     * @return int Log id
     */
    public function savelog(
        $timelinetestid,
        $timelinephase,
        $userid,
        $phaseresponse,
        $nextphase,
        $status,
        $obtainedmark
    ){
        global $DB;

        $attemptlog = new stdClass();
        $attemptlog->timelinetestid = $timelinetestid;
        $attemptlog->timelinephase = $timelinephase;
        $attemptlog->userid = $userid;
        $attemptlog->phaseresponse = $phaseresponse;
        $attemptlog->nextphase = $nextphase;
        $attemptlog->status = $status;
        $attemptlog->obtainedmark = $obtainedmark;
        $attemptlog->timecreated = time();

        $recordid = $DB->insert_record("timelineattemptlog", $attemptlog);
        return $recordid;
    }

    /**
     * Update log in DB
     * @param
     * @param $timelinetestid int ID of the timeline test
     * @param $timelinephase int ID of the timelinephase
     * @param $userid int ID of the USER
     * @param $phaseresponse String response of user in that attempt
     * @param $nextphase int ID of the next phase
     * @param $status int Status of the attempt. 0 for viewed and 1 for attempted
     * @param $obtainedmark int obtained mark in this attempt
     * @return void
     */
    public function updatelog(
        $timelinetestid,
        $timelinephase,
        $userid,
        $phaseresponse,
        $nextphase,
        $status,
        $obtainedmark
    ){
        global $DB;
        $sql = "UPDATE {timelineattemptlog} 
                SET phaseresponse=:phaseresponse,
                nextphase=:nextphase,
                status=:status,
                obtainedmark=:obtainedmark,
                timemodified=:timemodified 
                WHERE timelinetestid=:timelinetestid AND 
                timelinephase=:timelinephase AND 
                userid=:userid";
        $params = array(
            "phaseresponse" => $phaseresponse,
            "nextphase" => $nextphase,
            "status" => $status,
            "obtainedmark" => $obtainedmark,
            "timemodified" => time(),
            "timelinetestid" => $timelinetestid,
            "timelinephase" => $timelinephase,
            "userid" => $userid
        );
        $DB->execute($sql,$params);
    }


}