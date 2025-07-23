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

require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * External function 'local_modcontentservice_update_assign_content' implementation.
 *
 * @package     local_modcontentservice
 * @category    external
 * @copyright   2025 Clemens Koza <ckoza@tgm.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_assign_content extends external_api {
    protected static string $module_name = "assign";
    protected static ?string $display_name = "assignment";


    /**
     * Describes parameters of the {@see self::execute()} method.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'course module ID of the assignment to update'),
            'intro' => self::editor_structure('intro'),
            'activity' => self::editor_structure('body'),
            'attachments' => new external_value(PARAM_INT, 'the new attachments of the assignment', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * TO-DO describe what the function actually does.
     *
     * @param int $cmid
     * @param string $body
     * @return mixed TO-DO document
     */
    public static function execute(int $cmid, array $intro, array $activity, int $attachments) {
        // Re-validate parameters in rare case this method was called directly.
        [
            'cmid' => $cmid,
            'intro' => $intro,
            'activity' => $activity,
            'attachments' => $attachments,
        ] = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'intro' => $intro,
            'activity' => $activity,
            'attachments' => $attachments,
        ]);

        $op = self::update_operation($cmid);

        $op->set_time_modified();
        // $op->set_revision();
        $op->data->intro = $intro['text'];
        $op->data->introformat = $intro['format'];
        $op->data->activity = $activity['text'];
        $op->data->activityformat = $activity['format'];

        $op->save_files($intro['itemid'], 'intro', 0, ['subdirs' => true]);
        $op->save_files($activity['itemid'], ASSIGN_ACTIVITYATTACHMENT_FILEAREA, 0, ['subdirs' => true]);
        $op->update_record();

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
