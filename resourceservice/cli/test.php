<?php

define('CLI_SCRIPT', true);

// Check that PHP is of a sufficient version as soon as possible.
require_once(__DIR__.'/../../../lib/phpminimumversionlib.php');
moodle_require_minimum_php_version();

// Nothing to do if config.php does not exist
$configfile = __DIR__.'/../../../config.php';
if (!file_exists($configfile)) {
    fwrite(STDERR, 'config.php does not exist, can not continue'); // do not localize
    fwrite(STDERR, "\n");
    exit(1);
}

// Include necessary libs
require($configfile);

print("Test");

global $CFG, $DB;
require_once("$CFG->libdir/filelib.php");
require_once("$CFG->dirroot/course/modlib.php");

$cmid = 5;
$moduleinfo = get_coursemodule_from_id('page', $cmid, 0, false, MUST_EXIST);
var_export($moduleinfo);

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

var_export($moduleinfo);

update_module($moduleinfo);
