<?php
require_once("../../config.php");
require_once($CFG->dirroot. '/mod/timelinetest/classes/form/phase.php');
//require_login();
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

$title = $course->shortname . ': '.$cm->name.":"."Add Phase";
$PAGE->set_url(new moodle_url('/mod/timelinetest/addphase.php'));
$PAGE->set_context($context);
$PAGE->set_title($title);

$formcustomData = array();
$formcustomData["cmid"] = $id;
$actionUrl = new moodle_url("/mod/timelinetest/addphase.php");
$mform = new phase($actionUrl,$formcustomData,'post','',null,true,null);

if ($mform->is_cancelled()) {
    // Go back to the manage.php page
    redirect($CFG->wwwroot.'/mod/timelinetest/view.php', 'You cancelled the message form');
} else if ($fromform = $mform->get_data()) {
        $title = $fromform->phasetitle;
        $descriptiondata = $fromform->description;
        $description = $descriptiondata["text"];

        $phasetype = $fromform->phasetype;
        $option1 = $fromform->option1;
        $option2 = $fromform->option2;
        $option3 = $fromform->option3;
        $option4 = $fromform->option4;

        // Get timeline test data
        $timelinetest = $DB->get_record_sql("SELECT * FROM {timelinetest} WHERE id=:id", array('id'=>$cm->instance));
        if(!$timelinetest){
            print_error('invalidcoursemodule');
        }

        // Store Phase data
        $timelinephase = new stdClass();
        $timelinephase->timelinetestid = $timelinetest->id;
        $timelinephase->phasetitle = $title;
        $timelinephase->description = $description;
        $timelinephase->type = $phasetype;
        $timelinephase->markthreshold = 0;
        $timelinephase->timecreated = time();

        $timelinephaseid = $DB->insert_record('timelinephases', $timelinephase);

        if($phasetype == "Interactive"){
            // Store Options for interactiove phase
            $options = array($option1,$option2,$option3,$option4);
            foreach ($options as $option){
                if(trim($option) != ""){
                    $phaseoption = new stdClass();
                    $phaseoption->timelinetestid = $timelinetest->id;
                    $phaseoption->timelinephase = $timelinephaseid;
                    $phaseoption->description = $option;
                    $phaseoption->timecreated = time();
                    $optionid = $DB->insert_record('timelineoptions', $phaseoption);
                }
            }
        }

        $url = new moodle_url("/mod/timelinetest/timelinetestedit.php?id=$id");
        redirect($url, 'Successfully saved phase data.');
}
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();