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
use external_single_structure;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/course/modlib.php');
// require_once($CFG->dirroot . '/mod/folder/locallib.php');

/**
 * External function 'local_modcontentservice_update_folder_content' implementation.
 *
 * @package     local_modcontentservice
 * @category    external
 * @copyright   2025 Clemens Koza <ckoza@tgm.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_folder_content extends external_api {

    /**
     * Describes parameters of the {@see self::execute()} method.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {

        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'course module ID of the folder to update'),
            'intro' => new external_single_structure([
                'text' => new external_value(PARAM_RAW, 'the new intro content of the folder'),
                'format' => new external_value(PARAM_INT, 'the format of the intro content', VALUE_DEFAULT, FORMAT_HTML),
                'itemid' => new external_value(PARAM_INT, 'the item ID for file storage', VALUE_DEFAULT, IGNORE_FILE_MERGE),
            ], 'the new intro content of the folder'),
            'files' => new external_value(PARAM_INT, 'the item ID for file storage'),
        ]);
    }

    /**
     * TO-DO describe what the function actually does.
     *
     * @param int $cmid
     * @param string $body
     * @return mixed TO-DO document
     */
    public static function execute(int $cmid, array $intro, int $files) {
        // Re-validate parameters in rare case this method was called directly.
        [
            'cmid' => $cmid,
            'intro' => $intro,
            'files' => $files,
        ] = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'intro' => $intro,
            'files' => $files,
        ]);

        $moduleinfo = get_coursemodule_from_id('folder', $cmid, 0, false, MUST_EXIST);

        $moduleinfo->coursemodule = $cmid;
        $moduleinfo->introeditor = $intro;
        $moduleinfo->files = $files;

        // TODO the folder module checks the CSRF token (called sesskey, see
        // https://moodledev.io/general/development/policies/security/crosssite-request-forgery),
        // which lets the update fail unless we tell Moodle to ignore it.
        // since this is a webservice authenticated by token, we don't have a session.
        // this setting is not persisted, but it's still hacky
        global $USER;
        $USER->ignoresesskey = true;

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
