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

require_once("forms/rates.php");

$PAGE->set_url('/local/staffmanager/rates.php');
$PAGE->set_context(context_system::instance());
$PAGE->requires->js('/local/staffmanager/assets/staffmanager.js');

require_login();

$strpagetitle = get_string('staffmanager', 'local_staffmanager');
$strpageheading = get_string('rates', 'local_staffmanager');

$PAGE->set_title($strpagetitle);
$PAGE->set_heading($strpageheading);

$id = optional_param('id', '', PARAM_TEXT);

$mform = new rates_form();
$toform = [];

// if no org ID then it is a new org
// if there is an orgid the we show the edit for with save
  $mform = new rates_form("?id=$id");
  $toform = [];
  //Form processing and displaying is done here
  if ($mform->is_cancelled()) {
      //Handle form cancel operation, if cancel button is present on form
      redirect("/local/staffmanager/rates.php", '', 10);
  } elseif ($fromform = $mform->get_data()) {
      //In this case you process validated data. $mform->get_data() returns data posted in form.
      // Save form data
      if ($id) {
          // update
          $obj = $DB->get_record('local_staffmanager_rates', ['id'=>$id]);
          $obj->month = $fromform->month;
          $obj->year = $fromform->year;
          $obj->assignmentrate = $fromform->assignmentrate;
          $obj->quizrate = $fromform->quizrate;
          $DB->update_record('local_staffmanager_rates', $obj);
      } else {
        // check if already exists to prevent duplicate rates
if($DB->record_exists('local_staffmanager_rates', ['year'=>$fromform->year,'month'=>$fromform->month]))
{
        redirect("/local/staffmanager/ratesform.php", 'Duplicate rate - rate not created', 10,  \core\output\notification::NOTIFY_SUCCESS);
} else {
          // new
          $obj = new stdClass();
          $obj->month = $fromform->month;
          $obj->year = $fromform->year;
          $obj->assignmentrate = $fromform->assignmentrate;
          $obj->quizrate = $fromform->quizrate;
          $orgid = $DB->insert_record('local_staffmanager_rates', $obj, true, false);
      }
    }
      // redirect to units page with qual id
      redirect("/local/staffmanager/rates.php?id=$id", 'Changes saved', 10,  \core\output\notification::NOTIFY_SUCCESS);
  } else {
      // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
      // or on the first display of the form.
      if ($id) {
          $toform = $DB->get_record('local_staffmanager_rates', ['id'=>$id]);
      }
      //Set default data (if any)
      $mform->set_data($toform);

      echo $OUTPUT->header();
      $mform->display();

      echo $OUTPUT->footer();
  }
