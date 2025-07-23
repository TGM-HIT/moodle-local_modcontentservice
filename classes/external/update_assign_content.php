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

use core\context\module as context_module;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/course/modlib.php');
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
    /**
     * Describes parameters of the {@see self::execute()} method.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'course module ID of the assignment to update'),
            'intro' => new external_single_structure([
                'text' => new external_value(PARAM_RAW, 'the new intro content of the assignment'),
                'format' => new external_value(PARAM_INT, 'the format of the intro content', VALUE_DEFAULT, FORMAT_HTML),
                'itemid' => new external_value(PARAM_INT, 'the item ID for file storage', VALUE_DEFAULT, IGNORE_FILE_MERGE),
            ], 'the new intro content of the assignment'),
            'activity' => new external_single_structure([
                'text' => new external_value(PARAM_RAW, 'the new body content of the assignment'),
                'format' => new external_value(PARAM_INT, 'the format of the body content', VALUE_DEFAULT, FORMAT_HTML),
                'itemid' => new external_value(PARAM_INT, 'the item ID for file storage', VALUE_DEFAULT, IGNORE_FILE_MERGE),
            ], 'the new body content of the assignment'),
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
        global $DB;

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

        $cm = get_coursemodule_from_id('assign', $cmid, 0, false, MUST_EXIST);
        $context = context_module::instance($cmid);
        require_capability('moodle/course:manageactivities', $context);

        $data = new \stdClass();
        $data->id = $cm->instance;
        $data->timemodified = time();
        // $data->revision = $DB->get_record('assign', ['id' => $cm->instance], 'revision', MUST_EXIST)->revision + 1;
        $data->intro = $intro['text'];
        $data->introformat = $intro['format'];
        $data->activity = $activity['text'];
        $data->activityformat = $activity['format'];

        file_save_draft_area_files($intro['itemid'], $context->id, 'mod_assign', 'intro', 0, ['subdirs' => true]);
        file_save_draft_area_files($activity['itemid'], $context->id, 'mod_assign', ASSIGN_ACTIVITYATTACHMENT_FILEAREA, 0, ['subdirs' => true]);
        $DB->update_record('assign', $data);

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
