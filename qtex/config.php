<?php
///////////////////////////////////////////////////////////////////////////
/////////////////////     Configuration        ////////////////////////////
///////////////////////////////////////////////////////////////////////////

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
        'description' => array('remark')
);
$cfg['MACROS'] = array(
// Meta macros
        'title' => array('intro', 'quiztitle', 'section*', 'category'),
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

// Default weight of wrong answer (must be in [-1,1])
$cfg['DEFAULT_WRONG_WEIGHT_MULTICHOICE'] = -1;
$cfg['DEFAULT_WRONG_WEIGHT_SINGLECHOICE'] = 0;
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

// Macro file (is read into $cfg when needed)
$cfg['MACRO_FILE'] = '';

// Use Latex Distro to produce .lem file from .tex?
$cfg['GO_VIA_LEM'] = false;

// Path to latex.exe from a LaTeX distribution (read from MimeTeX, if present)
$cfg['PATH_TO_LATEX'] = '';

// Whether to get the question category from a 'title' environment
$cfg['CATEGORY_FROM_TITLE'] = false;
$cfg['DEFAULT_CATEGORY'] = 'QuestionTeX import';

/**
 * Default grading scheme.
 * 
 * Punish guessing in multi-choice questions,
 * but not in single-choice ones.
 * If several answers are correct, each get the same
 * fraction. 
 */
class DefaultGradingScheme {
	/**
	 * @param object $qobject Question object imported from QuestionTeX.
	 *   $qobject->fraction[$i] holds:
	 *     - a proper fraction, if it was specified in the TeX source
	 *     - 'FRACTION_TRUE', if the answer was true
	 *     - 'FRACTION_FALSE', if the answer was false.  
	 * @return $qobject with updated grades
	 */
	function grade($qobject) {
		// one point per question
		$qobject->defaultmark= 1;
			
		$truecount = 0;
		$predefcount = 0;
		foreach($qobject->fraction as $i => $fraction){
			if($fraction == 'FRACTION_FALSE'){
				// for single choice, we don't punish guessing
				if($qobject->single) $qobject->fraction[$i] = 0;
				// for multi choice, we punish guessing
				else                 $qobject->fraction[$i] = -1.0;
			} elseif ($fraction == 'FRACTION_TRUE'){
				++$truecount;
			} else {
				++$predefcount;
			}
		}
			
		if ($predefcount == 0) {
			// Each true answer gets the same fraction
			if ($truecount == 0) print_error('allanswerswrong','qtex',"Question \"$qobject->name\" 
					                          has no true answer.");
			$truefraction = 1.0/$truecount;
			foreach($qobject->fraction as $i => $fraction){
				if ($fraction == 'FRACTION_TRUE'){
					$qobject->fraction[$i] = $truefraction;
				}
			}
		} elseif ($truecount > 0) {
			print_error('specifyallanswers','qtex',"Please specify fractions for all 
			             correct answers of question \"$qobject->name\" or for none of them.");
		}

		return $qobject;
	}
}

/**
 * Grading scheme used by Meike Akveld for a test exam held on Dec 2nd, 2013
 */
class AkveldGradingScheme {
	/**
	 * @param object $qobject Question object imported from QuestionTeX.
	 *   $qobject->fraction[$i] holds:
	 *     - a proper fraction, if it was specified in the TeX source
	 *     - 'FRACTION_TRUE', if the answer was true
	 *     - 'FRACTION_FALSE', if the answer was false.
	 * @return $qobject with updated grades
	 */
	function grade($qobject) {
		// True/false questions get 1 point.
		// False answers are punished with -1 point.
		if(sizeof($qobject->answer) == 2){
			$qobject->defaultmark= 1;
			foreach($qobject->fraction as $i => $fraction){
				if($fraction == 'FRACTION_FALSE'){
					$qobject->fraction[$i] = -1;
				}
			}
		// Single-choice questions with >2 answers get 2 points.
		// False answers cost -0.5 points (fraction -0.25).
		} else {
			$qobject->defaultmark= 2;
			foreach($qobject->fraction as $i => $fraction){
				if($fraction == 'FRACTION_FALSE'){
					$qobject->fraction[$i] = -0.25;
				}
			}
				
		}
		
		// True answers always have fraction 1.0.
		foreach($qobject->fraction as $i => $fraction){
			if ($fraction == 'FRACTION_TRUE'){
				$qobject->fraction[$i] = 1.0;
			}
		}
		return $qobject; 
	}	
}

/**
 * Grading scheme used by Meike Akveld for Analysis I exam held on Feb 2nd, 2013.
 */
class AkveldGradingSchemeExam {
	/**
	 * @param object $qobject Question object imported from QuestionTeX.
	 *   $qobject->fraction[$i] holds:
	 *     - a proper fraction, if it was specified in the TeX source
	 *     - 'FRACTION_TRUE', if the answer was true
	 *     - 'FRACTION_FALSE', if the answer was false.
	 * @return $qobject with updated grades
	 */
	function grade($qobject) {
		// All questions are worth 1 point (?).
		// There is always exactly one correct answer.
		// False answers are punished with -1 point.
		var_dump($qobject);

		$qobject->defaultmark= 1;
		foreach($qobject->fraction as $i => $fraction){
			if($fraction == 'FRACTION_FALSE'){
				$qobject->fraction[$i] = -1;
			} elseif ($fraction == 'FRACTION_TRUE'){
				$qobject->fraction[$i] = +1;
			}
		}
		
		return $qobject;
	}
}

///////////////////////////////////////////////////////////////////////////
//////////////      End of configuration         /// //////////////////////
///////////////////////////////////////////////////////////////////////////

return true;
?>
