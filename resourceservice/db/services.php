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
];