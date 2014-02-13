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
 * Strings for component 'qformat_qtex', language 'en'
 *
 * @package    qformat
 * @subpackage qtex
 * @author     Leopold Talirz
 * @copyright  2014 Project LEMUREN, ETH Zurich
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'QuestionTeX format';
$string['pluginname_help'] = 'QuestionTeX is a collection of LaTeX-macros that enables authors to create multiple-choice tests.';
$string['pluginname_link'] = 'qformat/qtex';


/////////////////////
// Used during import
$string['cannotopenzip'] = 'Can not open zip archive, error code: {$a}.';
$string['notexfound'] = 'Found no .tex file in zip archive.';
$string['macrosmissing'] = 'File with QuestionTeX macros is missing.';
$string['latexdistromissing'] = 'No LaTeX distribution found. Please set path to latex.exe in config.php.';
$string['tempdir'] = 'Problem with creation/deletion of temporary directory.';
$string['latexcompilation'] = 'Error during LaTeX compilation:\n{$a}';
$string['multipletex'] = 'Found multiple .tex files {$a} in zip archive. Do not know, which to translate.';
$string['unknownenvironment'] = 'Unknown LaTeX environment {$a}.';
$string['unknownfiletype'] = 'Unknown file extension: {$a}.';
$string['imagemissing'] = 'Image "{$a}" can not be found in the zip archive.';
$string['unsupportedimagetype'] = 'The image type of {$a} may not be supported for display in Moodle.';
$string['allimagesmissing'] = 'The .tex file includes images. If you upload a zip archive containing the images and the .tex file, the images will be embedded automatically (supported formats: png).';
$string['badpercentage'] = 'In question "{$a->question}" : The optional parameter {$a->fraction} contains bad characters.';
$string['changedpercentage'] = 'In question "{$a->question}" : The percentage {$a->original} is not allowed in Moodle and has been changed to {$a->new}.';
$string['noanswers'] = 'No answers found for question "{$a->question}".';
// Standard import category
$cfg['importcategory'] = 'QuestionTeX Import';

/////////////////////
// Used during export
$string['unknownexportformat'] = 'The export of format {$a} is not supported.';
$string['cannotreadimage'] = 'Can not read image from {$a}';
$string['questionsincludeimages'] = 'The selected questions include images. A zip archive is being created, containing the images as well as the .tex file.';
$string['embederror'] = 'Error while parsing embedded image';
$string['notreadable'] = 'The filepath {$a} could not be read and can thus not be included in export.';
// The preamble put into an exported quiz
$string['preamble'] = '% QuestionTeX 

% User Information
% ================
%   - This LaTeX source may be compiled by either latex or pdflatex.
%   - The compilation process requires a file "Questiontex_Macros.tex" to be
%     found in the same folder. It can be downloaded from
%       www.lemuren.math.ethz.ch/coursesupport/multiplechoice
%     where you can also find a more detailed documentation.
%   - Please do not make use of user-defined macros.
';
// Subfolder to store images in exported zip (may be left empty)
$string['imagefolder'] = 'images/';

////////////////
// Miscellaneous
$string['wrongidentifier'] = 'The identifier {$a} is unknown.';
$string['norenderenginefound'] = 'No text filter for math detected. Assuming standard TeX filter.';
$string['unknowngradingscheme'] = 'The specified grading scheme cannot be found.';
$string['configmissing'] = 'The configuration file config.php of the QuestionTeX format cannot be accessed.';
$string['gradingmissing'] = 'The file grading.php containing the grading schemes cannot be accessed.';