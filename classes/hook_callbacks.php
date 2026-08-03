<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_socialcert
 * @copyright   2025 Manuel Bojaca <manuel@buendata.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_socialcert;

use core\hook\output\before_footer_html_generation;

/**
 * Defines plugin hook callbacks for local_socialcert.
 *
 * Only the before_footer_html_generation hook is registered: it injects the
 * rendered panel template and loads the JavaScript module on the certificate
 * view page. The plugin stylesheet (styles.css) needs no hook because Moodle
 * auto-includes every plugin's styles.css via the theme stylesheet.
 *
 * @package    local_socialcert
 * @category   output
 */
class hook_callbacks {
    /**
     * Returns the course-module id of the current certificate view, or 0 when
     * the panel should not be rendered.
     *
     * The checks are intentionally ordered so that $PAGE->cm is only read after
     * the page-type and session guards: hooks fire on every page, and on a
     * non-course-module page $PAGE->cm is null, so reading it unconditionally
     * raises an "Attempt to read property id on null" PHP warning.
     *
     * @return int The current cmid, or 0 if the panel should not be shown.
     */
    protected static function get_active_cmid(): int {
        global $PAGE;

        if ($PAGE->pagetype !== 'mod-customcert-view') {
            return 0;
        }
        if (!isloggedin()) {
            return 0;
        }
        if (isguestuser()) {
            return 0;
        }
        if (empty($PAGE->cm) || empty($PAGE->cm->id)) {
            return 0;
        }
        if (trim((string) get_config('local_socialcert', 'organizationid')) === '') {
            return 0;
        }

        return (int) $PAGE->cm->id;
    }

    /**
     * Injects custom HTML into the footer area of the certificate view page.
     *
     * Triggered by the before_footer_html_generation hook. Renders the
     * local_socialcert main panel using a Mustache template and inserts
     * it into the page output. Also loads the required JavaScript module.
     *
     * @param before_footer_html_generation $hook The hook object for the event.
     * @return void
     */
    public static function before_footer_html_generation(
        before_footer_html_generation $hook
    ): void {
        global $PAGE, $OUTPUT;

        $cmid = self::get_active_cmid();
        if (!$cmid) {
            return;
        }

        $panel = new \local_socialcert\output\main_panel(cmid: $cmid);
        $context = $panel->export_for_template(output: $OUTPUT);
        $html  = $OUTPUT->render_from_template(
            'local_socialcert/main',
            $context
        );

        $hook->add_html($html);

        $PAGE->requires->js_call_amd('local_socialcert/actions', 'init', [
            'cmid' => $cmid,
        ]);
    }
}
