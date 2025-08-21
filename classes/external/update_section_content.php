<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_modcontentservice\external;

use external_description;
use external_function_parameters;
use external_value;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * External function 'local_modcontentservice_update_section_content' implementation.
 *
 * @package     local_modcontentservice
 * @category    external
 * @copyright   2025 Clemens Koza <ckoza@tgm.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_section_content extends external_api {
    protected static string $module_name = "section";

    /**
     * Describes parameters of the {@see self::execute()} method.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'section' => new external_value(PARAM_INT, 'ID of the section to update'),
            'summary' => self::editor_structure('summary'),
        ]);
    }

    /**
     * TO-DO describe what the function actually does.
     *
     * @param int $cmid
     * @param string $body
     * @return mixed TO-DO document
     */
    public static function execute(int $section, array $summary) {
        global $CFG;

        // Re-validate parameters in rare case this method was called directly.
        [
            'section' => $section,
            'summary' => $summary,
        ] = self::validate_parameters(self::execute_parameters(), [
            'section' => $section,
            'summary' => $summary,
        ]);

        $op = new section_update_operation($section);
        self::validate_context($op->get_context());
        require_capability('moodle/course:update', $op->get_context());

        $op->data->summary = $summary['text'];
        $op->data->summaryformat = $summary['format'];

        $op->save_files($summary['itemid'], 'section', 0,
                ['maxfiles'  => EDITOR_UNLIMITED_FILES, 'maxbytes'  => $CFG->maxbytes]);
        $op->update_section();

        return "ok";
    }

    /**
     * Describes the return value of the {@see self::execute()} method.
     *
     * @return external_description
     */
    public static function execute_returns(): external_description {
        return new external_value(PARAM_TEXT, 'the result');
    }
}
