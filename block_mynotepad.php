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
* @author      Hugo Santos (Modified/Based code on Hugo Santos)         **
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
**************************************************************************
**************************************************************************/

require_once($CFG->dirroot . '/config.php');

class block_mynotepad extends block_base {


    /**
     * Specifies what pages the block can be added to
     *
     * @return array of booleans Returns what pages the block is allowed/disallowed can be added to
     */
    function applicable_formats() {
        return array('course' => true,
            'mod' => true,
            'site-index' => false
        );
    }

    /**
    * Initializes the block title
    */
    function init() {
        $this->title = get_string('blocktitle', 'block_mynotepad');
    }

    /**
     * Function to allow multiple instances of the block. Makes the function instance_allow_config obsolete.
     *
     * @return boolean Disallows multiple instances of the same block
     */
    function instance_allow_multiple() {
        return false;
    }

    /**
     * Disallow the configuration of the block
     *
     * @return boolean Disallows configuration of the block
     */
    function has_config() {
        return false;
    }

    /**
     * Used to populate the $this->content variable to be displayed in the block
     *
     * @global stdClass $CFG
     * @global stdClass $USER
     * @global moodle_database $DB
     * @global moodle_page $PAGE
     * @return object Returns the content to be displayed as a string
     */
    function get_content() {
        global $CFG, $USER, $DB, $PAGE;
        //Checks if there is a userid
        if (!isset($USER->id)) {
            //Denies non-users access
            $this->content->text = '<div class="description">' . get_string('noaccess', 'block_mynotepad') . '</div>';
        }

        /*To get the enrolled user of a course you need this tables:
        mdl_context, mdl_role, mdl_role_assignments
        1. mdl_context... get records with contextlevel = CONTEXT_COURSE (CONTEXT_COURSE = 50) and instanceid = <id of course>
        2. mdl_role... get record with shortname = 'student'
        3. mdl_role_assignments... get records with contextid = <refer 1.> und roleid = <refer 2.>*/
        $context = get_context_instance(CONTEXT_COURSE, $this->page->course->id);

        /*Grabs the user info from table mdl_user. Shows how many courses the user
        is registered in on table role_assignments relative to the contextid*/
        $student = get_role_users(5, $context);

        $enrolled_users = array();
        //Store all users enrolled in this course
        $enrolled_users = array_keys($student);

        //Checks if user is enrolled in this course
        if (!in_array($USER->id, $enrolled_users)) {
            $this->content->text = '<div class="description">' . get_string('notregistered', 'block_mynotepad') . '</div>';
        }

        if ($this->content != NULL) {
            return $this->content;
        }

        $this->content = new stdClass;

        if (empty($this->instance)) {
            $this->content->text = '';
            return $this->content;
        }

        //Optional params
        $blockInstance = $this->instance->id;
        $courseid = $this->page->course->id;
        $course_format = $this->page->course->format;
        $link = $PAGE->url;

        $cmid = required_param('id', PARAM_INT);
        $remove = optional_param('removenote', 0, PARAM_INT);
        $url = $link;

        //Parses the url into 4 parts: dirname, basename, extension, and filename
        $path_parts = pathinfo($url);
        $dirname = $path_parts['dirname'];
        $basename = $path_parts['basename'];

        $encrypted_url = urlencode($url);

        if ($remove) { // remove a note
            if (!$this->removenote($remove)) {

            } //error(get_string('error_removing', 'block_mynotes')); }
        }

        //Check if in a course
        if ($pos1 = strpos($dirname, "course")) {
            $this->course_notes($encrypted_url, $courseid, $blockInstance);

        //Checks if in a mod
        } elseif ($pos1 = strpos($dirname, "mod")) {
            $this->course_mod_notes($encrypted_url, $courseid, $blockInstance, $cmid, $course_format);

        }
    }

