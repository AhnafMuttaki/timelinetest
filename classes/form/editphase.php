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
        $mform->addElement('header', 'general', get_string('phaseedit:editheading', 'timelinetest'));

        // Course Module ID
        $mform->addElement('hidden','id',$id);
        $mform->setType('id', PARAM_RAW);

        // Phase ID
        $mform->addElement('hidden','phaseid',$phase->id);
        $mform->setType('phaseid', PARAM_RAW);

        // Phase Title
        $mform->addElement('text', 'phasetitle', get_string('addphasetitlelabel', 'timelinetest')); // Add elements to your form
        $mform->setType('phasetitle', PARAM_NOTAGS);                   //Set type of element
        $mform->setDefault('phasetitle',$phase->phasetitle);
        $mform->addRule('phasetitle', null, 'required', null, 'client');

        // Phase Description
        // Draft image procure
        $context = context_module::instance($id);
        $draftid = file_get_submitted_draft_itemid('description');


        $fileoptions = array("subdirs"=>true,"maxfiles"=>-1,"maxbytes"=>0);
        $descriptionText = file_prepare_draft_area($draftid, $context->id,
            'mod_timelinetest', 'description', $phase->id, $fileoptions,$phase->description);

        $editoroption = array("subdirs"=>1,"maxfiles" => -1);
        $mform->addElement('editor', 'description', get_string('addphasedescriptionlabel', 'timelinetest'),
            array('rows' => 15),$editoroption);
        $mform->setType('description', PARAM_RAW);
        $mform->addRule('description', "", 'required', null, 'client');
        $mform->setDefault('description',array('text'=>$descriptionText,'format'=>'1'));

        // Phase Type
//        $choices = array();
//        $choices[''] = get_string('phasetypeoptiondefault', 'timelinetest');
//        $choices['Informative'] = get_string('phasetypeoption1', 'timelinetest');
//        $choices['Interactive'] = get_string('phasetypeoption2', 'timelinetest');
//
//        $mform->addElement('select', 'phasetype', get_string('phasetypelabel', 'timelinetest'),$choices,array('id'=>'phasetype')); // Add elements to your form
//        $mform->addRule('phasetype', null, 'required', null, 'client');
//        $mform->setDefault('phasetype',$phase->type);

        $mform->addElement('text', 'phasetype', get_string('phasetypelabel', 'timelinetest'), 'maxlength="10" size="10" readonly="true"');
        $mform->setDefault('phasetype',$phase->type);

        // Phase Mark Threshold
        $mform->addElement('text', 'markthreshold', get_string('phasemarkthresholdlabel', 'timelinetest')); // Add elements to your form
        $mform->setType('markthreshold', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('markthreshold', null, 'required', null, 'client');
        $mform->setDefault('markthreshold',$phase->markthreshold);

        if($phase->type == 'Interactive'){
            if($options = $DB->get_records('timelineoptions', array('timelinephase' => $phase->id))){
                $mform->addElement('header', 'options_header', get_string('phaseedit:optionheading', 'timelinetest'));
                $sl = 1;

                foreach ($options as $key=>$option){
                    if($sl != 1){
                        $mform->addElement('html', '<hr/>');
                    }

                    $mform->addElement('hidden',"optionid-$sl",$option->id);
                    $mform->setType("optionid-$sl", PARAM_RAW);


                    $mform->addElement('text', "optiondescription-$sl", get_string('phaseedit:optiontext', 'timelinetest')); // Add elements to your form
                    $mform->setType("optiondescription-$sl", PARAM_NOTAGS);                   //Set type of element
                    $mform->addRule("optiondescription-$sl", null, 'required', null, 'client');
                    $mform->setDefault("optiondescription-$sl",$option->description);

                    $maxmarkchoice = array();
                    $maxmarkchoice[''] = get_string('phaseedit:maxmarkchoicedefault', 'timelinetest');
                    $maxmarkchoice['0'] = get_string('phaseedit:maxmarkchoice1', 'timelinetest');
                    $maxmarkchoice['50'] = get_string('phaseedit:maxmarkchoice2', 'timelinetest');
                    $maxmarkchoice['100'] = get_string('phaseedit:maxmarkchoice3', 'timelinetest');

                    $mform->addElement('select', "maxmark-$sl", get_string('phaseedit:maxmarklabel', 'timelinetest'),$maxmarkchoice,array('id'=>"maxmark-$sl")); // Add elements to your form
                    $mform->addRule("maxmark-$sl", null, 'required', null, 'client');
                    $mform->setDefault("maxmark-$sl",$option->maxmark);

                    // Next phase
                    $phaselistchoice = array();
                    $phaselistchoice["0"] = get_string('phaseedit:nextphasedefault', 'timelinetest');
                    foreach ($phaselist as $phaserow){
                        $phaselistchoice[$phaserow->id] = $phaserow->phasetitle;
                    }
                    $phaselistchoice["-1"] = get_string('phaseedit:nextphasefinish', 'timelinetest');

                    $mform->addElement('select', "nextphase-$sl", get_string('phaseedit:nextphaselabel', 'timelinetest'),$phaselistchoice,array('id'=>"nextphase-$sl")); // Add elements to your form
                    $mform->setDefault("nextphase-$sl",$option->nextphase);

                    $sl += 1;
                }
            }
        }
        else{
            if($options = $DB->get_records('timelineoptions', array('timelinephase' => $phase->id))){
                $sl = 1;
                foreach ($options as $key=>$option){
                    $mform->addElement('hidden',"optionid-$sl",$option->id);
                    $mform->setType("optionid-$sl", PARAM_RAW);

                    $mform->addElement('hidden',"optiondescription-$sl",$option->description);
                    $mform->setType("optiondescription-$sl", PARAM_RAW);

                    $mform->addElement('hidden',"maxmark-$sl","100");
                    $mform->setType("maxmark-$sl", PARAM_RAW);

                    // Next phase
                    $phaselistchoice = array();
                    $phaselistchoice["0"] = get_string('phaseedit:nextphasedefault', 'timelinetest');
                    foreach ($phaselist as $phaserow){
                        $phaselistchoice[$phaserow->id] = $phaserow->phasetitle;
                    }
                    $phaselistchoice["-1"] = get_string('phaseedit:nextphasefinish', 'timelinetest');

                    $mform->addElement('select', "nextphase-$sl", get_string('phaseedit:nextphaselabel', 'timelinetest'),$phaselistchoice,array('id'=>"nextphase-$sl")); // Add elements to your form
                    $mform->setDefault("nextphase-$sl",$option->nextphase);

                    $sl += 1;
                }
            }
            $mform->addElement('hidden','optionid',$phase->id);
        }

        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files=null) {
        return array();
    }
}
