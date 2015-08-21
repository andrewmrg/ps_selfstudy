<?php

require_once('../../config.php');
require_once('page_createcourses_form.php');

global $OUTPUT, $PAGE;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/ps_selfstudy/managecourses.php');
$PAGE->set_pagelayout('standard');
$form_page = new page_managecourses_form();

$today = time();
$toform['date_created'] = $today;
$form_page->set_data($toform);

// Define headers
$PAGE->set_title('Create a new self-study course');
$PAGE->navbar->add('Create a new self-study course', new moodle_url('/blocks/ps_selfstudy/managecourses.php'));

require_login();

if($form_page->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $courseurl = new moodle_url('/blocks/ps_selfstudy/managecourses.php');

} else if ($fromform = $form_page->get_data()) {
    // We need to add code to appropriately act on and store the submitted data
   	/*
   	1. get the data from the form
   	2. save into the db
   	3. redirect to the course list page
   	*/
   	if (!$DB->insert_record('block_ps_selfstudy_course', $fromform)) {
    	print_error('inserterror', 'block_simplehtml');
	}
	$courseurl = new moodle_url('/blocks/ps_selfstudy/managecourses.php');
	redirect($courseurl);
    //print_object($fromform);


} else {
    // form didn't validate or this is the first display
    $site = get_site();
	echo $OUTPUT->header();
	echo "<h2>Add a new course<br><br></h2>";
	$form_page->display();
	echo $OUTPUT->footer();
}

// form didn't validate or this is the first display


?>