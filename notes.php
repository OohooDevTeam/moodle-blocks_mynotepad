<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 * This is where students take their notes
 */
GLOBAL $PAGE,$CFG,$OUTPUT,$DB,$SESSION, $USER;

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('notes_form.php');
//Just added recently
//require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php'); 

//***VERY IMPORTANT***

$blockInstance = required_param('blockInstance',PARAM_INT);
$id = optional_param('id',0, PARAM_INT); 
$course_id = optional_param('courseid',0, PARAM_INT); 
$currpage = optional_param('url',$CFG->wwwroot.'/course/view.php?id='.$course_id,PARAM_URL);
$cmid = optional_param('cmid',0, PARAM_INT);
//echo $cmid . "</br>";
//echo $id;
//$noteid = optional_param('noteid', 0 , PARAM_INT);
//print $course_id;

$PAGE->set_pagelayout('popup');
$PAGE->set_url($CFG->wwwroot . '/blocks/mynotepad/notes.php');

if($course_id > 0) {
    require_login($course_id);   
} else {
    print_error('No course id');
}

// Output starts here
echo $OUTPUT->header();

$mform = new notes_form($id, $course_id, $currpage, $blockInstance, $cmid);
 
//---------------CANCEL BUTTON PRESSED------------------------------------------
//------------------------------------------------------------------------------
    if ($mform->is_cancelled()) {
 
 //When cancel or close window buton is pressed
//echo "<FORM name='text'>";
//echo "<center><INPUT type='button' value='Close Window' onClick='window.close(\"resizable=yes,scrollbars=yes,width=50,height=50,left=0,top=0\")'></center>";
//echo "</FORM>";
        
self_close();
 
//echo "<center><INPUT type='button' value='Close Window' onClick='parent.opener.location.reload();window.close();'></center>";
//echo "<center><INPUT type='button' value='Close Window' onClick='window.close(); if (window.opener && !window.opener.closed) { window.opener.location.reload();'></center>";
//echo "<center><INPUT type='button' value='Close Window' onClick='window.onunload = function() {
//window.opener.location.refresh();'></center>";
//
//window.close(); opener.refreshme();
//parent.opener.location.reload();window.close();

//---------------PARTIAL SUBMIT-------------------------------------------------
//------------------------------------------------------------------------------
    } elseif ($mform->no_submit_button_pressed()) {
        
        //When submit button not pressed, do nothing basically
 
} elseif ($fromform = $mform->get_data()) {
 
    //When submitbutton is clicked, save to database
    //and refresh the page
    echo $cmid;
 $text = $fromform->text;
 $title = $fromform->title;

 //$text = clean_text($text);
 //$any = addnote($text, $title, $course_id);

 //print_object($currpage);
 
if ($id == 0){   
 $id = addnote($course_id, $text, $title, $blockInstance, $currpage, $cmid);
}

else{
updatenote($id, $text, $title);
}

close_and_refresh();

//echo "<center><input type=button onClick='win();' value='Close window'></center>";
//
refresh($currpage);

} else {   
    
    // $noteid = get_record with noteid
    //$toform = new stdClass();
    //$toform->text = ''; // noteid->content
    //$toform->text = array('text'=>'Text in text area', 'format'=>FORMAT_HTML);    
  //$mform->set_data($toform);
    $mform->display();   
    
}

// Finish the page
echo $OUTPUT->footer();


?>
