<?php

// This file is part of YvagaCore - https://yvagacore.tech/
//
// YvagaCore is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// YvagaCore is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with YvagaCore. If not, see <http: //www.gnu.org/licenses />.

/**
 * Version information for the quizaccess_delaybetweenattempts plugin.
 *
 * @package quizaccess
 * @subpackage delaybetweenattempts
 * @copyright 2024 YvagaCore
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\mod_quiz\event\attempt_submitted', // Evento al que escuchamos.
        'callback' => '\local_quiz_closer\observer::attempt_submitted',
        'internal' => false,
    ],
];