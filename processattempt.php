<?php
global $CFG,$USER,$DB;
require_once("../../config.php");
require_once($CFG->dirroot. '/mod/timelinetest/classes/attempttestlog.php');
require_once($CFG->dirroot. '/mod/timelinetest/classes/markingmanager.php');

$cmid = optional_param('cmid', 0, PARAM_INT); // Course Module ID
$userid = optional_param('userid', 0, PARAM_INT); // USER ID
$timelinetestid = optional_param('timelinetestid', 0, PARAM_INT); // Timelinetestid
$phaseid = optional_param('timelinephase', 0, PARAM_INT); // Timelinephase
$phaseresponse = optional_param('phaseresponse', 0, PARAM_TEXT); // User Phase Response

//var_dump($timelinetestid);
//echo "<br/>";
//var_dump($phaseid);
//echo "<br/>";
//var_dump($phaseresponse);
//die();

if ($cmid) {
    if (!$cm = get_coursemodule_from_id('timelinetest', $cmid)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
} else {
    print_error('invalidcoursemodule');
}
//var_dump($userid);
//echo "<br/>";
//var_dump($USER->id);
//die();

//if($USER->id !== (int)$userid){
//    // User not found
//    throw new ddl_exception(get_string('error:invalid user', 'timelinetest'));
//}

if(!$timelinetest = $DB->get_record_sql("SELECT * FROM {timelinetest} WHERE id=:timelinetestid", array('timelinetestid'=>$timelinetestid))){
    // Timeline test not found
    throw new ddl_exception(get_string('error:invalid user', 'timelinetest'));
}

if(!$timelinephase = $DB->get_record_sql("SELECT * FROM {timelinephases} WHERE id=:phaseid", array('phaseid'=>$phaseid))){
    // Timeline test not found
    throw new ddl_exception(get_string('error:invalid phase', 'timelinetest'));
}

if(!$attemptlog = $DB->get_record_sql("SELECT * FROM {timelineattemptlog} WHERE timelinetestid=:timelinetestid AND timelinephase=:phaseid AND userid=:userid", array('timelinetestid'=>$timelinetestid,'phaseid'=>$phaseid,'userid'=>$USER->id))){
    // Timeline test not found
    throw new ddl_exception(get_string('error:invalid attempt', 'timelinetest'));
}

if(!$chosenoption = $DB->get_record_sql("SELECT * FROM {timelineoptions} WHERE timelinetestid=:timelinetestid AND timelinephase=:phaseid AND description=:phaseresponse", array('timelinetestid'=>$timelinetestid,'phaseid'=>$phaseid,'phaseresponse'=>$phaseresponse))){
    // Timeline test not found
    throw new ddl_exception(get_string('error:invalid option', 'timelinetest'));
}

// Per phase full mark
$fullmark = 0;
if($timelinephase->type == "Informative"){
    $fullmark = 0;
}
else{
    $fullmark = 10;
}

$maxmarkpercentage = $chosenoption->maxmark;
$obtainedmark = $fullmark * ($maxmarkpercentage/100);

$nextphase = $chosenoption->nextphase;

$attempttestlog = new attempttestlog($USER->id,$timelinetestid);
$attempttestlog->updatelog($timelinetestid,$phaseid,$USER->id,$phaseresponse,$nextphase,1,$obtainedmark);

$markingmanager = new markingmanager($USER->id,$timelinetestid);
$markingmanager->updatemarking($obtainedmark);

redirect($CFG->wwwroot."/mod/timelinetest/timelinetestattempt.php?id=$cmid", get_string('success:attemptsave', 'timelinetest'));