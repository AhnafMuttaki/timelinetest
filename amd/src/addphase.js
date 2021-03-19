/**
 * Initialise the an add question modal on the quiz page.
 *
 * @module    mod_timelinetest/addphase
 * @package   mod_timelinetest
 * @copyright 2021 Ahnaf Muttaki
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
    [
        'jquery',
        'core/notification',
        'core/modal_factory',
    ],
    function(
        $,
        Notification,
        ModalFactory
    ) {

        return {
            init: function(emptyoptionmsg) {
                console.log("init fired again with change with jsquery id fire v7");

                var phaseType = document.getElementById("phasetype").value;
                console.log("phase type:", phaseType);

                if (phaseType == "Informative") {
                    $('#options-div').css('display', 'none');
                } else {
                    $('#options-div').css('display', 'block');
                }

                $('#phasetype').change(function() {
                    console.log("phase type changed");
                    var phaseType = document.getElementById("phasetype").value;
                    console.log("phase type:", phaseType);

                    if (phaseType == "Informative") {
                        $('#options-div').css('display', 'none');
                    } else {
                        $('#options-div').css('display', 'block');
                    }
                });

                $("#id_submitbutton").click(function() {
                    var phaseType = document.getElementById("phasetype").value;
                    var invalidflag = false;
                    if (phaseType == "Interactive") {
                        var msg = "* " + emptyoptionmsg;
                        for (var sl = 1; sl < 5; sl++) {
                            var optionvalue = document.getElementById("id_option" + sl).value;
                            if (!optionvalue) {
                                $("#id_error_option" + sl).addClass("form-group row fitem has-danger");
                                document.getElementById("id_error_option" + sl).innerHTML = msg;
                                invalidflag = true;
                            }
                        }

                        if (invalidflag) {
                            return false;
                        }
                    }
                });

                return true;
            }
        };
    });
