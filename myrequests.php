<?php
/**
 * Simple file test_custom.php to drop into root of Moodle installation.
 * This is an example of using a sql_table class to format data.
 */
require "../../config.php";
require "$CFG->libdir/tablelib.php";

require_login();
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

global $OUTPUT, $PAGE, $DB, $USER;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/ps_selfstudy/myrequests.php');
$PAGE->set_pagelayout('standard');

/**** TABLE LIST OF PHYSICAL REQUESTS ****/
$table = new html_table();
$table->head = array('Course Code','Course Title','Request date','Status','Completion');
$table->data = array();
/**** TABLE LINK TYPE REQUESTS ****/
$table_link = new html_table();
$table_link->head = array('Course Code','Course Title','Link','Request date','Completion');
$table_link->data = array();
/**** TABLE HISTORY ****/
$table_history = new html_table();
$table_history->head = array('Course Code','Course Title','Completion date','Status');
$table_history->data = array();

//get all data from _complete table
$completionlist = $DB->get_records('block_ps_selfstudy_complete', array('student_id'=>$USER->id), $sort='', $fields='*', $limitfrom=0, $limitnum=0);
//get all data from _request table
$request = $DB->get_records('block_ps_selfstudy_request', array('student_id'=>$USER->id), $sort='', $fields='*', $limitfrom=0, $limitnum=0);
//loop the request records to form the requests list and the link list
foreach($request as $value) {
	$course = $DB->get_record('block_ps_selfstudy_course', array('id'=>$value->course_id), $fields='id,course_name,course_code,course_link');

	//format requested date from timestamp
	$timestamp = $value->request_date;
	$date = date("m/d/Y",$timestamp);

	//get status to be strings
	//if status is pending, then doesn't show the link for completion, if not, display shipped and show the completion link
	if($value->request_status == 0) {
		$status = "Pending";
		$completion = '';
	} else {
		$completion = '<a href="success.php?cid='.$course->id.'&rid='.$value->id.'">Complete</a>';
		$status = "Shipped";
	}

	//if it is course link type, add to second table, if not, add it to the request table
	if(!$course->course_link == 0) {
		//if the course was completed => skip it.
		if($DB->record_exists('block_ps_selfstudy_complete', array('request_id'=>$value->id))) {
			continue;
		} else {
			$completion = '<a href="success.php?cid='.$course->id.'&rid='.$value->id.'">Complete</a>';
			$row1 = array($course->course_code,$course->course_name,$course->course_link,$date,$completion);
    		$table_link->data[] = $row1;
		}
	} else {
		//if the course was completed => skip it.
		if($DB->record_exists('block_ps_selfstudy_complete', array('request_id'=>$value->id))) {
			continue;
		} else {
			$row = array($course->course_code,$course->course_name,$date,$status,$completion);
    		$table->data[] = $row;
		}
	}
			
}


//loop the request table_history
foreach($completionlist as $value) {
	$course = $DB->get_record('block_ps_selfstudy_course', array('id'=>$value->course_id), $fields='course_name,course_code');

	//format requested date from timestamp
	$timestamp = $value->completion_date;
	$date = date("m/d/Y",$timestamp);

	$status = ucfirst($value->completion_status);

	//add the cells to the request table
	$row = array($course->course_code,$course->course_name,$date,$status);
    $table_history->data[] = $row;		
}

// Define headers
$PAGE->set_title('My requests & courses');
$PAGE->set_heading('My requests & courses');
//$PAGE->navbar->add('My requests', new moodle_url('/blocks/ps_selfstudy/myrequests.php'));

$site = get_site();
echo $OUTPUT->header(); //output header
if(isset($_GET['success'])) {
	$success = $_GET['success'];
	if($success == 'yes') {
		echo "<div class='alert alert-success'>Order Submitted</div>";	
	} else {
		echo "<div class='alert alert-success'>Course Marked as Completed</div>";	 
	}	
}
if($table->data) {
echo "<h3>CD Courses</h3>";
echo html_writer::table($table);
} 
if($table_link->data) {
echo "<h3>Link Courses</h3>";
echo html_writer::table($table_link);	
}
if($table_history->data) {
echo "<h3>History</h3>";
echo html_writer::table($table_history);	
}
if(!$table->data && !$table_link->data && !$table_history->data) {
	echo get_string('nopendingrequests','block_ps_selfstudy');
}
echo $OUTPUT->footer();