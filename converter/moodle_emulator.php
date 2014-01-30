<?php
/**
 * Moodle emulator for standalone version of the TeX converter
 *
 * @uses lib/format_default.php
 * @uses lib/format_xml.php
 * @uses ../qtex/format.php
 * @uses ../qtex/qformat_tex.php
 *
 * @author Leopold Talirz
 * @version 26.11.2013
 */

///////////////////////////////////////////////////////////////////////////////
////////////////            Configuration              ////////////////////////
///////////////////////////////////////////////////////////////////////////////

$path_to_plugin = '../qtex/';
$path_to_qtex_class = $path_to_plugin.'format.php';
$path_to_qtex_strings = $path_to_plugin.'lang/en/qformat_qtex.php';

$CFG = new stdClass;
$CFG->libdir = 'lib';                   // Used by xml format
$CFG->dataroot = '.';

const FORMAT_MOODLE = 'FORMAT_MOODLE';
const FORMAT_HTML = 'FORMAT_HTML';
const FORMAT_WIKI = 'FORMAT_WIKI';
const FORMAT_MARKDOWN = 'FORMAT_MARKDOWN';
const FORMAT_PLAIN = 'FORMAT_PLAIN';
const RANDOM = 'RANDOM';
const PARAM_TEXT = 'PARAM_TEXT';

///////////////////////////////////////////////////////////////////////////////
////////////////     End of configuration              ////////////////////////
///////////////////////////////////////////////////////////////////////////////

// Set some variables
define('TEXFORMAT_STANDALONE', true);   // Triggers standalone mode
define('MOODLE_INTERNAL', true);              // New in Moodle 2.?

$OUTPUT = new core_renderer();

// Get required code
//require_once('lib/setuplib.php');     // First get help strings
//require_once('lib/moodlelib.php');     // First get help strings
require_once($CFG->libdir . '/text.php');
require_once($path_to_qtex_strings);     // First get help strings
require_once($CFG->libdir . '/format_default.php');   // Then base class for formats
require_once($CFG->libdir . '/format_xml.php');       // Moodle XML format...
require_once($path_to_qtex_class);       // and finally the qtex format



///////////////////////////////////////////////////////////////////////////////
////   Emulated Moodle functions used by qformat_qtex + helper functions  /////
///////////////////////////////////////////////////////////////////////////////

// Only knows strings specially made for the tex format
function get_string($identifier, $module = '', $a = ''){
    global $string;

    if($module == 'qformat_qtex'){
        $message = $string[$identifier];
        // TODO: handle cases, when $a is not simply a string, but an object
        if ($a != '') $message = preg_replace('/\{\$a\}/s', $a, $message);

        return $message;
    }
    else{
        return "Identifier $identifier, Module $module\n";
    }
}

class core_renderer {
    // Used to notify, mostly about irregularities, but not only.
    // Turning it on can provide insight into process of import (but will also
    // cause import to fail, since this causes headers to be sent).
    function notification($string){
        global $CFG;
    
        if($CFG->notify) return "<p><b>Note:</b> $string</p>";
        else return "";
    }   
}


// Prints errors
function print_error($identifier, $module, $c){
    error($c, $identifier, $module);
}

// Prints error without dying.
function error($string, $identifier, $module){
    global $CFG;

    if($CFG->notify) echo "<p><b>Error:</b> $string (identifier: $identifier, module: $module)</p>";
}

// Normally returns an object containing grades...
function get_grade_options(){
    return true;
}

// But is only used here, where we now simply return the same grade
function match_grade_options($options, $fraction, $mode){
    return $fraction;
}

// Used to find out which TeX filter is active
function get_list_of_plugins($d){
    global $CFG;
    if ($d == 'filter') return $CFG->textfilters;
    else echo "Error: get_list_of_plugins called with unfamiliar parameter $d";
}


