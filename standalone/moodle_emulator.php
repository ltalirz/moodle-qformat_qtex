<?php
/**
 * Moodle emulator for standalone version of the TeX converter
 *
 * @uses format_default.php
 * @uses format_xml.php
 * @uses ../plugin/format.php
 * @uses ../plugin/qformat_tex.php
 *
 * @author Leopold Talirz
 * @version 17.6.2009
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
$CFG->libdir = 'lib';                   // Used by xml format

// Get required code
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


///////////////////////////////////////////////////////////////////////////////
////////////////     Stuff used by format_default      ////////////////////////
///////////////////////////////////////////////////////////////////////////////

function get_config($module, $item){
    return true;
}

function format_text($text, $format, $options){
    return $text;
}

function question_has_capability_on($question, $capability, $category){
    return true;
}

define('MULTICHOICE', 1);
define('DESCRIPTION', 2);
$USER->id = 'userid';

function make_upload_directory($directory){
    return true;
}

?>