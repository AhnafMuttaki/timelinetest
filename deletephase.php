<?php
global $DB,$USER;
require_once("../../config.php");
$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or ...
$phaseid = optional_param('phaseid', 0, PARAM_INT);
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

if(has_capability('mod/timelinetest:addinstance', $context, $USER, true)){
    // Get phase data
    $sql = "SELECT * FROM {timelinephases} WHERE id=:phaseid";
    $data = $DB->get_record_sql($sql, array("phaseid"=>$phaseid));

    if($data){
        $timelinetestid = $data->timelinetestid;
        // Update nextphase columns
        $updatesql = "UPDATE {timelineoptions} SET nextphase = '0' WHERE timelinetestid=:timelinetestid AND nextphase=:phaseid";
        $DB->execute($updatesql, array('timelinetestid'=>$timelinetestid,'phaseid'=>$phaseid));
        // Delete phase options
        $DB->delete_records('timelineoptions', array('timelinephase'=>$phaseid));

        // Delete phase
        $DB->delete_records('timelinephases', array('id'=>$phaseid));

        $redirecturl = new moodle_url("/mod/timelinetest/timelinetestedit.php?id=$id");
        redirect($redirecturl , "Successfully deleted the phase");
    }
}