/**
 * Extending qformat_qtex class, overwriting and 'unprotecting' some functions.
 *
 * @see ../qtex/format_xml.php
 */
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
        //if (! $export_dir = make_upload_directory($this->question_get_export_dir())) {
        //    error( get_string('cannotcreatepath','quiz',$export_dir) );
        //}
        //$path = $CFG->dataroot.'/'.$this->question_get_export_dir();
        //$path = $CFG->dataroot.'/export';

        // get the questions (from database) in this category
        // only get q's with no parents (no cloze subquestions specifically)
        if ($this->category){
            $questions = get_questions_category( $this->category, true );
        } else {
            $questions = $this->questions;
        }

        // CHANGE: Do not notify in Moodle emulator
        // $CFG->notify( get_string('exportingquestions','quiz'));
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
            if (question_has_capability_on($question, 'view', (is_object($question) && property_exists($question, 'category')) ? $question->category : '')){
                $expout .= $this->writequestion( $question ) . "\n";
            }
        }

        // continue path for following error checks
        $course = $this->course;
        $continuepath = '';//"$CFG->wwwroot/question/export.php?courseid=$course->id";

        // did we actually process anything
        if ($count==0) {
            print_error( 'noquestions','quiz',$continuepath );
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );

        // write file
        //$filepath = $path."/".$this->filename . $this->export_file_extension();

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

    /**
     * Imports image into $this->images
     *
     * @param array $files of $file objects with functions
     *          get_filename(), get_content()
     */
    function writeimages($files = NULL, $encoding = 'base64'){
        if(isset($files)){
            foreach ($files as $file) {
                $includename = get_string('imagefolder', 'qformat_qtex').$file['name'];
                $this->images[$includename] = base64_decode($file['content']);
            }
        }

    }

    protected function writequestion($question){
        global $OUTPUT;
        $identifier = $this->get_identifier($question->qtype);

        // If our get_identifier function knows the question type
        if($identifier){

            // Call the right specialized function to get the content of the
            // tex environment.
            $exportfunction = 'export_'.$identifier;
            $content = $this->{$exportfunction}($question);

            /*
            // Keep track of non-embedded images (DEPRECATED)
            if(!empty($question->image)){
                // Get file name
                preg_match('/(.*?)\//', strrev($question->image), $imagenamesrev);

                $image['includename'] = get_string('imagefolder', 'qformat_qtex').strrev($imagenamesrev[1]);
                $image['filepath'] = $question->image;
                $this->images[$image['includename']] = $image;

                // Add image in front of content
                $imagetag = $this->create_macro('image', array($image['includename']));
                $content = $imagetag.$content;

                unset($image);
            }
            */

            $fs = get_file_storage();
            $contextid = $question->contextid;
            // Get files used by the questiontext.
            $question->questiontextfiles = $fs->get_area_files(
                                                               $contextid, 'question', 'questiontext', (is_object($question) && property_exists($question, 'questiontextitemid')) ? $question->questiontextitemid : '');
            $this->writeimages($question->questiontextfiles);

            // Get files used by the generalfeedback.
            $question->generalfeedbackfiles = $fs->get_area_files(
                                                                  $contextid, 'question', 'generalfeedback', (is_object($question) && property_exists($question, 'generalfeedbackitemid')) ? $question->generalfeedbackitemid : '');
            if (!empty($question->options->answers)) {
                foreach ($question->options->answers as $answer) {
                    $answer->answerfiles = $fs->get_area_files(
                                                               $contextid, 'question', 'answer', $answer->answeritemid);
                    $this->writeimages($answer->answerfiles);
                    $answer->feedbackfiles = $fs->get_area_files(
                                                                 $contextid, 'question', 'answerfeedback', $answer->feedbackitemid);
                    $this->writeimages($answer->feedbackfiles);
                }
            }

        }
        // Else we don't know the type
        else{
            echo $OUTPUT->notification(get_string('unknownexportformat', 'qformat_qtex', $question->qtype));
        }

        return $content;
    }
}

/**
 * Processes questions from readquestions() such that they can be passed to
 * writequestions().
 *
 * @param array $questions An array of imported questions
 * @return array Processed array, ready to be handled by writequestions()
 */
function process_for_export($questions){
    foreach($questions as $question){
        if($question->qtype == 'multichoice') $question = rearrange_multichoice($question);
    }

    return $questions;
}

/**
 * Rearranges a multiple choice question from readquestions() such that it can
 * be passed to writequestions().
 *
 * @param object $questions An imported question
 * @return object Processed question, ready to be handled by writequestions()
 * @see http://docs.moodle.org/dev/Question_data_structures
 */
