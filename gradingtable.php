<?php
global $DB,$USER;
require_once("../../config.php");
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

$viewtype = "";
if(has_capability('mod/timelinetest:addinstance', $context, $USER, true)){
    $viewtype = "teacher/admin";
}
else{
    $viewtype = "student";
}
$timelinetestid = $cm->instance;
$userid = $USER->id;

if($viewtype == "teacher/admin"){
    $gradessql = "SELECT UT.username,TT.timelinetestid,TT.userid,TT.obtainedmark FROM {timelinetotalmark} TT JOIN {user} UT ON TT.userid = UT.id WHERE TT.timelinetestid=:timelinetestid";
    $gradesdata = $DB->get_records_sql($gradessql, array("timelinetestid"=>$timelinetestid));
}else{
    $gradessql = "SELECT UT.username,TT.timelinetestid,TT.userid,TT.obtainedmark FROM {timelinetotalmark} TT JOIN {user} UT ON TT.userid = UT.id WHERE TT.timelinetestid=:timelinetestid AND TT.userid=:userid";
    $gradesdata = $DB->get_records_sql($gradessql, array("timelinetestid"=>$timelinetestid,"userid"=>$userid));
}

$table = new html_table();
$table->head = array(get_string('tableheader:userid', 'timelinetest'),get_string('tableheader:username', 'timelinetest'),get_string('tableheader:obtainedmark', 'timelinetest'));
$table->align = array ("center","center","center");

if(count($gradesdata)>0){
    foreach ($gradesdata as $row){
        $table->data[] = array($row->userid,$row->username,$row->obtainedmark);
    }
}

echo $OUTPUT->header();
echo html_writer::table($table);
echo $OUTPUT->footer();
