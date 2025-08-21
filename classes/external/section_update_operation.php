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

use stdClass;
use core\context\course as context_course;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * Metadata class for this plugin's external functions.
 *
 * @package     local_modcontentservice
 * @category    external
 * @copyright   2025 Clemens Koza <ckoza@tgm.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section_update_operation {
    private int $sectionid;

    private int $courseid;
    private context_course $context;
    public stdClass $data;

    public function __construct(int $sectionid) {
        global $DB;

        $this->sectionid = $sectionid;

        $course = $DB->get_record('course_sections', ['id' => $sectionid], 'course', MUST_EXIST);
        $this->courseid = $course->course;
        $this->context = context_course::instance($this->courseid);

        $this->data = new stdClass();
    }

    public function get_context(): context_course {
        return $this->context;
    }

    // public function set_time_modified() {
    //     $this->data->timemodified = time();
    // }

    public function save_files(int $draftitemid, string $filearea, int $itemid=0, ?array $options=null) {
        file_save_draft_area_files($draftitemid, $this->context->id, 'course', $filearea, $itemid, $options);
    }

    public function update_section() {
        course_update_section($this->courseid, (object) ['id' => $this->sectionid], $this->data);
    }
}