function rearrange_multichoice($question){
    // We go from a structure
    // $question->answer, $question->feedback, $question->fraction   to
    // $question->options->answers, where each answer object has
    // $answer->answer, $answer->fraction, $answer->feedback

    // Handling answer text and fraction
    foreach($question->answer as $i => $answer){
        $answers[$i] = new stdClass();

        if(is_array($answer)){
            $answers[$i]->answer = $answer['text'];
            $answers[$i]->answerfiles = isset($answer['files']) ? $answer['files'] : array();
            $answers[$i]->answerformat = $answer['format'];
            $answers[$i]->answeritemid = isset($answer['itemid']) ? $answer['itemid'] : '';
            $answers[$i]->id = $answer['id'];
        } else {
            $answers[$i]->answer = $answer;
        }
        $answers[$i]->fraction = $question->fraction[$i];
    }

    // Handling answer feedbacks
    foreach($question->feedback as $i => $feedback){
        if(is_array($feedback)){
            $answers[$i]->feedback = $feedback['text'];
            $answers[$i]->feedbackfiles = isset($feedback['files']) ? $feedback['files'] : array();
            $answers[$i]->feedbackformat = $feedback['format'];
            $answers[$i]->feedbackitemid = isset($feedback['itemid']) ? $feedback['itemid'] : '';
        } else {
            $answers[$i]->feedback = $feedback;
        }
    }

    $question->options = new stdClass();
    $question->options->answers = $answers;
    $question->options->correctfeedback = '';
    $question->options->correctfeedbackformat = '';
    $question->options->incorrectfeedback = '';
    $question->options->incorrectfeedbackformat = '';
    $question->options->partiallycorrectfeedback = '';
    $question->options->partiallycorrectfeedbackformat = '';
    unset($question->answer);
    
    // Multichoice has a few more options...
    $question->options->single = $question->single;
    unset($question->single);
    $question->options->answernumbering = $question->answernumbering;
    unset($question->answernumbering);
    $question->options->shuffleanswers = $question->shuffleanswers;
    unset($question->shuffleanswers);
    
    return $question;
}

// /**
//  * Does some postprocessing that could also be done directly after import,
//  * however with more difficulty (maybe by serialization)
//  *
//  * @param string $string A text generated by writequestion()
//  * @return string The processed string.
//  */
// function postprocess($string){
//      return stripslashes($string);
// }

///////////////////////////////////////////////////////////////////////////////
///////////    Emulated Moodle functions used by qformat_xml   ///////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Extending qformat_xml class, overwriting some functions.
 *
 * @see format_xml.php
 */
class qformat_xml_emulator extends qformat_xml {
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
     * Generte the XML to represent some files.
     * @param array of store array of stored_file objects.
     * @return string $string the XML.
     */
    function write_files($files) {
        if (empty($files)) {
            return '';
        }
        $string = '';
        foreach ($files as $file) {
            $string .= '<file name="' . $file->name . '" encoding="base64">';
            $string .= $file->content;
            $string .= '</file>';
        }
        return $string;
    }

