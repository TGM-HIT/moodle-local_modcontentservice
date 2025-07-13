<?php

define('CLI_SCRIPT', true);

require_once(__DIR__.'/../../../config.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/webservice/lib.php');

$systemcontext = context_system::instance();

// Enable web services and REST protocol.
set_config('enablewebservices', true);
set_config('webserviceprotocols', 'rest');

// Create a web service user.
$webserviceuserid = user_create_user([
  'username' => 'resourceservice-user',
  'email' => 'resourceservice@example.com',
  'firstname' => 'Resource Service',
  'lastname' => 'User',
  'mnethostid' => $CFG->mnet_localhost_id,
  'confirmed' => 1,
]);

// Create a web service role.
$wsroleid = create_role('WS Role for Resource Service', 'ws-resourceservice-role', '');
set_role_contextlevels($wsroleid, [CONTEXT_SYSTEM]);
assign_capability('webservice/rest:use', CAP_ALLOW, $wsroleid, $systemcontext->id, true);

// Give the user the role.
role_assign($wsroleid, $webserviceuserid, $systemcontext->id);

// Enable the externalquiz webservice.
$webservicemanager = new webservice();
$service = $webservicemanager->get_external_service_by_shortname('resourceservice');
$service->enabled = true;
$webservicemanager->update_external_service($service);

// Authorise the user to use the service.
// $webservicemanager->add_ws_authorised_user((object) ['externalserviceid' => $service->id, 'userid' => $webserviceuserid]);

// Create a token for the user.
$token = \core_external\util::generate_token(EXTERNAL_TOKEN_PERMANENT, $service, $webserviceuserid, $systemcontext);
print($token);
