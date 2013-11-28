<?php
/**
 * Moodle emulator for standalone version of the TeX converter
 *
 * @uses format_default.php
 * @uses format_xml.php
 * @uses ../qtex/format.php
 * @uses ../qtex/qformat_tex.php
 *
 * @author Leopold Talirz
 * @version 26.11.2013
 */

///////////////////////////////////////////////////////////////////////////////
////////////////            Configuration              ////////////////////////
///////////////////////////////////////////////////////////////////////////////

$path_to_default_class = 'format_default.php';
$path_to_xml_class = 'format_xml.php';
$path_to_plugin = '../qtex/';
$path_to_qtex_class = $path_to_plugin.'format.php';
$path_to_qformat_qtex = $path_to_plugin.'lang/en/qformat_qtex.php';

///////////////////////////////////////////////////////////////////////////////
////////////////     End of configuration              ////////////////////////
///////////////////////////////////////////////////////////////////////////////

// Set some variables
define('TEXFORMAT_STANDALONE', true);   // Triggers standalone mode
define('MOODLE_INTERNAL', true);              // New in Moodle 2.?
$CFG = new stdClass;
$CFG->libdir = 'lib';                   // Used by xml format

// Get required code
//require_once('lib/setuplib.php');     // First get help strings
//require_once('lib/moodlelib.php');     // First get help strings
require_once($path_to_qformat_qtex);     // First get help strings
require_once($path_to_default_class);   // Then base class for formats
require_once($path_to_qtex_class);       // Then the real tex...
require_once($path_to_xml_class);       // and xml converters.


///////////////////////////////////////////////////////////////////////////////
////////////////     Emulated Moodle functions         ////////////////////////
///////////////////////////////////////////////////////////////////////////////

// Only knows strings specially made for the tex format
function get_string($identifier, $module = '', $a = ''){
    global $string;

    if($module == 'qformat_qtex'){
        $message = $string[$identifier];
        // TODO: handle cases, when $a is set (string + object)

        return $message;
    }
    else{
        return "Identifier $identifier, Module $module\n";
    }
}

// Used to notify, mostly about irregularities, but not only.
// Turning it on can provide insight into process of import (but will also
// cause import to fail, since nothing may be "echoed").
function notify($string){
    global $CFG;

    if($CFG->notify) echo "\n<b>Warning:</b> $string\n";
}

// Prints errors
function print_error($identifier, $module, $c){
    error($c, $identifier);
}

// Prints error without dying.
function error($string, $identifier, $module){
   global $CFG;

   if($CFG->notify) echo "Error: $string with identifier $identifier in module $module";
}



// Normally returns an object containing grades...
function get_grade_options(){
    return true;
}

// But is only used here, where we now simply return the same grade
function match_grade_options($options, $fraction, $mode){
    return $fraction;
}

function get_list_of_plugins($d){
	if ($d == 'filter') return array('tex');
	else echo "Error: get_list_of_plugins called with unfamiliar parameter $d";
}


class qformat_xml_emulator extends qformat_xml {
	/**
	 * Need to keep this function from inserting into DB etc.
	 *
	 * @param array $questions Array of questions to export
	 * @return string $expout Output
	 */
	function exportprocess(){
		// get the questions (from database) in this category
		// only get q's with no parents (no cloze subquestions specifically)
		if ($this->category){
			$questions = get_questions_category( $this->category, true );
		} else {
			$questions = $this->questions;
		}
		
		foreach($questions as $question) {
	
			// do not export hidden questions
			if (!empty($question->hidden)) {
				continue;
			}
	
			// do not export random questions
			if ($question->qtype==RANDOM) {
				continue;
			}
	
			// check if we need to record category change
			if ($this->cattofile) {
				if ($question->category != $trackcategory) {
					$trackcategory = $question->category;
					$categoryname = $this->get_category_path($trackcategory, '/', $this->contexttofile);
	
					// create 'dummy' question for category export
					$dummyquestion = new object;
					$dummyquestion->qtype = 'category';
					$dummyquestion->category = $categoryname;
					$dummyquestion->name = "switch category to $categoryname";
					$dummyquestion->id = 0;
					$dummyquestion->questiontextformat = '';
					$expout .= $this->writequestion( $dummyquestion ) . "\n";
				}
			}
			// export the question displaying message
			$count++;
	
			$expout .= $this->writequestion( $question ) . "\n";
		}
	
		return $expout;
	}
	
}


// readdata and readquestions are now protected
// We need to remove this protection in order to be able to call them
class qformat_qtex_emulator extends qformat_qtex{
	
	/**
	 * readdata of base class is protected!
	 * @see qformat_default::readdata()
	 */
	function readdata($filename){
		return parent::readdata($filename);		
	}
	
