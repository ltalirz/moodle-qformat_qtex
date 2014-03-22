<?php
// This file is part of Moodle - http://moodle.org/
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
 * Configuration file.
 *
 * @package    qformat
 * @subpackage qtex
 * @author     Leopold Talirz
 * @copyright  2014 Project LEMUREN, ETH Zurich
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/*
 * Definition of tex commands (simple macros and environments).
 *
 * Remarks:
 *   -The array keys must stay unaltered. However, macros may be added/
 *   deleted/changed here without further need to change the code.
 *   -The first macro in the list will be used for export to LaTeX,
 *   so it should be the preferred one.
 */
$cfg['ENVIRONMENTS'] = array(
        'multichoice' => array('question', 'begin{multichoice}', 'begin{question}'),
		'singlechoice' => array('questionSc'),
        'description' => array('intro', 'keepme', 'remark')
);
$cfg['MACROS'] = array(
// Meta macros
        'title' => array('quiztitle', 'section*', 'category'),
// Multichoice macros
        'true' => array('true', 'correctanswer'),
        'false' => array('false', 'incorrectanswer'),
        'explanation' => array('explanation', 'generalfeedback'),
        'feedback' => array('feedback'),
        'shuffleanswers' => array('shuffleanswers'),
        'multianswer' => array('multianswer', 'singleanswer{false}'),
// Misc
        'image' => array('includegraphics', 'image')
);

// Special file names
$cfg['MACRO_FILENAME'] = 'MoodleQuiz_Macros.tex';
$cfg['CORPORATE_FILENAME'] = 'MoodleQuiz_Corporate.tex';
$cfg['QUIZ_FILENAME'] = 'MoodleQuiz.tex';
// OSX creates an additional folder that may contain copies of tex files.
// The content of this folder should be ignored.
$cfg['OSX_FOLDER'] = '__MACOSX';
$cfg['RESERVED_NAMES'] = array($cfg['MACRO_FILENAME'],
$cfg['CORPORATE_FILENAME'], $cfg['OSX_FOLDER']);

// Maximum length for imported question names in Moodle
$cfg['QNAME_LENGTH'] = 50;

// Allowed image formats (by file extension)
$cfg['IMAGE_FORMATS'] = array('png', 'jpg', 'gif');

// Line break in exported LaTeX text
$cfg['NL'] = "\r\n";

// Maximum size of MySql text fields in bytes
$cfg['MYSQL_TEXT_SIZE'] = 65535;

// Whether to get the question category from a 'title' environment
$cfg['CATEGORY_FROM_TITLE'] = false;
$cfg['DEFAULT_CATEGORY'] = 'QuestionTeX import';

/////////////////////
//// Currently unused
/////////////////////
// Macro file (is read into $cfg when needed)
$cfg['MACRO_FILE'] = '';

// Use Latex Distro to produce .lem file from .tex?
$cfg['GO_VIA_LEM'] = false;

// Path to latex.exe from a LaTeX distribution (read from MimeTeX, if present)
$cfg['PATH_TO_LATEX'] = '';

///////////////////////////////////////////////////////////////////////////
//////////////      End of configuration         /// //////////////////////
///////////////////////////////////////////////////////////////////////////

return true;
?>
