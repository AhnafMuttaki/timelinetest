<?php
require_once("../../config.php");
require_login();
$PAGE->set_context(context_system::instance());
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
require_capability('mod/timelinetest:view', $context);

// Get timeline test data
$timelinetest = $DB->get_record_sql("SELECT * FROM {timelinetest} WHERE id=:id", array('id'=>$cm->instance));
if(!$timelinetest){
    print_error('invalidcoursemodule');
}

// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/timelinetest/view.php', array('id' => $cm->id));
$templatecontext = (object)[
    'title' => $timelinetest->name,
    'editurl' => new moodle_url("/mod/timelinetest/timelinetestedit.php?id=$id"),
    'attempturl' => new moodle_url("/mod/timelinetest/timelinetestattempt.php?id=$id")
];


$title = $course->shortname . ': '.$cm->name;
$PAGE->set_url(new moodle_url('/mod/timelinetest/view.php'));
$PAGE->set_context($context);
$PAGE->set_title($title);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_timelinetest/manage', $templatecontext);
echo $OUTPUT->footer();

