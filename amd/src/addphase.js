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
            init: function() {

                console.log("init fired again with change with jsquery id fire");

                var phaseType = document.getElementById("phasetype").value;
                console.log("phase type:", phaseType);

                if (phaseType == "Informative") {
                    $('#options-div').css('display', 'none');
                } else {
                    $('#options-div').css('display', 'block');
                }

                // When a select is changed, look for the students based on the department id
                // and display on the dropdown students select
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

                return true;
            }
        };
    });
