<?php
/** @package mod_timelinetest
 * @author Ahnaf
 * @license http://www.gnu.org
 */

global $DB,$CFG;
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot. '/mod/timelinetest/classes/phaseinputcompletion.php');

class phaselist extends moodleform
{
    public function definition()
    {
        global $DB,$CFG;
        $customdata = $this->_customdata;
        $cmid = $customdata["cmid"];
        $timelinetestid = $customdata["timelinetestid"];

        $timelinephases = $DB->get_records_sql("SELECT * FROM {timelinephases} WHERE timelinetestid=:id ORDER BY id DESC", array('id'=>$timelinetestid));
        $phasecount = count($timelinephases);
        $mform = $this->_form; // Don't forget the underscore!
        $mform->addElement('header', 'general', "Phase List($phasecount)");

        foreach ($timelinephases as $key=>$value){
            $url = new moodle_url("/mod/timelinetest/editphase.php?id=$cmid&phaseid=$value->id");

            $phasetitlelabel = get_string('phasetitlelabel', 'timelinetest');
            $mform->addElement('html', "<br/>");
            $mform->addElement('html', "<label >$phasetitlelabel</label><br/>");
            $mform->addElement('html', "<input type='text' name='phase-title-$value->id' value='$value->phasetitle' readonly>");

            $editbtnlabel = get_string('editbtnlabel', 'timelinetest');
            if($value->type == "Interactive"){
                $mform->addElement('html', "<br/>");
                $mform->addElement('html', "<br/>");
                $mform->addElement('html', "<a class='btn btn-warning' href='$url'>$editbtnlabel</a>");
            }
            else{
                $mform->addElement('html', "<br/>");
                $mform->addElement('html', "<br/>");
                $mform->addElement('html', "<a class='btn btn-info' href='$url'>$editbtnlabel</a>");
            }

            $mform->addElement('html', "<br/>");

            // Completion data
            $phaseinputcompletion = new phaseinputcompletion($value->id,$value->timelinetestid);
            $completionstatus = $phaseinputcompletion->getcompletionstatus();
            if(!$completionstatus["completionstatus"]){
                $statusmsgs = $completionstatus["completionmsg"];
                foreach ($statusmsgs as $row){
                    $mform->addElement('html', "<p style='color: red'>* $row</p>");
                }
            }

            $mform->addElement('html', "<hr/>");
        }
    }
}