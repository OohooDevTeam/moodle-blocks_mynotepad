<!DOCTYPE html>
<?php

// OUTPUT file is part of Moodle - http://moodle.org/
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

/**
 * Internal library of functions for module makenote
 *
 * All the makenote specific functions, needed to implement the module
 * logic, should go here. Never include OUTPUT file from your lib.php!
 * 
 * @package   mod_makenote
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//OUTPUT is used to call lib.php, so that module can see all its available funcs
//require_once("$CFG->dirroot/local/makenote/lib.php");

defined('MOODLE_INTERNAL') || die();
//global $PAGE;

function reorderindex(array $source, $conditions_list = array())
{
    $i = 0;
    foreach ($source as $key => $val) {
        if ($key != $i) {
            unset($source[$key]);
            $source[$i] = $val;
        }       
        $i++;
    }
    
    foreach ($source as $key => $val) {
        foreach ($conditions_list as $var) {
            if ($val === $var) {
                unset($source[$key]);    
                $source = reorderindex($source, $conditions_list);
            }
        }
    }
    
    return $source;
}

//**********************************************************************************************************//
//**********************************************************************************************************//

function refresh_parent(){
    echo "<script language='javascript' type='text/javascript'>;

function loadinparent(url, closeSelf){
	self.opener.location = url;     
	}
</SCRIPT>      
";
    echo "</script>";
}


// Hide script from non-javascript browsers.
// Load Page Into Parent Window
// Version 1.0
// Last Updated: May 18, 2000
// Code maintained at: http://www.moock.org/webdesign/javascript/
// Copy permission granted any use provided this notice is unaltered.
// Written by Colin Moock.
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

function close_and_refresh(){
         echo "<script language='javascript' type='text/javascript'>;
opener.location.reload()
setTimeout('self.close();',0);

        ";
        echo "</script>";
}

function self_close(){
         echo "<script language='javascript' type='text/javascript'>;
setTimeout('self.close();',0);
        ";
        echo "</script>";
}

  function text_limit($text) {
    $chars = 13;
    $points = (strlen($text) > $chars) ? '...' : '';
    return (substr($text, 0, $chars)."".$points);
  }

//function changeheading($num, $i, $tabname){
////    print $num . $i .$tabname;
//    echo "<script type='text/javascript'>
//    function changetext$num$i(){
//	var userInput$num$i = document.getElementById('userInput$num$i').value;
//    var xmlhttp;
//    if (window.XMLHttpRequest)
//    {
//        xmlhttp=new XMLHttpRequest();
//    }
//    else
//    {
//        xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
//    }
//    xmlhttp.onreadystatechange=function()
//    {
//        if (xmlhttp.readyState==4 && xmlhttp.status==200)
//        {
//            document.getElementById('header$num$i').innerHTML = userInput$num$i;
//        }
//    }
//    xmlhttp.open('POST','save.php',true);
//    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
//    xmlhttp.send('userinput=' + userInput$num$i + '&colnum=$num' + '&tablenum=$i' +'&tabname=$tabname');   
//  
//}";
//echo "</script>";
//}

function autorefresh(){
    global $CFG;
    echo "<script type='text/javascript'>;
   window.location = '$CFG->wwwroot/local/makenote/view.php';  
</script>";
}

//use escape(content) to encode
function decode($url)
{
   echo "<script type='text/javascript'>;
        return unescape($url);
   }";
   echo"</script>"; 
    
}

function curPageURL() {
 $pageURL = 'http';
 //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function refresh($url){
    
    echo "<SCRIPT language=JavaScript>;

function win(){
window.opener.location.href='$url';
self.close();

}";
echo "</SCRIPT>";
}

