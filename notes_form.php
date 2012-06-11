<?php

global $CFG;

require_once($CFG->libdir.'/formslib.php');
//require_once(dirname(__FILE__).'/block_mynotepad.php'); 
//require_once(dirname(__FILE__).'/locallib.php'); 

class notes_form extends moodleform {
 
    private $id;
    private $course_id;
    private $url;
    private $blockInstance;
    private $cmid;
       
    function __construct($id, $course_id,$return_url,$blockInstance, $cmid){
        $this->id = $id;
        $this->course_id = $course_id;
        $this->url = $return_url;
        $this->blockInstance = $blockInstance;
        $this->cmid = $cmid;
        parent::__construct();
    }

    function definition() {
        //Need the id number
        global $CFG, $SESSION, $PAGE, $DB, $USER;
        //print_object($id);
        $course_id = optional_param('courseid',0, PARAM_INT);
        //$currpage = optional_param('url',$CFG->wwwroot.'/course/view.php?id='.$course_id,PARAM_URL);
//print_object($currpage);

        //$DB->get_record('notes', array('noteurl'))
//print $this->id;
        if ($DB->record_exists('notes', array('id'=>$this->id))){
            $currpage1 = $DB->get_record('notes', array('id'=>$this->id));
            $currpage = $currpage1->noteurl;
            $url = $currpage;
        } else {
            $url = optional_param('url',$CFG->wwwroot.'/course/view.php?id='.$course_id,PARAM_URL);
            
        }
$path_parts = pathinfo($url);

//echo $path_parts['dirname'], "</br>";
//echo $path_parts['basename'], "</br>";
//echo $path_parts['extension'], "</br>";
//echo $path_parts['filename'], "</br>"; // since PHP 5.2.0

$dirname = $path_parts['dirname'];
$basename = $path_parts['basename'];
//echo $dirname;
//echo "</br>";
//echo $basename;


//change_parent();

        $mform =& $this->_form; // Don't forget the underscore! 
        
        //Link to page note was taken
        $mform->addElement('html', '<div class="link">');
        $mform->addElement('html', '<center>');

        //***Redirect the parent window to the course page the note was taken***//

        //Checks if you are in a mod page and creates the link in your note
//        if ($this->id > 0){
//            if ($pos1 = strpos($dirname, "mod")){
//                $pos1 = strpos($basename, "id=");
//                $pos1 = $pos1+3;
//                $moduleid = substr("$basename", $pos1, strlen($basename));
// 
//                $course_name = $DB->get_record('course', array('id'=>$course_id), '*');
////            print $course_name->fullname;
//                $mod_id = $DB->get_record('course_modules', array('id'=>$moduleid, 'course'=>$course_id));
////            print_object($mod_id->module);
//                $mod_name = $DB->get_record('modules', array('id'=>$mod_id->module));
////            print_object($mod_name->name);
//        
//                $note = $DB->get_record('notes', array('id'=>$this->id, 'userid'=> $USER->id), '*');
//            
////          $mform->addElement('html', '<a href="#" onmouseup="'.$note->noteurl.'" class=\"link_text\">'.$course_name->fullname . "►" . $mod_name->name .'</a>');
//                $mform->addElement('html', '<a href="'.$note->noteurl.'" target="_blank" >'.$course_name->fullname . "►" . $mod_name->name .'</a>');         
//            } else {
//        
//                $course_name = $DB->get_record('course', array('id'=>$course_id), '*');
//                $note = $DB->get_record('notes', array('id'=>$this->id, 'userid'=> $USER->id), '*');          
//
//                $mform->addElement('html', '<a href="#" onmouseup="loadinparent(\' '.$note->noteurl.' \', false)" >'.$course_name->fullname.'</a>');
//                echo$note->noteurl;
//            }
//        }
//        $mform->addElement('html', '</center>');
//        
//        $mform->addElement('html', '</br>');
             
        //Link back to Notes Gallery
//        $mform->addElement('html', '<div class="link2">');
//        $mform->addElement('html', '<center>');
//        
//        //$mform->addElement('html', '<a href="#" onmouseup="loadinparent(\' http://www.moock.org \', false)" >My Notes Gallery</a>');
//        
//        
//        $mform->addElement('html', '<a href="#" onmouseup="loadinparent(\' '.$CFG->wwwroot.'/local/makenote/view.php'.' \', false)" >My Notes Gallery</a>');
//         
//        $mform->addElement('html', '</center>');
           
        
        $mform->addElement('text', 'title', 'Note Title: ', array('maxlength' => 255, 'size'=>65));
        $mform->addRule('title', 'Please enter a title', 'required', 'client', false, false);
        $mform->addRule('title', 'Please enter numbers and/or letters only', 'nopunctuation', 'client', false, false);
        
        
        $mform->addElement('htmleditor', 'text', '', array('canUseHtmlEditor'=>'detect', 'rows'=>50, 'cols'=>50));
        $mform->setType('text', PARAM_RAW);


        $mform->addElement('hidden', 'id', $this->id);
        $mform->addElement('hidden', 'courseid', $this->course_id);
        $mform->addElement('hidden', 'url', $this->url);
        $mform->addElement('hidden', 'blockInstance', $this->blockInstance);
        $mform->addElement('hidden', 'cmid', $this->cmid);
        
        $mform->addElement('html', '<div class="qheader">');
        $mform->addElement('html', '<center>');
        $this->add_action_buttons(true, 'save');
        $mform->addElement('html', '</center>');
 
        //$mform->addElement('format', 'format', get_string('format'));
       
        //Checks if id is set
        //Retrieves record and loads the data from specified note
          if($this->id > 0){
            //get one note with this id
              //print_object(3);              
 
              $note = $DB->get_record('notes', array('id'=>$this->id, 'userid'=> $USER->id), '*');
                      //print_object($note);
              $mform->setDefault('title', $note->name);
              $mform->setDefault('text', $note->text);
        }
        
        
        
    }                           
}  
?>