    /**
     * Displays the notes for current course main page and passes the 3 param variables to note.php to store in database for each note
     *
     * @global stdClass $CFG
     * @global stdClass $USER
     * @global moodle_database $DB
     * @param string $encrypted_url This string is for storing where the note was taken
     * @param int $courseid This number is for finding notes belonging to this course
     * @param int $blockInstance This number is for identifying blocks instances for each note
     * @return object Returns the content to be displayed in the block
     */
    function course_notes($encrypted_url, $courseid, $blockInstance) {
        global $CFG, $USER, $DB;

        $notes = $DB->get_records('notes', array('userid' => $USER->id, 'deleted' => 0, 'courseid' => $courseid, 'cmid' => 0), 'time_modified DESC ');
        $this->content->text = $this->get_javascript($encrypted_url) . '<div class="block_mynotepad">';

        //Checks if the note exists
        if ($notes) {
            $this->content->text .= '<table class="notepad">';
            //Loop to display notes
            foreach ($notes as $note) {
                $note_popup = $note->name . '<hr />';

                //Creates a table to display all the notes taken on the page
                $this->content->text .= '<tr><td class="td">';

                //Onclick, opens up the note for editing
                $this->content->text .=     "<a href='#' onmouseup='newPopup(\"$CFG->wwwroot/blocks/mynotepad/notes.php?blockInstance=$blockInstance&courseid=$courseid&id=$note->id&url=$encrypted_url\");' class=\"link_text\">";
                $this->content->text .=     '<img src="' . $CFG->wwwroot . '/blocks/mynotepad/pic/note.png" height="25" width="25" border="0" class="icon" alt="&nbsp&nbsp&nbsp&nbsp"/> ';

                //Limits the title of each note displayed so overflow is controlled
                $this->content->text .=     $this->text_limit($note->name) . '</a></br>';

                //Display time and date note was taken
                $this->content->text .=     $note->time_modified . '</td>';
                $this->content->text .=     '<td width="6%">';

                //Creates a delete button
                $this->content->text .=         "<form action='" . urldecode($encrypted_url) . "' method='post'>";
                $this->content->text .=         '<input type=\'image\' src="' . $CFG->wwwroot . '/blocks/mynotepad/pic/delete.png" height="11" width="11" border="0" value=\'Submit\' alt=\'Submit\'>';
                $this->content->text .=         '<input type=\'hidden\' name="removenote" value="' . $note->id . '" >';
                $this->content->text .=         '</a></form>';
                $this->content->text .=     '</td>';
                $this->content->text .= '</tr>';
            }
            $this->content->text .= '</table>';

        } else {
            //Displays text indicating no notes were found
            $this->content->text .= '<div class="description">' . get_string('nonotes', 'block_mynotepad') . '</div>';
        }

        //Form to insert notes and to order by date
        $this->content->text .= '<form enctype="multipart/form-data" name="index" action="' . $encrypted_url . '" style="display:inline"><br>';
        $this->content->text .=     '<table id="table2" class="notepad"><tr><td>';

        //Creates a button to create a new note
        $this->content->text .=     get_string('add_note', 'block_mynotepad');
        $this->content->text .=     "<a href='#' onmouseup='newPopup(\"$CFG->wwwroot/blocks/mynotepad/notes.php?blockInstance=$blockInstance&courseid=$courseid&url=$encrypted_url\");'>";
        $this->content->text .=     '<img src="' . $CFG->wwwroot . '/blocks/mynotepad/pic/addfile.png" height="20" width="20" border="0"/>';
        $this->content->text .=     '</A></br></br>';

        $this->content->text .=     '</tr></table>';
        $this->content->text .=     '<input name="remove" type="hidden" value=""/>';
        $this->content->text .= '</form></div>';
        return $this->content;
    }

