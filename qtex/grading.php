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
 * Grading schemes for multiple-choice questions.
 *
 * @package    qformat
 * @subpackage qtex
 * @author     Leopold Talirz
 * @copyright  2014 Project LEMUREN, ETH Zurich
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Default grading scheme.
 *
 * Punishes guessing in multi-choice questions,
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
			if($fraction === 'FRACTION_FALSE'){
				// for single choice, we don't punish guessing
				if($qobject->single) $qobject->fraction[$i] = 0.0;
				// for multi choice, we punish guessing
				else                 $qobject->fraction[$i] = -1.0;
			} elseif ($fraction === 'FRACTION_TRUE'){
				++$truecount;
			} else {
				++$predefcount;
			}
		}

		if ($predefcount == 0) {
			// Each true answer gets the same fraction
			if ($truecount == 0) print_error('allanswerswrong','qtex',
					"Question \"$qobject->name\" has no true answer.");
			$truefraction = 1.0/$truecount;
			foreach($qobject->fraction as $i => $fraction){
				if ($fraction === 'FRACTION_TRUE'){
					$qobject->fraction[$i] = $truefraction;
				}
			}
		} elseif ($truecount > 0) {
			print_error('specifyallanswers','qtex',
			  "Please specify fractions for all correct answers 
			  of question \"$qobject->name\" or for none of them.");
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


return true;
?>
