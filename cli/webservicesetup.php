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

/**
 * CLI script for local_modcontentservice.
 *
 * @package     local_modcontentservice
 * @subpackage  cli
 * @copyright   2025 Clemens Koza <ckoza@tgm.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/webservice/lib.php');

// Get the cli options.
list($options, $unrecognized) = cli_get_params([
    'help' => false,
],
[
    'h' => 'help',
]);

$help =
"
For testing purposes. Installs the service, adds an example web service user, then prints the token.
";

if ($unrecognized) {
    $unrecognized = implode("\n\t", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    cli_writeln($help);
    die();
}

$systemcontext = context_system::instance();

// Enable web services and REST protocol.
set_config('enablewebservices', true);
set_config('webserviceprotocols', 'rest');

// Create a web service user.
$webserviceuserid = user_create_user([
  'username' => 'modcontentservice-user',
  'email' => 'modcontentservice@example.com',
  'firstname' => 'Mod Content Service',
  'lastname' => 'User',
  'mnethostid' => $CFG->mnet_localhost_id,
  'confirmed' => 1,
]);

// Create a web service role.
$wsroleid = create_role('WS Role for Mod Content Service', 'ws-modcontentservice-role', '');
set_role_contextlevels($wsroleid, [CONTEXT_SYSTEM]);
assign_capability('webservice/rest:use', CAP_ALLOW, $wsroleid, $systemcontext->id, true);

// Give the user the role.
role_assign($wsroleid, $webserviceuserid, $systemcontext->id);

// Enable the externalquiz webservice.
$webservicemanager = new webservice();
$service = $webservicemanager->get_external_service_by_shortname('modcontentservice');
$service->enabled = true;
$webservicemanager->update_external_service($service);

// Authorise the user to use the service.
// $webservicemanager->add_ws_authorised_user((object) ['externalserviceid' => $service->id, 'userid' => $webserviceuserid]);

// Create a token for the user.
$token = \core_external\util::generate_token(EXTERNAL_TOKEN_PERMANENT, $service, $webserviceuserid, $systemcontext);
print($token);
