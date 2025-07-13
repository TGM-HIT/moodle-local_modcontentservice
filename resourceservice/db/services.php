<?php
$functions = [
  // The name of your web service function, as discussed above.
  'local_resourceservice_get_greeting' => [
    // The name of the namespaced class that the function is located in.
    'classname'   => 'local_resourceservice\external\get_greeting',

    // A brief, human-readable, description of the web service function.
    'description' => 'Returns a greeting.',

    // Options include read, and write.
    'type'        => 'get',

    // Whether the service is available for use in AJAX calls from the web.
    'ajax'        => true,

    // 'services' => [],
    // 'capabilities' => 'moodle/course:creategroups,moodle/course:managegroups',
  ],
  'local_resourceservice_save_page_content' => [
    'classname'   => 'local_resourceservice\external\save_page_content',
    'description' => 'Replaces the intro and content of a specified page',
    'type'        => 'write',
    'ajax'        => true,
  ],
];

$services = [
  // The name of the service.
  // This does not need to include the component name.
  'resourceservice' => [
    // A list of external functions available in this service.
    'functions' => [
      'local_resourceservice_get_greeting',
      'local_resourceservice_save_page_content',
    ],

    // If set, the external service user will need this capability to access
    // any function of this service.
    // For example: 'local_groupmanager/integration:access'
    // 'requiredcapability' => 'local_groupmanager/integration:access',

    // If enabled, the Moodle administrator must link a user to this service from the Web UI.
    'restrictedusers' => 0,

    // Whether the service is enabled by default or not.
    // 'enabled' => 0,

    // This field os optional, but requried if the `restrictedusers` value is
    // set, so as to allow configuration via the Web UI.
    'shortname' =>  'resourceservice',

    // Whether to allow file downloads.
    'downloadfiles' => 1,

    // Whether to allow file uploads.
    'uploadfiles'  => 1,
  ],
];