    /**
     * Need to stop this function from inserting stuff into database etc.
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

        $count = 0;
        $expout = '';
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
        
        // did we actually process anything
        if ($count==0) {
            print_error('noquestions', 'question', $continuepath);
        }
        
        // final pre-process on exported data
        $expout = $this->presave_process($expout);
        return $expout;
    }

}

function debugging() {
}

$fs = new file_storage;

// Used by format_xml
function get_file_storage() {
    global $fs;

    return $fs;
}

$draft_itemid = 1;

function file_get_unused_draft_itemid() {
    global $draft_itemid;
    return $draft_itemid++;
}



class file_storage {
    public $questions;
    private $createdFiles;

    function __construct() {
        $this->questions = array();
        $this->createdFiles = array();
    }

    function get_area_files($contextid, $mod, $area, $itemid) {
        if (!$itemid) {
            return array();
        }

        if (!is_array($itemid) && isset($this->createdFiles[$itemid])) {
            return $this->createdFiles[$itemid];
        } else {
            $questionId = 0;
            $answerId = 0;
            if (is_array($itemid)) {
                $questionId = $itemid[0];
                $answerId = $itemid[1];
            } else {
                $questionId = $itemid;
            }

            $question = $this->questions[$questionId];
            if ($area === 'questiontext') {
                if (is_object($question) &&
                        property_exists($question, 'questiontextfiles')) {
                    return $question->questiontextfiles;
                }
            } else if ($area === 'generalfeedback') {
                if (is_object($question) &&
                        property_exists($question, 'generalfeedbackfiles')) {
                    return $question->generalfeedbackfiles;
                }
            } else if ($area === 'answer') {
                $answer = $question->options->answers[$answerId];
                return $answer->answerfiles;
            } else if ($area === 'answerfeedback') {
                $feedback = $question->options->answers[$answerId];
                return $feedback->feedbackfiles;
            }
            return array();
        }
    }

    function create_file_from_string($filerecord, $str) {
        $itemid = $filerecord['itemid'];
        if (!isset($this->createdFiles[$itemid])) {
            $this->createdFiles[$itemid] = array();
        }
        $this->createdFiles[$itemid][] =
            array(
                  'name' => $filerecord['filename'],
                  'encoding' => 'base64',
                  'content' => base64_encode($str));
    }
}

///////////////////////////////////////////////////////////////////////////////
////////    Emulated Moodle functions used by qformat_default   ///////////////
///////////////////////////////////////////////////////////////////////////////

function clean_param($param, $type) {
    return $param;
}

function get_config($module, $item){
    return true;
}

function format_text($text, $format, $options){
    return $text;
}

function question_has_capability_on($question, $capability, $category){
    return true;
}

$USER = new stdClass();
$USER->id = 'userid';

class context_user {
    public static function instance($instanceid, $strictness = 0) {
        $USER = new stdClass();
        $USER->id = 'userid';
        return $USER;
    }
}

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

/**
 * Given HTML text, make it into plain text using external function
 *
 * @param string $html The text to be converted.
 * @param integer $width Width to wrap the text at. (optional, default 75 which
 *      is a good value for email. 0 means do not limit line length.)
 * @param boolean $dolinks By default, any links in the HTML are collected, and
 *      printed as a list at the end of the HTML. If you don't want that, set this
 *      argument to false.
 * @return string plain text equivalent of the HTML.
 */
function html_to_text($html, $width = 75, $dolinks = true) {

    global $CFG;

    require_once($CFG->libdir .'/html2text.php');

    $h2t = new html2text($html, false, $dolinks, $width);
    $result = $h2t->get_text();

    return $result;
}

/**
 * Useful functions for writing question types and behaviours.
 *
 * @copyright 2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_utils {
    /**
     * Tests to see whether two arrays have the same keys, with the same values
     * (as compared by ===) for each key. However, the order of the arrays does
     * not have to be the same.
     * @param array $array1 the first array.
     * @param array $array2 the second array.
     * @return bool whether the two arrays have the same keys with the same
     *      corresponding values.
     */
    public static function arrays_have_same_keys_and_values(array $array1, array $array2) {
        if (count($array1) != count($array2)) {
            return false;
        }
        foreach ($array1 as $key => $value1) {
            if (!array_key_exists($key, $array2)) {
                return false;
            }
            if (((string) $value1) !== ((string) $array2[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Tests to see whether two arrays have the same value at a particular key.
     * This method will return true if:
     * 1. Neither array contains the key; or
     * 2. Both arrays contain the key, and the corresponding values compare
     *      identical when cast to strings and compared with ===.
     * @param array $array1 the first array.
     * @param array $array2 the second array.
     * @param string $key an array key.
     * @return bool whether the two arrays have the same value (or lack of
     *      one) for a given key.
     */
    public static function arrays_same_at_key(array $array1, array $array2, $key) {
        if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
            return ((string) $array1[$key]) === ((string) $array2[$key]);
        }
        if (!array_key_exists($key, $array1) && !array_key_exists($key, $array2)) {
            return true;
        }
        return false;
    }

    /**
     * Tests to see whether two arrays have the same value at a particular key.
     * Missing values are replaced by '', and then the values are cast to
     * strings and compared with ===.
     * @param array $array1 the first array.
     * @param array $array2 the second array.
     * @param string $key an array key.
     * @return bool whether the two arrays have the same value (or lack of
     *      one) for a given key.
     */
    public static function arrays_same_at_key_missing_is_blank(
            array $array1, array $array2, $key) {
        if (array_key_exists($key, $array1)) {
            $value1 = $array1[$key];
        } else {
            $value1 = '';
        }
        if (array_key_exists($key, $array2)) {
            $value2 = $array2[$key];
        } else {
            $value2 = '';
        }
        return ((string) $value1) === ((string) $value2);
    }

    /**
     * Tests to see whether two arrays have the same value at a particular key.
     * Missing values are replaced by 0, and then the values are cast to
     * integers and compared with ===.
     * @param array $array1 the first array.
     * @param array $array2 the second array.
     * @param string $key an array key.
     * @return bool whether the two arrays have the same value (or lack of
     *      one) for a given key.
     */
    public static function arrays_same_at_key_integer(
            array $array1, array $array2, $key) {
        if (array_key_exists($key, $array1)) {
            $value1 = (int) $array1[$key];
        } else {
            $value1 = 0;
        }
        if (array_key_exists($key, $array2)) {
            $value2 = (int) $array2[$key];
        } else {
            $value2 = 0;
        }
        return $value1 === $value2;
    }

    private static $units     = array('', 'i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix');
    private static $tens      = array('', 'x', 'xx', 'xxx', 'xl', 'l', 'lx', 'lxx', 'lxxx', 'xc');
    private static $hundreds  = array('', 'c', 'cc', 'ccc', 'cd', 'd', 'dc', 'dcc', 'dccc', 'cm');
    private static $thousands = array('', 'm', 'mm', 'mmm');

    /**
     * Convert an integer to roman numerals.
     * @param int $number an integer between 1 and 3999 inclusive. Anything else
     *      will throw an exception.
     * @return string the number converted to lower case roman numerals.
     */
    public static function int_to_roman($number) {
        if (!is_integer($number) || $number < 1 || $number > 3999) {
            throw new coding_exception('Only integers between 0 and 3999 can be ' .
                    'converted to roman numerals.', $number);
        }

        return self::$thousands[$number / 1000 % 10] . self::$hundreds[$number / 100 % 10] .
                self::$tens[$number / 10 % 10] . self::$units[$number % 10];
    }

    /**
     * Typically, $mark will have come from optional_param($name, null, PARAM_RAW_TRIMMED).
     * This method copes with:
     *  - keeping null or '' input unchanged.
     *  - nubmers that were typed as either 1.00 or 1,00 form.
     *
     * @param string|null $mark raw use input of a mark.
     * @return float|string|null cleaned mark as a float if possible. Otherwise '' or null.
     */
    public static function clean_param_mark($mark) {
        if ($mark === '' || is_null($mark)) {
            return $mark;
        }

        return clean_param(str_replace(',', '.', $mark), PARAM_FLOAT);
    }

    /**
     * Get a sumitted variable (from the GET or POST data) that is a mark.
     * @param string $parname the submitted variable name.
     * @return float|string|null cleaned mark as a float if possible. Otherwise '' or null.
     */
    public static function optional_param_mark($parname) {
        return self::clean_param_mark(
                optional_param($parname, null, PARAM_RAW_TRIMMED));
    }

    /**
     * Convert part of some question content to plain text.
     * @param string $text the text.
     * @param int $format the text format.
     * @param array $options formatting options. Passed to {@link format_text}.
     * @return float|string|null cleaned mark as a float if possible. Otherwise '' or null.
     */
    public static function to_plain_text($text, $format, $options = array('noclean' => 'true')) {
        // The following call to html_to_text uses the option that strips out
        // all URLs, but format_text complains if it finds @@PLUGINFILE@@ tokens.
        // So, we need to replace @@PLUGINFILE@@ with a real URL, but it doesn't
        // matter what. We use http://example.com/.
        $text = str_replace('@@PLUGINFILE@@/', 'http://example.com/', $text);
        return html_to_text(format_text($text, $format, $options), 0, false);
    }
}

?>
