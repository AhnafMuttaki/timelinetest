<?php

class timelinehtmlbuilder{
    private $finalhtml;
    private $cmid;

    public function __construct($html,$cmid)
    {
        $this->finalhtml = $html;
        $this->cmid = $cmid;
    }

    public function buildtimeline($timelinephases,$userid){
        global $DB;
        foreach ($timelinephases as $key=>$phase){
            if($phase->id !== "-1"){
                $phaselog = $DB->get_record_sql("SELECT * FROM {timelineattemptlog} WHERE timelinephase=:timelinephase AND userid=:userid", array('timelinephase'=>$phase->id,'userid'=>$userid));
                $phaseoptions = $DB->get_records_sql("SELECT * FROM {timelineoptions} WHERE timelinephase=:timelinephase", array('timelinephase'=>$phase->id));
                $this->addphaseinhtml($key,$phase,$phaselog,$phaseoptions);
            }
            else{
                $this->addfinish($key);
            }
        }
        return $this->finalhtml;
    }

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

    public function addphaseinhtml($sl,$phase,$phaselog,$phaseoptions){
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
                                    <form action='$formurl' method='post'>
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