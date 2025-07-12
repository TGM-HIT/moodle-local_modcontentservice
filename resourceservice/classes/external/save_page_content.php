<?php
namespace local_resourceservice\external;

use core_external\external_function_parameters;
// use core_external\external_multiple_structure;
// use core_external\external_single_structure;
use core_external\external_value;

require_once($CFG->dirroot . '/course/modlib.php');

class save_page_content extends \core_external\external_api {
  /**
   * Returns description of method parameters
   * @return external_function_parameters
   */
  public static function execute_parameters(): external_function_parameters {
    return new external_function_parameters([
      'cmid' => new external_value(PARAM_INT, 'course module ID of the page to update'),
    ]);
  }

  public static function execute_returns() {
    return new external_value(PARAM_RAW, 'the result');
  }

  /**
   * Create groups
   * @param string $groups array of group description arrays (with keys groupname and courseid)
   * @return string of newly created groups
   */
  public static function execute(int $cmid): string {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    $params = self::validate_parameters(self::execute_parameters(), ['cmid' => $cmid]);
    $cmid = $params["cmid"];

    $moduleinfo = get_coursemodule_from_id('page', $cmid, 0, false, MUST_EXIST);

    $moduleinfo->coursemodule = $cmid;
    $moduleinfo->introeditor = [
      "text" => "<p>new a</p>",
      "format" => FORMAT_HTML,
      "itemid" => IGNORE_FILE_MERGE,
    ];
    $moduleinfo->page = [
      "text" => "<p>new b</p>",
      "format" => FORMAT_HTML,
      "itemid" => null,
    ];

    update_module($moduleinfo);

    return "ok";
  }
}
