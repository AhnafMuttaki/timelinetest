<?php
global $CFG,$USER,$DB;
class markingmanager{
    private $userid;
    private $timelinetestid;

    public function __construct($userid,$timelinetestid)
    {
        $this->userid = $userid;
        $this->timelinetestid = $timelinetestid;
    }

    public function initiatemarking(){
        global $DB;
        $score = new stdClass();
        $score->timelinetestid = $this->timelinetestid;
        $score->userid = $this->userid;
        $score->obtainedmark = 0;
        $score->timecreated = time();
        $DB->insert_record("timelinetotalmark", $score);
    }

}