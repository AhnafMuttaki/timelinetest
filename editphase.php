<?php
require_once("../../config.php");
require_once($CFG->dirroot. '/mod/timelinetest/classes/form/editphase.php');

// Get params
$id = optional_param('id', 0, PARAM_INT); // Course Module ID
$phaseid = optional_param('phaseid', 0, PARAM_INT); // Phase ID

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

// Need to update the error code for phase
if ($phaseid) {
    if (!$phase = $DB->get_record('timelinephases', array('id' => $phaseid))) {
        print_error('timelinephasenotfound');
    }
} else {
    print_error('timelinephasenotfound');
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

// Get phaselist
$phaselist = $DB->get_records_sql("SELECT * FROM {timelinephases} WHERE timelinetestid =:id", array('id'=>$timelinetest->id));
if(!$phaselist){
    print_error('invalidcoursemodule');
}

// Initiate Form
$customdata = array();
$customdata["cmid"] = $id;
$customdata["phaselist"] = $phaselist;
$customdata["phase"] = $phase;
$actionUrl = new moodle_url("/mod/timelinetest/editphase.php");
$mform = new editphase($actionUrl,$customdata,'post','',null,true,null);

if ($mform->is_cancelled()) {
    // Go back to the manage.php page
    redirect($CFG->wwwroot.'/mod/timelinetest/view.php', get_string('formcancel', 'timelinetest'));
} else if ($fromform = $mform->get_data()) {
    // Handle form post
    $id = $fromform->id;

    $phaseid = $fromform->phaseid;
    $phasetitle = $fromform->phasetitle;
    $description = $fromform->description["text"];
    $phasetype = $fromform->phasetype;
    $markthreshold = $fromform->markthreshold;

    // update
    $sql = "UPDATE {timelinephases} 
            SET phasetitle = :phasetitle, 
            description = :description, 
            type=:phasetype, 
            markthreshold=:markthreshold,
            timemodified=:now
            WHERE id=:id";


    $params = array();
    $params["id"] = $phaseid;
    $params["phasetitle"] = $phasetitle;
    $params["description"] = $description;
    $params["phasetype"] = $phasetype;
    $params["markthreshold"] = $markthreshold;
    $params["now"] = time();

    $DB->execute($sql, $params);

    if($phasetype == "Interactive"){
        // Interactive phase has 4 options
        $optionsl = array(1,2,3,4);

    }
    else if($phasetype == "Informative"){
        // Informative phase has 1 default option
        $optionsl = array(1);
    }

    foreach ($optionsl as $sl){
        $idProp = "optionid-$sl";
        $descriptionProp = "optiondescription-$sl";
        $maxmarkProp = "maxmark-$sl";
        $nextphaseProp = "nextphase-$sl";

        $optionid = $fromform->$idProp;
        $optiondescription = $fromform->$descriptionProp;
        $maxmark = $fromform->$maxmarkProp;
        $nextphase = $fromform->$nextphaseProp;

        $sql = "UPDATE {timelineoptions} 
            SET 
            description = :description, 
            maxmark=:maxmark, 
            nextphase=:nextphase,
            timemodified=:now
            WHERE id=:id";

        $params = array();
        $params["id"] = $optionid;
        $params["description"] = $optiondescription;
        $params["maxmark"] = $maxmark;
        $params["nextphase"] = $nextphase;
        $params["now"] = time();

        $DB->execute($sql, $params);
    }

    $url = new moodle_url("/mod/timelinetest/timelinetestedit.php?id=$id");
    redirect($url, get_string('successphasedatasave', 'timelinetest'));
}
$emptynextphase = get_string('validationmsg:emptynextphase', 'timelinetest');
// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/timelinetest/editphase.php', array('id' => $cm->id,'phaseid'=>$phaseid));
$PAGE->requires->js_call_amd('mod_timelinetest/editphase', 'init', array($emptynextphase));
$title = $course->shortname . ': '.$cm->name.":"."Edit";
$PAGE->set_context($context);
$PAGE->set_title($title);

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
