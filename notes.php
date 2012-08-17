<?php
/**
**************************************************************************
**                              mynotepad                               **
**************************************************************************
* @package     block                                                    **
* @subpackage  mynotepad                                                **
* @name        mynotepad                                                **
* @copyright   oohoo.biz                                                **
* @link        http://oohoo.biz                                         **
* @author      Theodore Pham                                            **
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
**************************************************************************
**************************************************************************/

/**
* @global stdClass $CFG
* @global stdClass $USER
* @global stdClass $SESSION
* @global moodle_database $DB
* @global moodle_page $PAGE
* @global core_renderer $OUTPUT
 */

global $PAGE,$CFG,$OUTPUT,$DB,$SESSION, $USER;

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('notes_form.php');
require_once(dirname(__FILE__).'/locallib.php');

//Optional Parameters
$blockInstance = required_param('blockInstance',PARAM_INT);
$id = optional_param('id',0, PARAM_INT);
$course_id = optional_param('courseid',0, PARAM_INT);
$currpage = optional_param('url',$CFG->wwwroot.'/course/view.php?id='.$course_id,PARAM_URL);
$cmid = optional_param('cmid',0, PARAM_INT);

$PAGE->set_pagelayout('popup');
$PAGE->set_url($CFG->wwwroot . '/blocks/mynotepad/notes.php');

//Checks if the course exists
if($course_id > 0) {
    require_login($course_id);
} else {
    print_error('No course id');
}

// Output starts here
echo $OUTPUT->header();

//Create a new moodle form
$mform = new notes_form($id, $course_id, $currpage, $blockInstance, $cmid);

//---------------CANCEL BUTTON PRESSED------------------------------------------
//------------------------------------------------------------------------------
    if ($mform->is_cancelled()) {

 //When cancel or close window buton is pressed, close the pop up window
self_close();

//---------------PARTIAL SUBMIT-------------------------------------------------
//------------------------------------------------------------------------------
    } elseif ($mform->no_submit_button_pressed()) {

        //When submit button not pressed, do nothing basically

} elseif ($fromform = $mform->get_data()) {

    //When submitbutton is clicked, save to database and refresh the page
    echo $cmid;
    $text = $fromform->text;
    $title = $fromform->title;

//Checks if this is a new note or an existing note
if ($id == 0){
    //Adds a new note to the database
    $id = addnote($course_id, $text, $title, $blockInstance, $currpage, $cmid);

} else {
    //Updates existing note
    updatenote($id, $text, $title);

}

close_and_refresh();

refresh($currpage);

} else {
    $mform->display();

}

// Finish the page
echo $OUTPUT->footer();
?>
