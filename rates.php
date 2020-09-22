<?php

// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once('../../config.php');
global $USER, $DB, $CFG;

$PAGE->set_url('/local/staffmanager/rates.php');
$PAGE->set_context(context_system::instance());
$PAGE->requires->js('/local/staffmanager/assets/staffmanager.js');

require_login();

$strpagetitle = get_string('staffmanager', 'local_staffmanager');
$strpageheading = get_string('rates', 'local_staffmanager');

$PAGE->set_title($strpagetitle);
$PAGE->set_heading($strpageheading);

$rates = $DB->get_records('local_staffmanager_rates',null,'year DESC,month ASC');
foreach ($rates as $key => $value)
{
  $rates[$key]->monthname = date("F", mktime(0, 0, 0, $rates[$key]->month, 10));
}

$results = new stdClass();
$results->data = array_values($rates);

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('local_staffmanager/rates', $results);

echo $OUTPUT->footer();
