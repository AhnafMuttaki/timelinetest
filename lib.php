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