    /**
     * Displays the notes for current course main page and passes the 3 param(courseid, blockInstance,encrypted_url) variables to note.php to store in database for each note
     *
     * @global stdClass $CFG
     * @global stdClass $USER
     * @global moodle_database $DB
     * @param string $encrypted_url
     * @param int $courseid This number is for finding notes belonging to this course
     * @param int $blockInstance This number is for identifying blocks instances for each note
     * @param int $cmid This number is for finding notes belonging to this course module
     * @param string $course_format This string is for finding what format the course is set up to be
     * @return object Returns the content to be displayed in the block for course modules
     */
    function course_mod_notes($encrypted_url, $courseid, $blockInstance, $cmid, $course_format) {
        global $CFG, $USER, $DB;

        //Takes into consideration of the 4 different course formats
        switch ($course_format) {
            case 'weeks': case 'topics':
                //Used to get the section id
                $section = $DB->get_record('course_modules', array('id' => $cmid, 'course' => $courseid));

                //Grabs the sequence numbers for each course
                $sql_required_param = "SELECT *
                                        FROM {course_sections} th
                                        WHERE th.id = '$section->section' AND th.course = '$courseid'";
                $sequence = $DB->get_record_sql($sql_required_param);

                if($course_format == 'weeks'){
                    $course_start = $DB->get_record('course', array('id' => $sequence->course));

                    $date_sql = date('(m/d/Y)', $course_start->startdate);
                    $output = explode('/', $date_sql);

                    //Trims the open bracket
                    $month_str = trim($output[0],'(');
                    $day_init = $output[1];
                    $year = trim($output[2],')');

                    //Initial week dates that course was created
                    $date1 = new DateTime("$year-$month_str-$day_init");
                    $date_init = $date1->format('Y-m-d');

                    $date2 = new DateTime("$date_init");
                    //Adds 6 days to get the first week
                    $date2->add(new DateInterval('P6D'));
                    $date_final = $date2->format('Y-m-d');

                    if ($sequence->section == 0){
                        //Do nothing
                        $default_section = 'Summary';
                        echo $default_section;

                    } else {
                        //Offset according to the section number the activity or resource it is in to calculate the date
                        $offset = 7*($sequence->section-1);
                        //Begin date for specified course section
                        $date1->add(new DateInterval('P'.$offset.'D'));
                        $date_new = $date1->format('Y-m-d');

                        //End date for specified course section
                        $date2->add(new DateInterval('P'.$offset.'D'));
                        $date_new2 = $date2->format('Y-m-d');
                    }

                } elseif ($course_format == 'topics') {
                    $key = explode(',', $sequence->sequence);

                    for ($counter = 0; $counter < sizeof($key); $counter++) {
                        //Checks if the sequence number and id match up, since there might be more than one number and prints out the section name if there is one
                        if ($key[$counter] == $section->id && $sequence->name != NULL) {

                        }
                    }
                }
                break;

            case 'social':
                //Decodes the url
                $decoded_url = urldecode($encrypted_url);
                //Finds the forum discussion id
                $discussion_id = trim(strstr($decoded_url, '='), '=');
                //grabs the discussion name
                $discussion_name = $DB->get_record('forum_discussions', array('id' => $discussion_id));
                echo $discussion_name->name;
                break;

            case 'scorm':
                $scorm = $DB->get_record('scorm', array('course'=>$cmid));
                $scorm = $scorm->name;
                break;
        }

        $notes = $DB->get_records('notes', array('userid' => $USER->id, 'deleted' => 0, 'courseid' => $courseid, 'cmid' => $cmid), 'time_modified DESC ');

        $this->content->text = $this->get_javascript($encrypted_url) . '<div class="block_mynotepad">';

        //Checks if you are in a course
        if ($notes) {
            $this->content->text .= '<table class="notepad">';
            foreach ($notes as $note) {

                $note_popup = $note->name . '<hr />';

                //Create the table record
                $this->content->text .= '<tr><td class="td">';

                //This is clicking on the note link
                $this->content->text .=     "<a href='#' onmouseup='newPopup(\"$CFG->wwwroot/blocks/mynotepad/notes.php?blockInstance=$blockInstance&courseid=$courseid&id=$note->id&url=$encrypted_url\");' class=\"link_text\">";
                $this->content->text .=     '<img src="' . $CFG->wwwroot . '/blocks/mynotepad/pic/note.png" height="25" width="25" border="0" class="icon" alt="&nbsp&nbsp&nbsp&nbsp"/> ';

                $this->content->text .=     $this->text_limit($note->name) . '</a></br>';
                $this->content->text .=     $note->time_modified . '</td>';
                $this->content->text .=     '<td width="6%">';

                //Delete button
                $this->content->text .=     "<form action='" . urldecode($encrypted_url) . "' method='post'>";
                $this->content->text .=     '<input type=\'image\' src="' . $CFG->wwwroot . '/blocks/mynotepad/pic/delete.png" height="11" width="11" border="0" value=\'Submit\' alt=\'Submit\'>';
                $this->content->text .=     '<input type=\'hidden\' name="removenote" value="' . $note->id . '" >';
                $this->content->text .=     '</a></form>';
                $this->content->text .=     '</td>';
                $this->content->text .= '</tr><tr>';

            }
            $this->content->text .= '</table>';
        } else { //No user notes found
            $this->content->text .= '<div class="description">' . get_string('nonotes', 'block_mynotepad') . '</div>';
        }

        //Form to insert notes and to order by date
        $this->content->text .= '<form enctype="multipart/form-data" name="index" action="' . $encrypted_url . '" style="display:inline"><br>';
        $this->content->text .=     '<table id="table2" class="notepad"><tr><td>';

        //Add a new note
        $this->content->text .=         "Add a new note: ";
        $this->content->text .=         "<a href='#' onmouseup='newPopup(\"$CFG->wwwroot/blocks/mynotepad/notes.php?blockInstance=$blockInstance&courseid=$courseid&cmid=$cmid&url=$encrypted_url\");'>";
        $this->content->text .=         '<img src="' . $CFG->wwwroot . '/blocks/mynotepad/pic/addfile.png" height="20" width="20" border="0"/>';
        $this->content->text .=         '</A></br></br>';

        $this->content->text .=     '</tr></table>';
        $this->content->text .=     '<input name="remove" type="hidden" value=""/>';

        $this->content->text .= '</form></div>';
        return $this->content;
    }

