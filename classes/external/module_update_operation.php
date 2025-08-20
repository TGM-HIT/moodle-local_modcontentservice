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
use core\context\module as context_module;

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
class module_update_operation {
    private string $module_name;
    private int $cmid;

    private stdClass $cm;
    private context_module $context;
    public stdClass $data;

    public function __construct(string $module_name, int $cmid) {
        $this->module_name = $module_name;
        $this->cmid = $cmid;

        $this->cm = get_coursemodule_from_id($this->module_name, $cmid, 0, false, MUST_EXIST);
        $this->context = context_module::instance($cmid);

        $this->data = new stdClass();
        $this->data->id = $this->cm->instance;
    }

    public function get_context(): context_module {
        return $this->context;
    }

    public function set_time_modified() {
        $this->data->timemodified = time();
    }

    public function set_revision() {
        global $DB;

        $record = $DB->get_record($this->module_name, ['id' => $this->cm->instance], 'revision', MUST_EXIST);
        $this->data->revision = $record->revision + 1;
    }

    public function save_files(int $draftitemid, string $filearea, int $itemid=0, ?array $options=null) {
        file_save_draft_area_files($draftitemid, $this->context->id, "mod_$this->module_name", $filearea, $itemid, $options);
    }

    public function update_record() {
        global $DB;

        $DB->update_record($this->module_name, $this->data);
    }

    public function rebuild_course_cache() {
        \course_modinfo::purge_course_module_cache($this->cm->course, $this->cmid);
        rebuild_course_cache($this->cm->course, true, true);
    }
}
