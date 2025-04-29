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

// Configuración CORS.
// Develop: header("Access-Control-Allow-Origin: http://localhost:5173");
// Production: header("Access-Control-Allow-Origin: http://161.35.53.140:5173");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejo de preflight requests.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/classes/grade_calculator.php');
// require_once($CFG->dirroot . '/mod/quiz/classes/quiz_settings.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $DB;

    // Leer JSON de entrada
    $input = json_decode(file_get_contents("php://input"), true);


    // Validar entrada
    $attemptid = isset($input['attemptid']) ? (int) $input['attemptid'] : null;
    $quizid = isset($input['quizid']) ? (int) $input['quizid'] : null;
    $cmid = isset($input['cmid']) ? (int) $input['cmid'] : null;

    if (!$attemptid || !$quizid || !$cmid) {
        echo json_encode([
            'status' => 'error',
            'error_code' => 400,
            'message' => 'Faltan parámetros requeridos (attemptid, quizid o cmid)'
        ]);
        http_response_code(400);
        exit;
    }

    // Obtener intento desde la base de datos
    $attempt = $DB->get_record('quiz_attempts', ['id' => $attemptid]);
    if (!$attempt) {
        echo json_encode([
            'status' => 'error',
            'error_code' => 404,
            'message' => "No se encontró el intento con ID $attemptid"
        ]);
        http_response_code(404);
        exit;
    }

    // Obtener el cuestionario
    $quiz = $DB->get_record('quiz', ['id' => $quizid]);
    if (!$quiz) {
        echo json_encode([
            'status' => 'error',
            'error_code' => 404,
            'message' => "No se encontró el cuestionario con ID $quizid"
        ]);
        http_response_code(404);
        exit;
    }

    // Obtener el contexto del módulo
    $context = context_module::instance($cmid);

    // Cargar intento como objeto de la clase quiz_attempt
    $quiz_attempt = new quiz_attempt($attempt, $quiz, $context, (object) ['id' => $cmid]);

    // Verificar estado del intento
    if ($quiz_attempt->get_state() !== quiz_attempt::FINISHED) {
        // Finalizar intento
        $quiz_attempt->process_finish(time(), true);

        // Calcular y guardar la nota final
        $scaled_grade = quiz_rescale_grade($attempt->sumgrades, $quiz);
        $DB->set_field('quiz_attempts', 'sumgrades', $scaled_grade, ['id' => $attemptid]);

        echo json_encode([
            'status' => 'success',
            'message' => "Intento finalizado correctamente",
            'scaled_grade' => $scaled_grade
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'error_code' => 409,
            'message' => "El intento con ID $attemptid ya ha sido finalizado"
        ]);
        http_response_code(409);
    }
    exit;
}
