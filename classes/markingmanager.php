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

    public function getmark(){
        global $DB;
        $previousmark = $DB->get_record_sql("SELECT * FROM {timelinetotalmark} WHERE timelinetestid=:timelinetestid AND userid=:userid", array('timelinetestid'=>$this->timelinetestid,'userid'=>$this->userid));

        return $previousmark->obtainedmark;
    }

}