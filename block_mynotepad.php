<?php

global $CFG, $_SESSION, $COURSE, $PAGE, $USER;
//print_object($COURSE->fullname);
//print_object($PAGE->navbar);
//print_object($PAGE);
//require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot . '/config.php');
//include_once($CFG->dirroot . '/local/mynotebook/locallib.php');
//include_once($CFG->dirroot . '/local/makenote/lib.php');
//***************************************************
//Used to get the URL of the page
//print_object($_SESSION);
//print_object($_SESSION->fromdiscussion);
//exit();
//***************************************************

class block_mynotepad extends block_base {

    //Specifies where the block can appear
    function applicable_formats() {
        return array('course' => true,
            'mod' => true,
            'site-index' => false
        );
    }

    function init() {
        $this->title = get_string('blocktitle', 'block_mynotepad');

    }

    //This makes instance_allow_config obsolete
    //If true, allows for more than one of this type
    function instance_allow_multiple() {
        return false;
    }

    /* This is allow block config when editing turned on
      function instance_allow_config() {
      return true;
      } */

    //Settings for block globally
    //Site Admin->Plugin->manage Block
    function has_config() {
        return false;
    }

    function get_content() {
        global $CFG, $USER, $DB, $PAGE;

        //Checks if there is a userid
        if (!isset($USER->id)) {
            $this->content->text = '<div class="description">' . get_string('noaccess', 'block_mynotepad') . '</div>';
        }
//    //To get the enrolled user of a course you need this tables:
////mdl_context, mdl_role, mdl_role_assignments
////1. mdl_context... get records with contextlevel = CONTEXT_COURSE (CONTEXT_COURSE = 50) and instanceid = <id of course>
////2. mdl_role... get record with shortname = 'student'
////3. mdl_role_assignments... get records with contextid = <refer 1.> und roleid = <refer 2.>
        $context = get_context_instance(CONTEXT_COURSE, $this->page->course->id);
//        print_object($context);
//    //Grabs the user info from table mdl_user. Shows how many courses user
//    //is registered in on table role_assignments relating to the contextid
        $student = get_role_users(5, $context);
        //array to store all users enrolled in this course
        $enrolled_users = array();
        $enrolled_users = array_keys($student);
//        print_object($test);

        //Checks if user is enrolled in this course
        if (!in_array($USER->id, $enrolled_users)) {
            $this->content->text = '<div class="description">' . get_string('notregistered', 'block_mynotepad') . '</div>';
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        //$this->content->footer = '';

        if (empty($this->instance)) {
            $this->content->text = '';
            return $this->content;
        }


        // optional params
        $blockInstance = $this->instance->id;
        $courseid = $this->page->course->id;
        $course_format = $this->page->course->format;
        $link = $PAGE->url;

        $cmid = required_param('id', PARAM_INT);
//        $cmid = optional_param('id', 0, PARAM_INT);
        $remove = optional_param('removenote', 0, PARAM_INT);         // used in method remove_note()
        //Grabs the current page url
//        $currpage = curPageURL();
        $url = $link;
//echo $url;
//        //Parses the url into 4 parts: dirname, basename, extension, and filename
        $path_parts = pathinfo($url);
        $dirname = $path_parts['dirname'];
        $basename = $path_parts['basename'];

//        print_object($path_parts);
//        print_object($dirname);
//
        $encrypted_url = urlencode($url);

        //debugging
//        echo "instanceid=" . $blockInstance . "</br>";
//        echo "courseid=" . $courseid . "</br>";
//        echo "cmid=" . $cmid . "</br>";
//        echo "link=" . $link . "</br>";
//        echo "course_format=" . $course_format . "</br>";
//        echo $this->page->pagetype;
//        echo $this->page->url->host;
//        echo $this->page->path;
////        print_object ($this->page);
//

        /*         * ******************************************************************** *//* Database Extraction */



//        $compare = "SELECT tabname
//                                    FROM {course_modules} th
//                                    WHERE th.tabname = '$tabname' AND th.userid = $USER->id";

        /*         * ******************************************************************** */
//
        if ($remove) { // remove a note
            if (!$this->removenote($remove)) {

            } //error(get_string('error_removing', 'block_mynotes')); }
        }
//        //Check if in a course
        if ($pos1 = strpos($dirname, "course")) {
            $this->course_notes($encrypted_url, $dirname, $courseid, $blockInstance);
            //Checks if in a mod
        } elseif ($pos1 = strpos($dirname, "mod")) {
            $this->course_mod_notes($encrypted_url, $dirname, $courseid, $blockInstance, $cmid, $course_format);
        }
    }

    //Displays the notes for the courses
    function course_notes($encrypted_url, $dirname, $courseid, $blockInstance) {
        global $CFG, $USER, $DB;

        $notes = $DB->get_records('notes', array('userid' => $USER->id, 'deleted' => 0, 'courseid' => $courseid, 'cmid' => 0), 'time_modified DESC ');
//        $notes = $DB->get_records('block_mynotepad', array('userid' => $USER->id, 'deleted' => 0, 'courseid' => $courseid, 'cmid' => 0), 'time_modified DESC ');

        $this->content->text = $this->get_javascript($encrypted_url) . '<div class="block_mynotepad">';

        //Checks if you are in a course

        if ($notes) {
            $this->content->text .= '<table class="notepad">';
            foreach ($notes as $note) {
                $note_popup = $note->name . '<hr />';

                // create the table record
                $this->content->text .= '<tr><td class="td">';

                //Opens up the note to view and edit
                $this->content->text .= "<a href='#' onmouseup='newPopup(\"$CFG->wwwroot/blocks/mynotepad/notes.php?blockInstance=$blockInstance&courseid=$courseid&id=$note->id&url=$encrypted_url\");' class=\"link_text\">";
                $this->content->text .= '<img src="' . $CFG->wwwroot . '/blocks/mynotepad/icon1.png" height="16" width="16" border="0" class="icon" alt="&nbsp&nbsp&nbsp&nbsp"/> ';

                $this->content->text .= $this->text_limit($note->name) . '</a></br>';
                $this->content->text .= $note->time_modified . '</td>';
                $this->content->text .= '<td width="6%">';

                //Delete button
                $this->content->text .= "<form action='" . urldecode($encrypted_url) . "' method='post'>";
                $this->content->text .= '<input type=\'image\' src="' . $CFG->wwwroot . '/pix/t/delete.gif" height="11" width="11" border="0" value=\'Submit\' alt=\'Submit\'>';
                $this->content->text .= '<input type=\'hidden\' name="removenote" value="' . $note->id . '" >';
                $this->content->text .= '</a></form>';
                $this->content->text .= '</td>';
                $this->content->text .= '</tr><tr>';
            }
            $this->content->text .= '</table>';
        } else {
            // no user notes found
            $this->content->text .= '<div class="description">' . get_string('nonotes', 'block_mynotepad') . '</div>';
        }

        // form to insert notes and to order by date
        $this->content->text .= '<form enctype="multipart/form-data" name="index" action="' . $encrypted_url . '" style="display:inline"><br>';
        $this->content->text .= '<table id="table2" class="notepad"><tr><td>';

        //Add a new note
//        $this->content->text .=jspopup();
        $this->content->text .="Add a new note: ";
        $this->content->text .="<a href='#' onmouseup='newPopup(\"$CFG->wwwroot/blocks/mynotepad/notes.php?blockInstance=$blockInstance&courseid=$courseid&url=$encrypted_url\");'>";
        $this->content->text .= '<img src="' . $CFG->wwwroot . '/pix/t/addfile.png" height="16" width="16" border="0"/>';
        $this->content->text .='</A></br></br>';

        $this->content->text .='</tr></table>';
        $this->content->text .= '<input name="remove" type="hidden" value=""/>';

        $this->content->text .= '</form></div>';
        return $this->content;
    }

    //Displays the notes for the course modules
    function course_mod_notes($encrypted_url, $dirname, $courseid, $blockInstance, $cmid, $course_format) {
        global $CFG, $USER, $DB;

        //Takes into consideration of the 4 different course formats
        switch ($course_format) {
            case 'weeks': case 'topics':
                //Used to get the section id
                $section = $DB->get_record('course_modules', array('id' => $cmid, 'course' => $courseid));

                //Grabs the sequence numbers
                $sql_required_param = "SELECT *
                                        FROM {course_sections} th
                                        WHERE th.id = '$section->section' AND th.course = '$courseid'";
                $sequence = $DB->get_record_sql($sql_required_param);

                if($course_format == 'weeks'){
                    echo $format = 'weeks' . '</br>';
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
                    //adds 6 days to get the first week
                    $date2->add(new DateInterval('P6D'));
                    $date_final = $date2->format('Y-m-d');

                    echo $date_init . '</br>';
                    echo $date_final . '</br>';

                    if ($sequence->section == 0){
                        //Do nothing
                        $default_section = 'Summary';
                        echo $default_section;
                    } else {
                        //Offset according to the section number the activity or resource it is in
                        $offset = 7*($sequence->section-1);
                        //Begin date for specified course section
                        $date1->add(new DateInterval('P'.$offset.'D'));
                        $date_new = $date1->format('Y-m-d');
                        echo $date_new . '</br>';
                        //End date for specified course section
                        $date2->add(new DateInterval('P'.$offset.'D'));
                        $date_new2 = $date2->format('Y-m-d');
                        echo $date_new2 . '</br>';
                    }

                } else if($course_format == 'topics') {
                    echo $format = 'topics' . '</br>';
                    $key = explode(',', $sequence->sequence);

                    for ($counter = 0; $counter < sizeof($key); $counter++) {
                        //Checks if the sequence number and id match up, since there might be more than one numbers and prints out the section name if there is one
                        if ($key[$counter] == $section->id && $sequence->name != NULL) {
                            echo $sequence->name . '</br>';
                            echo $sequence->section . '</br>';
                            print_object($sequence);
                        }
                    }
                }
                break;

            case 'social':
                echo $format = 'social' . '</br>';
                //decodes the url
                $decoded_url = urldecode($encrypted_url);
                //Finds the forum discussion id
                $discussion_id = trim(strstr($decoded_url, '='), '=');
                //grabs the discussion name
                $discussion_name = $DB->get_record('forum_discussions', array('id' => $discussion_id));
                echo $discussion_name->name;
                break;

            case 'scorm':
                echo $format = 'SCORM' . '</br>';
//                $scorm = $DB->get_record('scorm', array('id'=> , 'course'=>$cmid));
//                $scorm_name = $scorm->name;
                break;
        }

        $notes = $DB->get_records('notes', array('userid' => $USER->id, 'deleted' => 0, 'courseid' => $courseid, 'cmid' => $cmid), 'time_modified DESC ');

        $this->content->text = $this->get_javascript($encrypted_url) . '<div class="block_mynotepad">';

        //Checks if you are in a course
        if ($notes) {
            $this->content->text .= '<table class="notepad">';
            foreach ($notes as $note) {

                //if ($note->location == $blockInstance){
                //if ($courseid == $cmid){

                $note_popup = $note->name . '<hr />';

                // create the table record
                $this->content->text .= '<tr><td class="td">';

                //This is clicking on the note link
                $this->content->text .= "<a href='#' onmouseup='newPopup(\"$CFG->wwwroot/blocks/mynotepad/notes.php?blockInstance=$blockInstance&courseid=$courseid&id=$note->id&url=$encrypted_url\");' class=\"link_text\">";
                $this->content->text .= '<img src="' . $CFG->wwwroot . '/blocks/mynotepad/icon1.png" height="16" width="16" border="0" class="icon" alt="&nbsp&nbsp&nbsp&nbsp"/> ';

                $this->content->text .= $this->text_limit($note->name) . '</a></br>';
//        $this->content->text .=  date('(m/d/Y H:i:s)', $note->time_modified).'</td>';
                $this->content->text .= $note->time_modified . '</td>';
                $this->content->text .= '<td width="6%">';

                //Delete button
                //echo urldecode("$encrypted_url&remove=$note->id");
                $this->content->text .= "<form action='" . urldecode($encrypted_url) . "' method='post'>";
                $this->content->text .= '<input type=\'image\' src="' . $CFG->wwwroot . '/pix/t/delete.gif" height="11" width="11" border="0" value=\'Submit\' alt=\'Submit\'>';
                $this->content->text .= '<input type=\'hidden\' name="removenote" value="' . $note->id . '" >';
                $this->content->text .= '</a></form>';
                $this->content->text .= '</td>';
                $this->content->text .= '</tr><tr>';

                //}
            }
            $this->content->text .= '</table>';
        } else { // no user notes found
            $this->content->text .= '<div class="description">' . get_string('nonotes', 'block_mynotepad') . '</div>';
        }

        // form to insert notes and to order by date
        $this->content->text .= '<form enctype="multipart/form-data" name="index" action="' . $encrypted_url . '" style="display:inline"><br>';
        $this->content->text .= '<table id="table2" class="notepad"><tr><td>';

        //Add a new note
//        $this->content->text .=jspopup();
        $this->content->text .="Add a new note: ";
        $this->content->text .="<a href='#' onmouseup='newPopup(\"$CFG->wwwroot/blocks/mynotepad/notes.php?blockInstance=$blockInstance&courseid=$courseid&cmid=$cmid&url=$encrypted_url\");'>";
        $this->content->text .= '<img src="' . $CFG->wwwroot . '/pix/t/addfile.png" height="16" width="16" border="0"/>';
        $this->content->text .='</A></br></br>';

        $this->content->text .='</tr></table>';
        $this->content->text .= '<input name="remove" type="hidden" value=""/>';

        $this->content->text .= '</form></div>';
        return $this->content;
    }

    //Limits the length of the note title displayed
    function text_limit($text) {
        global $CFG;

        $chars = (isset($CFG->block_mynotes_chars)) ? $CFG->block_mynotes_chars : 13;
        $points = (strlen($text) > $chars) ? '...' : '';
        return (substr($text, 0, $chars) . "" . $points);
    }

    function removenote($noteid) {
        global $USER, $DB;
        $note_user = $DB->get_field('notes', 'userid', array('id' => $noteid));
        if ($note_user != $USER->id) {
            return false;
        }

//    if (($count = $DB->count_records('gallerytable', array('tablenumber'=>0, 'userid'=> $USER->id))) != 0){
//        $deletegallerynote = $DB->get_records('gallerytable', array('tablenumber'=>0, 'userid'=> $USER->id));
//
//        foreach ($deletegallerynote as $delete){
//
//           // if($delete->noteid == $noteid){
//
//            if ($DB->record_exists('gallerytable', array('noteid'=>$noteid, 'userid'=> $USER->id))){
//                $gallery_recycled_note = $DB->get_record('gallerytable', array('noteid'=>$noteid, 'userid'=> $USER->id));
//                $gallery_recycled_note->deleted = 1;
//                $updated_gallery = $DB->update_record('gallerytable', $gallery_recycled_note);
//
//                $notes_recycled_note = $DB->get_record('notes', array('id'=>$noteid, 'userid'=> $USER->id));
//                $notes_recycled_note->deleted = 1;
//                $updated_notes = $DB->update_record('notes', $notes_recycled_note);
//
//            //For notes under the "New Notes" heading
//            } else {

        $notes_recycled_note = $DB->get_record('notes', array('id' => $noteid, 'userid' => $USER->id));
        $notes_recycled_note->deleted = 1;
        $updated_notes = $DB->update_record('notes', $notes_recycled_note);

//            }
//            //}
//        }
//    }
        //return ($DB->delete_records('notes', array('id'=>$noteid)));
    }

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
     * The method get_javascript() returns all javascript necessary to the block
     *
     */
    /*     * **************
     *  Javascript  *
     * ************* */

    /** function mypopup(3)
     * Javascript function to provide a html form (htmlstring) in a popup
     *
     * @param int width
     * @param int height
     * @param string htmlstring
     *
     *
     */
    function get_javascript($encrypted_url) {
        global $CFG;

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
                '} else { exit; } }' .
                //---------------------------------

                '</SCRIPT>';

        return $javascript;
    }

}
?>


