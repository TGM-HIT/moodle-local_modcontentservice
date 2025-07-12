<?php
namespace local_resourceservice\external;

use core_external\external_function_parameters;
// use core_external\external_multiple_structure;
// use core_external\external_single_structure;
use core_external\external_value;

class get_greeting extends \core_external\external_api {
  /**
   * Returns description of method parameters
   * @return external_function_parameters
   */
  public static function execute_parameters(): external_function_parameters {
    return new external_function_parameters([
      'name' => new external_value(PARAM_TEXT, 'name of the user to greet'),
    ]);
  }

  public static function execute_returns() {
    return new external_value(PARAM_TEXT, 'the greeting');
  }

  /**
   * Create groups
   * @param string $groups array of group description arrays (with keys groupname and courseid)
   * @return string of newly created groups
   */
  public static function execute(string $name): string {
    // global $CFG, $DB;
    // require_once("$CFG->dirroot/group/lib.php");

    $params = self::validate_parameters(self::execute_parameters(), ['name' => $name]);
    $name = $params["name"];

    return "Hello $name";
  }
}
