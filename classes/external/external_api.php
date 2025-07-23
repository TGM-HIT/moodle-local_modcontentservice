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

use external_value;
use external_single_structure;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * Common superclass for this plugin's external functions.
 *
 * @package     local_modcontentservice
 * @category    external
 * @copyright   2025 Clemens Koza <ckoza@tgm.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_api extends \external_api {
    protected static string $module_name;
    protected static ?string $display_name = null;

    /**
     * Describes a file editor parameter.
     *
     * @return external_single_structure
     */
    public static function editor_structure(string $file): external_single_structure {
        $display_name = static::$display_name !== null ? static::$display_name : static::$module_name;

        return new external_single_structure([
            'text' => new external_value(PARAM_RAW, "the new $file content of the $display_name"),
            'format' => new external_value(PARAM_INT, "the format of the $file content", VALUE_DEFAULT, FORMAT_HTML),
            'itemid' => new external_value(PARAM_INT, "the item ID for file storage", VALUE_DEFAULT, IGNORE_FILE_MERGE),
        ], "the new $file content of the $display_name");
    }

    public static function update_operation(int $cmid) {
        return new module_update_operation(static::$module_name, $cmid);
    }
}
