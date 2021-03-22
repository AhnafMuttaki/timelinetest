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
            init: function(emptynextphase, emptycorrectanswer, msgbanner) {
                console.log("init fired in edit phase v6");

                $("#id_submitbutton").click(function() {
                    var phaseType = document.getElementById("phasetype").value;
                    var invalidnextphase = false;
                    var correctansnotfound = true;
                    if (phaseType == "Interactive") {
                        var msg = "* " + emptynextphase;
                        for (var sl = 1; sl < 5; sl++) {
                            var nextphasevalue = document.getElementById("nextphase-" + sl).value;
                            console.log(nextphasevalue);
                            if (nextphasevalue == 0) {
                                console.log("loop invoked");
                                invalidnextphase = true;
                            }

                            var mark = document.getElementById("maxmark-" + sl).value;
                            if (mark == 100) {
                                correctansnotfound = false;
                            }
                        }

                        if (invalidnextphase) {
                            Notification.alert(msgbanner, emptynextphase);
                            return false;
                        }

                        if (correctansnotfound) {
                            Notification.alert(msgbanner, emptycorrectanswer);
                            return false;
                        }
                    } else {

                        var nextphasevalue = document.getElementById("nextphase-1").value;
                        console.log(nextphasevalue);
                        if (nextphasevalue == 0) {
                            console.log("loop invoked");
                            invalidnextphase = true;
                        }

                        if (invalidnextphase) {
                            Notification.alert(msgbanner, emptynextphase);
                            return false;
                        }
                    }
                });

                return true;
            }
        };
    });
