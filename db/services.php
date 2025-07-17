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
 * External functions and services provided by the plugin are declared here.
 *
 * @package     local_modcontentservice
 * @category    external
 * @copyright   2025 Clemens Koza <ckoza@tgm.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [

    'local_modcontentservice_update_page_content' => [
        'classname' => '\local_modcontentservice\external\update_page_content',
        'methodname' => 'execute',
        'description' => 'Replaces the intro and content of a specified page',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
        'readonlysession' => false,
    ],
];

$services = [
    'Mod Content Service' => [
        'functions' => [
            'local_modcontentservice_update_page_content',
        ],
        'shortname' => 'modcontentservice',
        'restrictedusers' => false,
        'downloadfiles' => true,
        'uploadfiles' => true,
    ],
];
