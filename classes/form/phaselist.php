<?php
/** @package mod_timelinetest
 * @author Ahnaf
 * @license http://www.gnu.org
 */

global $DB,$CFG;
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

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

            $mform->addElement('html', "<br/>");
            $mform->addElement('html', "<label >Phase Title:</label><br/>");
            $mform->addElement('html', "<input type='text' name='phase-title-$value->id' value='$value->phasetitle' readonly>");

            if($value->type == "Interactive"){
                $mform->addElement('html', "<br/>");
                $mform->addElement('html', "<br/>");
                $mform->addElement('html', "<a class='btn btn-warning' href='$url'>Edit</a>");
            }
            else{
                $mform->addElement('html', "<br/>");
                $mform->addElement('html', "<br/>");
                $mform->addElement('html', "<a class='btn btn-info' href='$url'>Edit</a>");
            }

            $mform->addElement('html', "<br/>");
            $mform->addElement('html', "<hr/>");
        }
    }
}