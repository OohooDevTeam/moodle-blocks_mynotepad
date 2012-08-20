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

require_once($CFG->libdir.'/formslib.php');

class notes_form extends moodleform {

    private $id;
    private $course_id;
    private $url;
    private $blockInstance;
    private $cmid;

    /**
     * Constructor
     *
     * @param int $id This number is for use in the constructor
     * @param int $course_id This number is for use in the constructor
     * @param string $return_url This string is for use in the constructor
     * @param int $blockInstance This number is for use in the constructor
     * @param int $cmid This number is for use in the constructor
     */
    function __construct($id, $course_id,$return_url,$blockInstance, $cmid){
        $this->id = $id;
        $this->course_id = $course_id;
        $this->url = $return_url;
        $this->blockInstance = $blockInstance;
        $this->cmid = $cmid;
        parent::__construct();
    }

    /**
     *
     * @global type $CFG
     * @global type $SESSION
     * @global type $PAGE
     * @global type $DB
     * @global type $USER
     */
    function definition() {
        //Need the id number
        global $CFG, $SESSION, $PAGE, $DB, $USER;

        $course_id = optional_param('courseid',0, PARAM_INT);

        if ($DB->record_exists('notes', array('id'=>$this->id))){
            $currpage1 = $DB->get_record('notes', array('id'=>$this->id));
            $currpage = $currpage1->noteurl;
            $url = $currpage;
        } else {
            $url = optional_param('url',$CFG->wwwroot.'/course/view.php?id='.$course_id,PARAM_URL);

        }
$path_parts = pathinfo($url);

$dirname = $path_parts['dirname'];
$basename = $path_parts['basename'];


//This code was commented out
/***************************************************************/
change_parent();

        $mform =& $this->_form; // Don't forget the underscore!

        //Link to page note was taken
        $mform->addElement('html', '<div class="link">');
        $mform->addElement('html', '<center>');

        //***Redirect the parent window to the course page the note was taken***//
        //Checks if you are in a mod page and creates the link in your note
        if ($this->id > 0){
            if ($pos1 = strpos($dirname, "mod")){
                $pos1 = strpos($basename, "id=");
                $pos1 = $pos1+3;
                $moduleid = substr("$basename", $pos1, strlen($basename));

                $course_name = $DB->get_record('course', array('id'=>$course_id), '*');
                $mod_id = $DB->get_record('course_modules', array('id'=>$moduleid, 'course'=>$course_id));
                $mod_name = $DB->get_record('modules', array('id'=>$mod_id->module));

                $note = $DB->get_record('notes', array('id'=>$this->id, 'userid'=> $USER->id), '*');

                $mform->addElement('html', '<a href="'.$note->noteurl.'" target="_blank" >'.$course_name->fullname . "►" . $mod_name->name .'</a>');
            } else {

                $course_name = $DB->get_record('course', array('id'=>$course_id), '*');
                $note = $DB->get_record('notes', array('id'=>$this->id, 'userid'=> $USER->id), '*');

                $mform->addElement('html', '<a href="#" onmouseup="loadinparent(\' '.$note->noteurl.' \', false)" >'.$course_name->fullname.'</a>');
            }
        }
        $mform->addElement('html', '</center>');

        $mform->addElement('html', '</br>');

/****************************************************/ //End of commented code
        $mform->addElement('text', 'title', 'Note Title: ', array('maxlength' => 255, 'size'=>65));
        $mform->addRule('title', get_string('blank_title_msg', 'block_mynotepad'), 'required', null, 'client');
        $mform->addRule('title', get_string('invalid_title_msg', 'block_mynotepad'), 'nopunctuation', 'required', null, 'client');

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

        //Checks if id is set
        //Retrieves record and loads the data from specified note
          if($this->id > 0){
            //get one note with this id
              $note = $DB->get_record('notes', array('id'=>$this->id, 'userid'=> $USER->id), '*');
              //Set the default title and content if the note already exists upon opening the note for editing
              $mform->setDefault('title', $note->name);
              $mform->setDefault('text', $note->text);
        }



    }
}
?>
