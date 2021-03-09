<?php
global $CFG,$USER,$DB;
require_once("../../config.php");
require_once($CFG->dirroot. '/mod/timelinetest/classes/attempttestlog.php');
require_once($CFG->dirroot. '/mod/timelinetest/classes/markingmanager.php');
require_once($CFG->dirroot. '/mod/timelinetest/classes/timelinehtmlbuilder.php');

require_login();
$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or ...

if ($id) {
    if (!$cm = get_coursemodule_from_id('timelinetest', $id)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
} else {
    print_error('invalidcoursemodule');
}

// Check login and get context.
require_login($course, false, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/timelinetest:attempt', $context);

$PAGE->set_url('/mod/timelinetest/timelinetestattempt.php', array('id' => $cm->id));
$title = $course->shortname . ': '.$cm->name.":"."Attempt";
$PAGE->set_context($context);
$PAGE->set_title($title);

// Get timelinetest data
$timelinetest = $DB->get_record_sql("SELECT * FROM {timelinetest} WHERE id=:id", array('id'=>$cm->instance));
if(!$timelinetest){
    print_error('invalidcoursemodule');
}

//Find attempt log for that user for that test.
$userid = $USER->id;
$timelinetestid = $timelinetest->id;
$attempttestlog = new attempttestlog($userid,$timelinetestid);
$previousAttemptLogs = $attempttestlog->getlogs();

$timelinephases = array();
$markingmanager = new markingmanager($userid,$timelinetestid);
if(count($previousAttemptLogs)>0){
    //	if found:
    //        Check the status of last log.
    //		if viewed:
    //			update the timemodified -> show timeline upto this
    //		if attempted:
    //            get the next phaseid -> get next phase data -> insert log as viewed -> show timeline upto this
    $lastattemptstatus = 0;
    $nextphaseid = 0;
    foreach ($previousAttemptLogs as $attempt){
        $tempphase = $DB->get_record_sql("SELECT * FROM {timelinephases} WHERE id=:id ORDER BY id ASC LIMIT 1", array('id'=>$attempt->timelinephase));
        $tempphase->attemptlogid = $attempt->id;
        array_push($timelinephases,$tempphase);
        $lastattemptstatus = $attempt->status;
        $nextphaseid = $attempt->nextphase;
    }

    if($lastattemptstatus == 1){
        // if last log is attempted
        if($nextphaseid !== "-1"){
            // add next phase
            $tempphase = $DB->get_record_sql("SELECT * FROM {timelinephases} WHERE id=:id ORDER BY id ASC LIMIT 1", array('id'=>$nextphaseid));

            //        Insert viewed log
            $attemptlogid = $attempttestlog->savelog($timelinetestid,$tempphase->id,$userid,"",0,0,0);
            $tempphase->attemptlogid = $attemptlogid;

            array_push($timelinephases,$tempphase);
        }
        else{
            // add finish
//            echo "finish timeline";
//            var_dump($nextphaseid);
//            die();

            $finishphase = new stdClass();
            $finishphase->id = "-1";
            array_push($timelinephases,$finishphase);

        }
    }

}
else{
    //	else:
    //        Get the first phase of the timeline test
    $firstphase = $DB->get_record_sql("SELECT * FROM {timelinephases} WHERE timelinetestid=:timelinetestid ORDER BY id ASC LIMIT 1", array('timelinetestid'=>$timelinetestid));
    //        Insert viewed log
    $attemptlogid = $attempttestlog->savelog($timelinetestid,$firstphase->id,$userid,"",0,0,0);
    $firstphase->attemptlogid = $attemptlogid;
    array_push($timelinephases,$firstphase);
    //        Initiate Marking
    $markingmanager->initiatemarking();
    //        Add phase in timeline HTML


}
$timelinephases = array_reverse($timelinephases);

$timelinebuilder = new timelinehtmlbuilder("",$id);
$timelinehtml = $timelinebuilder->buildtimeline($timelinephases,$userid);
$score = $markingmanager->getmark();

$templatecontext = (object)[
    'timelinehtml' => $timelinehtml,
    'addphaseurl' => new moodle_url("/mod/timelinetest/addphase.php?id=$id"),
    'score' => $score
];

echo $OUTPUT->render_from_template('mod_timelinetest/attempt', $templatecontext);
