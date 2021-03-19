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
            init: function(emptynextphase) {
                console.log("init fired in edit phase v6");

                $("#id_submitbutton").click(function() {
                    var phaseType = document.getElementById("phasetype").value;
                    var invalidflag = false;
                    if (phaseType == "Interactive") {
                        var msg = "* " + emptynextphase;
                        for (var sl = 1; sl < 5; sl++) {
                            var nextphasevalue = document.getElementById("nextphase-" + sl).value;
                            console.log(nextphasevalue);
                            if (nextphasevalue == 0) {
                                console.log("loop invoked");
                                invalidflag = true;
                            }
                        }

                        if (invalidflag) {
                            Notification.alert("Please choose next phase");
                            return false;
                        }
                    }
                });

                return true;
            }
        };
    });
