/**
 * Initialise the an add question modal on the quiz page.
 *
 * @module    mod_timelinetest/attempttest.js
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

                console.log("init fired in attempt page");

                return true;
            }
        };
    });
