<?php

/** @package local_massage
 *  @author Ahnaf
 *  @license http://www.gnu.org
 */

global $CFG;
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class phase extends moodleform {
    public function __construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata,$cmid)
    {
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);

    }

    //Add elements to form
    public function definition() {
        global $CFG,$PAGE;
        $customdata = $this->_customdata;
        $id = $customdata["cmid"];

        $mform = $this->_form; // Don't forget the underscore!
        $mform->addElement('header', 'general', "Add Phase");

        $mform->addElement('hidden','id',$id);
        $mform->setType('id', PARAM_RAW);

        $mform->addElement('text', 'phasetitle', 'Title'); // Add elements to your form
        $mform->setType('phasetitle', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('phasetitle', null, 'required', null, 'client');
        // $mform->addHelpButton('phasetitle', 'phasetitle', 'mod_timelinetest');
        // $mform->setDefault('phasetitle', 'Please enter a title');        //Default value

        $mform->addElement('editor', 'description', "Description",
            array('rows' => 15));
        $mform->setType('questioninfo', PARAM_RAW);
        $mform->addRule('description', null, 'required', null, 'client');

        $choices = array();
        $choices[''] = "Select";
        $choices['Informative'] = "Informative";
        $choices['Interactive'] = "Interactive";

        $mform->addElement('select', 'phasetype', 'Type',$choices,array('id'=>'phasetype')); // Add elements to your form
        $mform->setDefault('phasetype','');
        $mform->addRule('phasetype', null, 'required', null, 'client');


        $mform->addElement('html',"<div id='options-div' style='display: none'>");

        $mform->addElement('text', 'option1', 'Option 1'); // Add elements to your form
        $mform->setType('option1', PARAM_NOTAGS);                   //Set type of element
        // $mform->setDefault('option-1', 'Please enter a option text');

        $mform->addElement('text', 'option2', 'Option 2'); // Add elements to your form
        $mform->setType('option2', PARAM_NOTAGS);                   //Set type of element
        // $mform->setDefault('option-2', 'Please enter a option text');

        $mform->addElement('text', 'option3', 'Option 3'); // Add elements to your form
        $mform->setType('option3', PARAM_NOTAGS);                   //Set type of element
        // $mform->setDefault('option-3', 'Please enter a option text');

        $mform->addElement('text', 'option4', 'Option 4'); // Add elements to your form
        $mform->setType('option4', PARAM_NOTAGS);                   //Set type of element
        // $mform->setDefault('option-4', 'Please enter a option text');

        $mform->addElement('html','</div>');

        $this->add_action_buttons();
        $PAGE->requires->js_call_amd('mod_timelinetest/addphase', 'init', array());
    }
    //Custom validation should be added here
    function validation($data, $files=null) {
        $title = $data['phasetitle'];
        $descriptiondata = $data['description'];
        $description = $descriptiondata["text"];

        $phasetype = $data['phasetype'];
        $option1 = $data['option1'];
        $option2 = $data['option2'];
        $option3 = $data['option3'];
        $option4 = $data['option4'];

        $valid = true;
        $messages = array();
        if(trim($title) == ""){
            $valid = false;
            array_push($messages,"Title can not be null or empty string.");
        }
        if(trim($description) == ""){
            $valid = false;
            array_push($messages,"Description can not be null or empty string.");
        }
        if(trim($phasetype) == ""){
            $valid = false;
            array_push($messages,"Phase type can not be null or empty string.");
        }
        if($phasetype == "Interactive"){
            $options=$option1.$option2.$option3.$option4;
            if(trim($options) == ""){
                $valid = false;
                array_push($messages,"You have to provide options if the phase type is interactive.");
            }
        }

        if($valid){
            $response = array();
            return $response;
        }
        else{
            $response = $messages;
            return $response;
        }
    }
}