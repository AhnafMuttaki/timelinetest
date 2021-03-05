<?php
global $CFG,$USER,$DB;

class attempttestlog {
    private $userid;
    private $timelinetestid;

    public function __construct($userid,$timelinetestid)
    {
        $this->userid = $userid;
        $this->timelinetestid = $timelinetestid;
    }

    public function getlogs(){
        global $DB;
        $previousAttemptLogs = $DB->get_records_sql("SELECT * FROM {timelineattemptlog} WHERE userid=:userid AND timelinetestid=:timelinetestid", array('userid'=>$this->userid,'timelinetestid'=>$this->timelinetestid));
        return $previousAttemptLogs;
    }

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

        $DB->insert_record("timelineattemptlog", $attemptlog);
    }


}