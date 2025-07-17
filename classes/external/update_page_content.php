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

use external_api;
use external_description;
use external_function_parameters;
use external_value;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/modlib.php');

/**
 * External function 'local_modcontentservice_update_page_content' implementation.
 *
 * @package     local_modcontentservice
 * @category    external
 * @copyright   2025 Clemens Koza <ckoza@tgm.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_page_content extends external_api {

    /**
     * Describes parameters of the {@see self::execute()} method.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {

        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'course module ID of the page to update'),
            'body' => new external_value(PARAM_RAW, 'the new body content of the page'),
        ]);
    }

    /**
     * TO-DO describe what the function actually does.
     *
     * @param int $cmid
     * @param string $body
     * @return mixed TO-DO document
     */
    public static function execute(int $cmid, string $body) {

        // Re-validate parameters in rare case this method was called directly.
        [
            'cmid' => $cmid,
            'body' => $body,
        ] = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'body' => $body,
        ]);

        $moduleinfo = get_coursemodule_from_id('page', $cmid, 0, false, MUST_EXIST);

        $moduleinfo->coursemodule = $cmid;
        $moduleinfo->introeditor = [
        "text" => "",
        "format" => FORMAT_HTML,
        "itemid" => IGNORE_FILE_MERGE,
        ];
        $moduleinfo->page = [
        "text" => $body,
        "format" => FORMAT_HTML,
        "itemid" => null,
        ];

        update_module($moduleinfo);

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