    /**
     * This function limits the length of the note title which is displayed
     *
     * @global stdClass $CFG
     * @param string $text This string is for the user input of their note title
     * @return string Return the shortened string
     */
    function text_limit($text) {
        global $CFG;
        $chars = (isset($CFG->block_mynotes_chars)) ? $CFG->block_mynotes_chars : 13;
        $points = (strlen($text) > $chars) ? '...' : '';
        return (substr($text, 0, $chars) . "" . $points);
    }

    /**
     * This function moves the note to the recycle bin
     *
     * @global stdClass $USER
     * @global moodle_database $DB
     * @param int $noteid This number is for finding the note with this id
     * @return boolean Returns whether the current user id matches
     */
    function removenote($noteid) {
        global $USER, $DB;
        $note_user = $DB->get_field('notes', 'userid', array('id' => $noteid));

        if ($note_user != $USER->id) {
            return false;
        }

        $notes_recycled_note = $DB->get_record('notes', array('id' => $noteid, 'userid' => $USER->id));
        $notes_recycled_note->deleted = 1;
        $updated_notes = $DB->update_record('notes', $notes_recycled_note);
    }

    /**
     * Converts month numerical value to it's string equivalent
     *
     * @param int $month_str This number represents the numerical value of eah month
     * @return string Returns the string value of the month
     */
    function convert_to_month($month_str) {
        switch ($month_str) {
            case '01': $month = 'January'; break;
            case '02': $month = 'February'; break;
            case '03': $month = 'March'; break;
            case '04': $month = 'April'; break;
            case '05': $month = 'May'; break;
            case '06': $month = 'June'; break;
            case '07': $month = 'July'; break;
            case '08': $month = 'August'; break;
            case '09': $month = 'September'; break;
            case '10': $month = 'October'; break;
            case '11': $month = 'November'; break;
            case '12': $month = 'December'; break;
            default  : $month = 'Month Does Not Exist'; break;
            }
        return $month;
    }

    /**
     *  This function enables user to open a pop up to edit their notes
     *
     * @param string $encrypted_url This string is for opening up the note in a pop up window
     * @return string Returns the Javascript function to provide a html form (htmlstring) in a popup
     */
    function get_javascript($encrypted_url) {

        $javascript =
                '<SCRIPT language="javascript" type="text/javascript">' .
                'function newPopup(url) {' .
                'newwindow=window.open(url,"name","height=600,width=650,scrollbars=yes");' .
                'if (window.focus) {newwindow.focus()}' .
                'return false;' .
                '}' .
                //---------------------------------
                'function mynotes_remove(noteid) {' .
                    'var_confirm=confirm("' . get_string('confirm_delete', 'block_mynotepad') . '");' .
                    'if(var_confirm==true) {' .
                        'document.index.remove.value=noteid;' .
                        'document.index.submit();' .
                    '} else { exit; }
                }' .
                '</SCRIPT>';

        return $javascript;
    }
}
?>


