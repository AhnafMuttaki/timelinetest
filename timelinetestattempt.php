<?php
global $CFG,$USER,$DB;
require_once("../../config.php");
require_once($CFG->dirroot. '/mod/timelinetest/classes/attempttestlog.php');
require_once($CFG->dirroot. '/mod/timelinetest/classes/markingmanager.php');
require_once($CFG->dirroot. '/mod/timelinetest/classes/timelinehtmlbuilder.php');

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
    // If attempt log has more than 1 rows then every attempt phase needs to be added in timeline.

    // Last attempt status and next phase id needs to be initialized.
    $lastattemptstatus = 0;
    $nextphaseid = 0;

    foreach ($previousAttemptLogs as $attempt){
        // Add phase in timeline for each attempt log
        $tempphase = $DB->get_record_sql("SELECT * FROM {timelinephases} WHERE id=:id ORDER BY id ASC LIMIT 1", array('id'=>$attempt->timelinephase));
        $tempphase->attemptlogid = $attempt->id;
        array_push($timelinephases,$tempphase);

        $lastattemptstatus = $attempt->status;
        $nextphaseid = $attempt->nextphase;
    }

    if($lastattemptstatus == 1){
        // if last log is attempted then user needs to be presented with the next phase.
        if($nextphaseid !== "-1"){
            // If next phase is not "finish" (phaseid = -1)

            // Get phase data
            $tempphase = $DB->get_record_sql("SELECT * FROM {timelinephases} WHERE id=:id ORDER BY id ASC LIMIT 1", array('id'=>$nextphaseid));
            // Insert viewed log
            $attemptlogid = $attempttestlog->savelog($timelinetestid,$tempphase->id,$userid,"",0,0,0);
            $tempphase->attemptlogid = $attemptlogid;

            // Add in timeline list
            array_push($timelinephases,$tempphase);
        }
        else{
            // If next phase is "finish" (phaseid = -1)

            // Add finish block in timeline
            $finishphase = new stdClass();
            $finishphase->id = "-1";
            array_push($timelinephases,$finishphase);
        }
    }

}
else{
    // If log is empty then present the user with the first phase

    // Get first phase data
    $firstphase = $DB->get_record_sql("SELECT * FROM {timelinephases} WHERE timelinetestid=:timelinetestid ORDER BY id ASC LIMIT 1", array('timelinetestid'=>$timelinetestid));
    // Insert viewed log
    $attemptlogid = $attempttestlog->savelog($timelinetestid,$firstphase->id,$userid,"",0,0,0);
    $firstphase->attemptlogid = $attemptlogid;

    // Add in timeline
    array_push($timelinephases,$firstphase);
    // Initiate Marking
    $markingmanager->initiatemarking();
}

// Reverse the array so that the latest phase stays on top.
$timelinephases = array_reverse($timelinephases);

// Build timelines
$timelinebuilder = new timelinehtmlbuilder("",$id);
$timelinehtml = $timelinebuilder->buildtimeline($timelinephases,$userid);
$score = $markingmanager->getmark();

$templatecontext = (object)[
    'timelinehtml' => $timelinehtml,
    'addphaseurl' => new moodle_url("/mod/timelinetest/addphase.php?id=$id"),
    'score' => $score
];

echo $OUTPUT->render_from_template('mod_timelinetest/attempt', $templatecontext);
