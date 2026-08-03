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

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    // The before_standard_html_head_generation hook was removed because that
    // class does not exist in Moodle 4.5 (the real core hook is
    // before_standard_head_html_generation). The plugin stylesheet is loaded
    // automatically by Moodle (every plugin's styles.css is included via the
    // theme stylesheet), so the head hook was redundant anyway.
    [
        'hook' => \core\hook\output\before_footer_html_generation::class,
        'callback' => [\local_socialcert\hook_callbacks::class, 'before_footer_html_generation'],
    ],
];
