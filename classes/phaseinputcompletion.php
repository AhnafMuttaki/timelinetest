<?php

/**
 * Phase Input Completion.
 *
 * @package   mod_timelinetest
 * @copyright 2021 Ahnaf Muttaki
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phaseinputcompletion{
    private $id; //phase id
    private $timelinetestid; // id of the timelinetest

    public function __construct($id,$timelinetestid)
    {
        $this->id = $id;
        $this->timelinetestid = $timelinetestid;
    }

    /**
     * Called to get completion status of a phase input
     *
     * @return void
     */
    public function getcompletionstatus(){
        global $DB;
        $completionmsg = array();
        $options = $DB->get_records_sql("SELECT OT.id,OT.timelinetestid,OT.timelinephase,OT.description,OT.maxmark,OT.nextphase,TP.type FROM {timelineoptions} OT JOIN {timelinephases} TP ON OT.timelinephase = TP.id WHERE OT.timelinetestid =:timelinetestid AND OT.timelinephase = :timelinephase", array('timelinetestid'=>$this->timelinetestid,'timelinephase'=>$this->id));

        $foundcorrect = false;
        $nextphasenull = false;
        foreach ($options as $row){
            if($row->type == "Informative"){
                $foundcorrect = true;
            }

            if($row->maxmark == "100"){
                $foundcorrect = true;
            }

            if($row->nextphase == "0"){
                $nextphasenull = true;
            }
        }

        if(!$foundcorrect){
            array_push($completionmsg,get_string('validationmsg:correctnull', 'timelinetest'));
        }

        if($nextphasenull){
            array_push($completionmsg,get_string('validationmsg:nextphasenull', 'timelinetest'));
        }

        $response = array();
        if(count($completionmsg) == 0){
            $response["completionstatus"] = true;
        }
        else{
            $response["completionstatus"] = false;
        }
        $response["completionmsg"] = $completionmsg;
        return $response;
    }
}