<?php
require_once("../../config.php");
require_once($CFG->dirroot. '/mod/timelinetest/classes/form/phaselist.php');
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

//

$PAGE->set_url('/mod/timelinetest/timelinetestattempt.php', array('id' => $cm->id));
$title = $course->shortname . ': '.$cm->name.":"."Attempt";
$PAGE->set_context($context);
$PAGE->set_title($title);

// Get timeline test data
$timelinetest = $DB->get_record_sql("SELECT * FROM {timelinetest} WHERE id=:id", array('id'=>$cm->instance));
if(!$timelinetest){
    print_error('invalidcoursemodule');
}

$templatecontext = (object)[
    'timelinedata' => array((array)$timelinetest),
    'addphaseurl' => new moodle_url("/mod/timelinetest/addphase.php?id=$id"),
    'editpagetitle' => get_string('editpagetitle', 'timelinetest'),
    'generalinfo' => get_string('generalinfo', 'timelinetest'),
    'phases' => get_string('phases', 'timelinetest')
];

echo $OUTPUT->render_from_template('mod_timelinetest/attempt', $templatecontext);
