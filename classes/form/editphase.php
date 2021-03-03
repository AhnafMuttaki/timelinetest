<?php


/** @package mod_timelinetest
 * @author Ahnaf
 * @license http://www.gnu.org
 */

global $CFG;
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class editphase extends moodleform
{
    public function __construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata)
    {
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);

    }

    //Add elements to form
    public function definition()
    {
        global $DB;
        $customdata = $this->_customdata;
        $id = $customdata["cmid"];
        $phase = $customdata["phase"];
        $phaselist = $customdata["phaselist"];

        $mform = $this->_form; // Don't forget the underscore!
        $mform->addElement('header', 'general', "Edit Phase");

        // Course Module ID
        $mform->addElement('hidden','id',$id);
        $mform->setType('id', PARAM_RAW);

        // Phase ID
        $mform->addElement('hidden','phaseid',$phase->id);
        $mform->setType('phaseid', PARAM_RAW);

        // Phase Title
        $mform->addElement('text', 'phasetitle', 'Title'); // Add elements to your form
        $mform->setType('phasetitle', PARAM_NOTAGS);                   //Set type of element
        $mform->setDefault('phasetitle',$phase->phasetitle);
        $mform->addRule('phasetitle', null, 'required', null, 'client');

        // Phase Description
        $mform->addElement('editor', 'description', "Description",
            array('rows' => 15));
        $mform->setType('description', PARAM_RAW);
        $mform->addRule('description', "", 'required', null, 'client');

        // Phase Type
        $choices = array();
        $choices[''] = "Select";
        $choices['Informative'] = "Informative";
        $choices['Interactive'] = "Interactive";

        $mform->addElement('select', 'phasetype', 'Type',$choices,array('id'=>'phasetype')); // Add elements to your form
        $mform->addRule('phasetype', null, 'required', null, 'client');
        $mform->setDefault('phasetype',$phase->type);

        // Phase Mark Threshold
        $mform->addElement('text', 'markthreshold', 'Mark Threshold'); // Add elements to your form
        $mform->setType('markthreshold', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('markthreshold', null, 'required', null, 'client');
        $mform->setDefault('markthreshold',$phase->markthreshold);

        if($phase->type == "Interactive"){
            if($options = $DB->get_records('timelineoptions', array('timelinephase' => $phase->id))){
                $mform->addElement('header', 'general', "Options");
                $sl = 1;

                foreach ($options as $key=>$option){
                    if($sl != 1){
                        $mform->addElement('html', '<hr/>');
                    }

                    $mform->addElement('hidden',"optionid-$sl",$option->id);
                    $mform->setType("optionid-$sl", PARAM_RAW);


                    $mform->addElement('text', "optiondescription-$sl", 'Option Text'); // Add elements to your form
                    $mform->setType("optiondescription-$sl", PARAM_NOTAGS);                   //Set type of element
                    $mform->addRule("optiondescription-$sl", null, 'required', null, 'client');
                    $mform->setDefault("optiondescription-$sl",$option->description);

                    $maxmarkchoice = array();
                    $maxmarkchoice[''] = "Select";
                    $maxmarkchoice['0'] = "0%";
                    $maxmarkchoice['50'] = "50%";
                    $maxmarkchoice['100'] = "100%";

                    $mform->addElement('select', "maxmark-$sl", 'Max mark',$maxmarkchoice,array('id'=>'phasetype')); // Add elements to your form
                    $mform->addRule("maxmark-$sl", null, 'required', null, 'client');
                    $mform->setDefault("maxmark-$sl",$option->maxmark);

                    // Next phase
                    $phaselistchoice = array();
                    $phaselistchoice["0"] = "Select";
                    foreach ($phaselist as $phaserow){
                        $phaselistchoice[$phaserow->id] = $phaserow->phasetitle;
                    }
                    $phaselistchoice["-1"] = "Finish";

                    $mform->addElement('select', "nextphase-$sl", 'Next Phase',$phaselistchoice,array('id'=>'phasetype')); // Add elements to your form
                    $mform->setDefault("nextphase-$sl",$option->nextphase);

                    $sl += 1;
                }
            }
        }
        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files=null) {
        return array();
    }
}
