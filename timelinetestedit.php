<?php
require_once("../../config.php");
require_once($CFG->dirroot. '/mod/timelinetest/classes/form/phaselist.php');
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
require_capability('mod/timelinetest:addinstance', $context);

// Get timeline test data
$timelinetest = $DB->get_record_sql("SELECT * FROM {timelinetest} WHERE id=:id", array('id'=>$cm->instance));
if(!$timelinetest){
    print_error('invalidcoursemodule');
}
// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/timelinetest/timelinetestedit.php', array('id' => $cm->id));
$templatecontext = (object)[
    'timelinedata' => array((array)$timelinetest),
    'addphaseurl' => new moodle_url("/mod/timelinetest/addphase.php?id=$id"),
    'editpagetitle' => get_string('editpagetitle', 'timelinetest'),
    'generalinfo' => get_string('generalinfo', 'timelinetest'),
    'phases' => get_string('phases', 'timelinetest')
];

$title = $course->shortname . ': '.$cm->name.":"."Edit";
$PAGE->set_context($context);
$PAGE->set_title($title);

// Initialize form
$actionUrl = new moodle_url("/mod/timelinetest/timelinetestedit.php");
$formcustomData = array();
$formcustomData["cmid"] = $id;
$formcustomData["timelinetestid"] = $timelinetest->id;
$mform = new phaselist($actionUrl,$formcustomData,'post','',null,true,null);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_timelinetest/edit', $templatecontext);
$mform->display();
echo $OUTPUT->footer();
