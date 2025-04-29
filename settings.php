<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_quiz_closer', 'Quiz Closer');
    $ADMIN->add('localplugins', $settings);
}
