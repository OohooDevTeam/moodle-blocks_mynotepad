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

defined('MOODLE_INTERNAL') || die();

// Hide script from non-javascript browsers.
// Load Page Into Parent Window
// Version 1.0
// Last Updated: May 18, 2000
// Code maintained at: http://www.moock.org/webdesign/javascript/
// Copy permission granted any use provided this notice is unaltered.
// Written by Colin Moock.
/**
 * Used in notes_form.php
 * Changes the parent window when user clicks on the link where the note was taken.
 */
function change_parent(){
    echo "<script language='javascript' type='text/javascript'>;

function loadinparent(url, closeSelf){
	self.opener.location = url;
	if(closeSelf) self.close();
	}
</SCRIPT>
";
    echo "</script>";
}

/**
 * Used in notes.php
 * Closes the pop up window and refreshes the page
 */
function close_and_refresh(){
         echo "<script language='javascript' type='text/javascript'>;
opener.location.reload()
setTimeout('self.close();',0);
        ";
        echo "</script>";
}

/**
 * Used in notes.php
 * Closes the pop up window
 */
function self_close(){
         echo "<script language='javascript' type='text/javascript'>;
setTimeout('self.close();',0);
        ";
        echo "</script>";
}

/**
 * Used in notes.php
 * Refreshes the page given the page url
 *
 * @param string $url This string is for the window location
 */
function refresh($url){

    echo "<SCRIPT language=JavaScript>;

function win(){
window.opener.location.href='$url';
self.close();

}";
echo "</SCRIPT>";
}

/**
 * Used in notes.php
 * This function updates the note the user has made change to the database.
 *
 * @global moodle_database $DB
 * @global stdClass $CFG
 * @global stdClass $USER
 * @param int $id This number is for finding the note id
 * @param string $text This string is for changed note text
 * @param string $title This string is for the new note title
 */
function updatenote($id, $text, $title){
      global $DB, $CFG, $USER;

      $note = $DB->get_record('notes', array('id'=>$id, 'deleted'=>0, 'userid'=> $USER->id));
      $note->name = $title;
      $note->text = $text;
    //Update the note
    $update = $DB->update_record('notes', $note);
}

/**
 * Used in notes.php
 * This function adds the new note that the user created in the database.
 *
 * @global stdClass $USER
 * @global moodle_database $DB
 * @param int $course_id This number is for the notes course id
 * @param string $text This string is for the notes content
 * @param string $title This string is for the notes title
 * @param int $blockInstance This number is for which block the note belonged to
 * @param string $noteurl This string is for the notes created location
 * @param int $cmid This number is for notes creation in a course module or activity
 * @return boolean Returns true or fals depending if the data was successfully added or not
 */
  function addnote($course_id, $text, $title, $blockInstance, $noteurl, $cmid) {
    global $USER, $DB;

    $note = new stdClass();
    $note->userid = $USER->id;
    $note->text = $text;
    $note->name = $title;
    $note->location = $blockInstance;
    $note->noteurl = $noteurl;
    $note->time_modified = date('(m/d/Y H:i:s)', time());
    $note->courseid = $course_id;
    $note->cmid = $cmid;

    return ($DB->insert_record('notes', $note));
  }