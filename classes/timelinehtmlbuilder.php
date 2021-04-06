<?php

/**
 * Timeline HTML Code Builder.
 *
 * @package   mod_timelinetest
 * @copyright 2021 Ahnaf Muttaki
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class timelinehtmlbuilder{
    private $finalhtml;
    private $cmid;

    public function __construct($html,$cmid)
    {
        $this->finalhtml = $html;
        $this->cmid = $cmid;
    }

    /**
     * Called to get timeline html code
     * @param $timelinephases array of timeline phases
     * @param $userid int ID of USER
     * @return String HTML time line code
     */
    public function buildtimeline($timelinephases,$userid){
        global $DB;
        foreach ($timelinephases as $key=>$phase){
            if($phase->id !== "-1"){
                $phaselog = $DB->get_record_sql("SELECT * FROM {timelineattemptlog} WHERE timelinephase=:timelinephase AND userid=:userid AND id=:attemptlogid", array('timelinephase'=>$phase->id,'userid'=>$userid,'attemptlogid'=>$phase->attemptlogid));
                $phaseoptions = $DB->get_records_sql("SELECT * FROM {timelineoptions} WHERE timelinephase=:timelinephase", array('timelinephase'=>$phase->id));
                $this->addphaseinhtml($key,$phase,$phaselog,$phaseoptions,$phase->attemptlogid);
            }
            else{
                $this->addfinish($key);
            }
        }
        return $this->finalhtml;
    }

    /**
     * Called to add option htmls
     * @param $options array of options for a phase
     * @param $phasetype String Type of the phase
     * @return String HTML code
     */
    public function returnoptionshtml($options,$phasetype){
        $optionsHTML = "";
        if($phasetype == "Informative"){
            foreach ($options as $option){
                $description = $option->description;
                $optionsHTML = $optionsHTML."<input type='hidden' name='phaseresponse' value='$description'>";
            }
        }
        else{
            foreach ($options as $option){
                $description = $option->description;
                $optionsHTML = $optionsHTML."
                            <input type='radio' id='$description' name='phaseresponse' value='$description'>
                            <label for='$description'>$description</label><br>";
            }
        }
        return $optionsHTML;
    }

    /**
     * Called to get finish block
     * @param $sl int Serial no of the timeline block
     * @return void
     */
    public function addfinish($sl){
        $cmid = $this->cmid;
        $modulepage = new moodle_url("/mod/timelinetest/view.php?id=$cmid");
        if($sl%2==0){
            $containerclass = "container left";
        }
        else{
            $containerclass = "container right";
        }

        $this->finalhtml = $this->finalhtml."
                            <div class='timeline'>
                                <div class='$containerclass'>
                                <div class='content'>
                                    <h2>Finish</h2>
                                    <a class= 'btn-info' href='$modulepage'>Return</a>
                                </div>
                            </div>";
    }

    /**
     * Called to add phase in html
     * @param $sl int Serial no of the timeline block
     * @param $phase StdClass Current Phase
     * @param $phaselog StdClass Phase Attempt Log
     * @param $phaseoptions array Options of the phase
     * @param $attemptlogid int Log id of current attempt
     * @return void
     */
    public function addphaseinhtml($sl,$phase,$phaselog,$phaseoptions,$attemptlogid){
        global $USER;
        $formurl = new moodle_url("/mod/timelinetest/processattempt.php");
        if($sl%2==0){
            $containerclass = "container left";
        }
        else{
            $containerclass = "container right";
        }
        $phasetitle = $phase->phasetitle;
        $phasedescription = $phase->description;

        // rewrite plugin file url
        $context = context_module::instance($this->cmid);
        $contextid = $context->id;
//        $formatoptions = new stdClass();
//        $formatoptions->noclean = true;
//        $formatoptions->para = false;

        $phasedescription = file_rewrite_pluginfile_urls($phasedescription, 'pluginfile.php', $contextid, 'mod_timelinetest', 'description', (int)$phase->id, null);

        // If current phase status is in view then a form is needed. If the phase was previously attempted then form is not needed
        $currentphasestatus = $phaselog->status;

        if($phase->type == "Informative"){
            $phaseresponse = "";
        }
        else{
            $phaseresponse = ":<strong>".$phaselog->phaseresponse."</strong>";
        }

        $timelinetestid = $phase->timelinetestid;
        $timelinephase = $phase->id;
        $optionsHtml = $this->returnoptionshtml($phaseoptions,$phase->type);

        $cmid = $this->cmid;
        $userid = $USER->id;
        if($currentphasestatus == 0){
            $this->finalhtml = $this->finalhtml."
                            <div class='timeline'>
                                <div class='$containerclass'>
                                <div class='content'>
                                    <h2>$phasetitle</h2>
                                    $phasedescription
                                    <form action='$formurl' onsubmit='return clicksubmit()' method='post'>
                                        <input type='hidden' name='attemptlogid' value='$attemptlogid'>
                                        <input type='hidden' name='cmid' value='$cmid'>
                                        <input type='hidden' name='userid' value='$userid'>
                                        <input type='hidden' name='timelinetestid' value='$timelinetestid'>
                                        <input type='hidden' name='timelinephase' value='$timelinephase'>
                                        $optionsHtml   
                                        <input type='submit' value='Next'>
                                    </form>
                                </div>
                            </div>";
        }
        else{
            $this->finalhtml = $this->finalhtml."
                            <div class='timeline'>
                                <div class='$containerclass'>
                                <div class='content'>
                                    <h2>$phasetitle</h2>
                                    $phasedescription
                                    <br/>
                                    $phaseresponse
                                    <br/>
                                </div>
                            </div>";

        }
    }
}