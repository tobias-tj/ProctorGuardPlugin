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

namespace local_quiz_closer;

class observer
{
    public static function attempt_submitted($event)
    {
        $data = $event->get_data();
        // Aquí obtienes los datos del intento enviado.
        $attemptid = $data['objectid'];
        $userid = $data['relateduserid'];

        // Envía una solicitud HTTP POST a React.
        // Develop: http://localhost:5173
        // Prod: https://proctorguard.yvagacore.com
        $url = 'https://proctorguard.yvagacore.com';
        $payload = json_encode([
            'attemptid' => $attemptid,
            'userid' => $userid,
        ]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }
}