	/**
	 * readquestions of base class is protected!
	 * @see qformat_default::readquestions()
	 */
	function readquestions($lines){
		return parent::readquestions($lines);
	}
	
	
	/**
	 * Does the export processing.
	 *
	 * Normally, the default function is not meant to be overwritten, however I
	 * cannot pass zip files otherwise.
	 * Changes are highlighted by // CHANGE:
	 *
	 * @uses the main code of the original exportprocess() function from file
	 *       'format_default.php'
	 * @return boolean success
	 */
	function exportprocess(){
		global $CFG;

		// create a directory for the exports (if not already existing)
		if (! $export_dir = make_upload_directory($this->question_get_export_dir())) {
			error( get_string('cannotcreatepath','quiz',$export_dir) );
		}
		$path = $CFG->dataroot.'/'.$this->question_get_export_dir();

		// get the questions (from database) in this category
		// only get q's with no parents (no cloze subquestions specifically)
		if ($this->category){
			$questions = get_questions_category( $this->category, true );
		} else {
			$questions = $this->questions;
		}

		// CHANGE: Do not notify in Moodle emulator
		if(!($this->standalone)) notify( get_string('exportingquestions','quiz'));
		$count = 0;

		// results are first written into string (and then to a file)
		// so create/initialize the string here
		$expout = "";

		// track which category questions are in
		// if it changes we will record the category change in the output
		// file if selected. 0 means that it will get printed before the 1st question
		$trackcategory = 0;

		// iterate through questions
		foreach($questions as $question) {

			// do not export hidden questions
			if (!empty($question->hidden)) {
				continue;
			}

			// do not export random questions
			if ($question->qtype==RANDOM) {
				continue;
			}

			// check if we need to record category change
			if ($this->cattofile) {
				if ($question->category != $trackcategory) {
					$trackcategory = $question->category;
					$categoryname = $this->get_category_path($trackcategory, '/', $this->contexttofile);

					// create 'dummy' question for category export
					$dummyquestion = new object;
					$dummyquestion->qtype = 'category';
					$dummyquestion->category = $categoryname;
					$dummyquestion->name = "switch category to $categoryname";
					$dummyquestion->id = 0;
					$dummyquestion->questiontextformat = '';
					$expout .= $this->writequestion( $dummyquestion ) . "\n";
				}
			}

			// export the question displaying message
			$count++;
			// CHANGE: No echo in stand alone
			if(!$this->standalone) echo "<hr /><p><b>$count</b>. ".$this->format_question_text($question)."</p>";
			if (question_has_capability_on($question, 'view', $question->category)){
				$expout .= $this->writequestion( $question ) . "\n";
			}
		}

		// continue path for following error checks
		$course = $this->course;
		$continuepath = "$CFG->wwwroot/question/export.php?courseid=$course->id";

		// did we actually process anything
		if ($count==0) {
			print_error( 'noquestions','quiz',$continuepath );
		}

		// final pre-process on exported data
		$expout = $this->presave_process( $expout );

		// write file
		$filepath = $path."/".$this->filename . $this->export_file_extension();

		// CHANGE: In standalone mode, we simply return $expout
		if($this->standalone) return $expout;
		else{
			if (!$fh=fopen($filepath,"w")) {
				print_error( 'cannotopen','quiz',$continuepath,$filepath );
			}
			if (!fwrite($fh, $expout, strlen($expout) )) {
				print_error( 'cannotwrite','quiz',$continuepath,$filepath );
			}
			fclose($fh);
		}

		return true;
	}
	
 }
 
 function get_file_storage(){
 	return new file_storage;
 }
 
 class file_storage {
 	function get_area_files($a,$b,$c,$d){
 		return NULL;
 	}
 }
 

///////////////////////////////////////////////////////////////////////////////
////////////////     Stuff used by format_default      ////////////////////////
///////////////////////////////////////////////////////////////////////////////

function get_config($module, $item){
    return true;
}

function format_text($text, $format, $options){
    return $text;
}

//function question_has_capability_on($question, $capability, $category){
//    return true;
//}

$USER = new stdClass();
$USER->id = 'userid';


//function make_upload_directory($directory){
//    return true;
//}

///////////////////////////////////////////////////////////////////////////////
////////////////     Stuff used by lib/xmlize          ////////////////////////
///////////////////////////////////////////////////////////////////////////////
class moodle_exception extends Exception {

	public $errorcode;
	public $module;
	public $a;
	public $link;
	public $debuginfo;

	function __construct($errorcode, $module='', $link='', $a=NULL, $debuginfo=null) {
		if (empty($module) || $module == 'moodle' || $module == 'core') {
			$module = 'error';
		}

		$this->errorcode = $errorcode;
		$this->module    = $module;
		$this->link      = $link;
		$this->a         = $a;
		$this->debuginfo = is_null($debuginfo) ? null : (string)$debuginfo;

		if (get_string_manager()->string_exists($errorcode, $module)) {
			$message = get_string($errorcode, $module, $a);
			$haserrorstring = true;
		} else {
			$message = $module . '/' . $errorcode;
			$haserrorstring = false;
		}

		if (defined('PHPUNIT_TEST') and PHPUNIT_TEST and $debuginfo) {
			$message = "$message ($debuginfo)";
		}

		if (!$haserrorstring and defined('PHPUNIT_TEST') and PHPUNIT_TEST) {
			// Append the contents of $a to $debuginfo so helpful information isn't lost.
			// This emulates what {@link get_exception_info()} does. Unfortunately that
			// function is not used by phpunit.
			$message .= PHP_EOL.'$a contents: '.print_r($a, true);
		}

		parent::__construct($message, 0);
	}
}



?>