function updatenote($id, $text, $title){
      global $DB,$CFG, $USER;

      $note = $DB->get_record('notes', array('id'=>$id, 'deleted'=>0, 'userid'=> $USER->id));
      $note->name = $title;
      $note->text = $text;
      
      //$note->noteurl = "$CFG->wwwroot./local/makenote/notes.php?id=$any";  
      
    $update = $DB->update_record('notes', $note);  
}

  function addnote($course_id, $text, $title, $blockInstance, $noteurl, $cmid) {
    global $USER, $DB, $SESSION, $CFG, $PAGE;
    
    /*
    $id = optional_param('id' ,0 ,PARAM_INT);  
    print_object($id);
    */

    $note = new stdClass();
    $note->userid = $USER->id;
    $note->text = $text;
    $note->name = $title;
    $note->location = $blockInstance;
    $note->noteurl = $noteurl;
    //date('(m/d/Y H:i:s)', $note->time_modified)
    $note->time_modified = date('(m/d/Y H:i:s)', time());
    $note->courseid = $course_id;
    $note->cmid = $cmid;
    //$note->noteurl = "$CFG->wwwroot./local/makenote/notes.php?id=$course_id";  
   
  //echo "<center><INPUT type='button' value='Close Window' onmouseup='window.close(\"resizable=yes,scrollbars=yes,width=50,height=50,left=0,top=0\")'></center>";

    //if(empty($note->text)) { return false;}
    return ($DB->insert_record('notes', $note));
    //return ($store);
  }

function jspopup(){
         echo "<script language='javascript' type='text/javascript'>;
        function newPopup(url) {
	newwindow=window.open(url,'name','height=600,width=750,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
	return false;
}

   function newPopup2(url) {
	newwindow=window.open(url,'name','height=600,width=750,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
	return false;
}

        ";
        echo "</script>";
}

function check_all(){

    echo "<script language='JavaScript'>

      checked = false;
      function checkedAll () {
        if (checked == false){
        checked = true
        } else {
        checked = false
        }
	for (var i = 0; i < document.getElementById('check').elements.length; i++) {
	  document.getElementById('check').elements[i].checked = checked;
	}
      }
";
echo "</script>";
}

function call_to_export(){

    echo "<script type='text/javascript'>
function export_option(str)
{
if (str=='')
  {
  document.getElementById('Show_option').innerHTML='';
  return;
  } 
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById('Show_option').innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open('GET','export.php?q='+str ,true);
xmlhttp.send();
}
";
echo "</script>";
    
}
  //xmlhttp.send('userinput=' + userInput$num$i + '&colnum=$num' + '&tablenum=$i' +'&tabname=$tabname');   

function check_button_clicked(){
    global $CFG;
    echo "<script type='text/javascript'>
    function checkData(id) {
       switch(id) {
          case 'delete':
           var a = 'delete';
             break;
          case 'restore':
           var a = 'restore';
             break;
        }
          window.location.href = '$CFG->wwwroot/local/makenote/recycle.php?test=' + a;
    }";
    echo "</script>";
        //window.location = '$CFG->wwwroot/local/makenote/view.php?' + content;}
}

function delete_notes(){
    global $DB, $USER;
        //Delete notes permeantly
    if(!empty($_POST['checkbox'])){

        for ($i=0; $i < count($_POST['checkbox']);$i++) {
               
            if ($DB->record_exists('gallerytable', array('noteid'=>$_POST['checkbox'][$i], 'userid'=> $USER->id))){
                
                $DB->delete_records('gallerytable', array('noteid'=>$_POST['checkbox'][$i], 'userid'=> $USER->id));
    
                $DB->delete_records('notes', array('id'=>$_POST['checkbox'][$i], 'userid'=> $USER->id));

            //For notes under the "New Notes" heading
            } else {      
                
                $DB->delete_records('notes', array('id'=>$_POST['checkbox'][$i], 'userid'=> $USER->id));
//                echo $_POST['checkbox'][$i] . ", ";

            }
    
        }
}
}

function restore_notes(){
    global $DB, $USER;
        //Restore notes
    if(!empty($_POST['checkbox'])){

        for ($i=0; $i < count($_POST['checkbox']);$i++) {
               
            if ($DB->record_exists('gallerytable', array('noteid'=>$_POST['checkbox'][$i], 'userid'=> $USER->id))){
                
                $update1 = $DB->get_record('gallerytable', array('noteid'=>$_POST['checkbox'][$i], 'userid'=> $USER->id));
                $update1->deleted = 0;
                $DB->update_record('gallerytable', $update1);
    
                $update2 = $DB->get_record('notes', array('id'=>$_POST['checkbox'][$i], 'userid'=> $USER->id));
                $update2->deleted = 0;
                $DB->update_record('notes', $update2);

            //For notes under the "New Notes" heading
            } else {      
                
                $update2 = $DB->get_record('notes', array('id'=>$_POST['checkbox'][$i], 'userid'=> $USER->id));
                $update2->deleted = 0;
                $DB->update_record('notes', $update2);

            }
        }

    }
}