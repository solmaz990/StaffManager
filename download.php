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

require_once '../../config.php';
global $USER, $DB, $CFG;

require_login();

$dataformat = optional_param('dataformat', '', PARAM_ALPHA);
$year = optional_param('year', '', PARAM_INT);
$month = optional_param('month', '', PARAM_INT);
$monthname = date('F',strtotime($year."-".$month));

$start = mktime(0,0,0,(int)$month,1,(int)$year);
$end  = mktime(23,59,00,(int)$month+1,0,(int)$year);

$rate = $DB->get_record('local_staffmanager_rates',['year'=>$year,'month'=>$month]);

$columns = array(
    'year' => "Year",
    'monthname' => "Month",
    'graderfirstname' => get_string('graderfirstname', 'local_staffmanager'),
    'graderlastname' => get_string('graderlastname', 'local_staffmanager'),
    'graderemail' => get_string('graderemail', 'local_staffmanager'),
    'coursename' => get_string('coursename', 'local_staffmanager'),
    'studentfirstname' => get_string('studentfirstname', 'local_staffmanager'),
    'studentlastname' => get_string('studentlastname', 'local_staffmanager'),
    'studentemail' => get_string('studentemail', 'local_staffmanager'),
    'gradeitemname' => get_string('gradeitemname', 'local_staffmanager'),
    'modulename' => get_string('modulename', 'local_staffmanager'),
    'finalgrade' => get_string('finalgrade', 'local_staffmanager'),
    'value' => "Value",
    'datetimemodified' => get_string('datetimemodified', 'local_staffmanager')
);
$sql = "SELECT gg.id as gradeid,
concat('$year','') AS year, concat('$monthname','') AS month,
grader.firstname AS graderfirstname, grader.lastname AS graderlastname, grader.email AS graderemail,
c.fullname as coursename, u.firstname AS studentfirstname,u.lastname AS studentlastname, u.email AS studentemail,
gi.itemname AS gradeitemname,
gi.itemmodule AS modulename, gg.finalgrade AS finalgrade, gg.timemodified AS tmodified
FROM {grade_grades} AS gg
JOIN {user} AS u ON u.id = gg.userid
JOIN {user} AS grader ON grader.id = gg.usermodified
JOIN {grade_items} AS gi ON gi.id = gg.itemid
JOIN {course} AS c ON gi.courseid = c.id
WHERE gg.finalgrade > 0 AND gg.timemodified >= ". $start." AND gg.timemodified <=".$end ;
$grades = $DB->get_records_sql($sql);

foreach ($grades as $key => $value)
{
    $grades[$key]->value = 0;
    if($grades[$key]->modulename == 'assign')
    {
      $grades[$key]->value = "$".$rate->assignmentrate;
    }
    if($grades[$key]->modulename == 'quiz')
    {
      $grades[$key]->value = "$".$rate->quizrate;
    }
}

//print_r($grades);
$obj = new ArrayObject( $grades );
$it = $obj->getIterator();
//\core\dataformat::download_data('graderdata', $dataformat, $columns, $it);
 \core\dataformat::download_data('graderdata', $dataformat, $columns, $it, function($record) {

  $record->datetimemodified = date('d-M-Y H:m',  $record->tmodified);
  unset($record->gradeid);
  unset($record->tmodified);
    // Process the data in some way.
    // You can add and remove columns as needed
    // as long as the resulting data matches the $column metadata.
    return $record;
});
