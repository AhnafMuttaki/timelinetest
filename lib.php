<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds an timelinetest instance
 *
 * Only used by generators so we can create old assignments to test the upgrade.
 *
 * @param stdClass $timelinetest
 * @return int intance id
 */
function timelinetest_add_instance($timelinetest) {
    global $DB;
    $timelinetest->name = $timelinetest->title;
    $timelinetest->timecreated = time();
    $timelinetest->id = $DB->insert_record('timelinetest', $timelinetest);
    return $timelinetest->id;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id the id of the quiz to delete.
 * @return bool success or failure.
 */
function timelinetest_delete_instance($id) {
    global $DB;

    $DB->delete_records('timelineoptions', array('timelinetestid'=>$id));
    $DB->delete_records('timelinephases', array('timelinetestid'=> $id));
    $DB->delete_records('timelineattemptlog', array('timelinetestid'=>$id));
    $DB->delete_records('timelinetotalmark', array('timelinetestid'=>$id));
    $DB->delete_records('timelinetest', array('id'=> $id));

    return true;
}

/**
 * Checks file access for multiple choice questions.
 *
 * @package  qtype_bsmultichoice
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 */
function mod_timelinetest_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    if($filearea == "description"){
        $itemid = $args[0];
        $filename = array_pop($args);
        $filepath = '/';
        $fs = get_file_storage();
        $file = $fs->get_file($context->id, 'mod_timelinetest', $filearea, $itemid, $filepath, $filename);
        if (!$file) {
            return false;
        }
        send_stored_file($file, 0, 0, $forcedownload, $options);
    }
    else{
        return false;
    }
}