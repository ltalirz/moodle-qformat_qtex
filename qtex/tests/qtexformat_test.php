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
 * Unit tests for the Moodle QuestionTex format.
 *
 * @package    qformat_qtex
 * @copyright  2014 Patrick Spettel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format/qtex/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

$fixturesPath = $CFG->dirroot . '/question/format/qtex/tests/';

/**
 * Unit tests for the matching question definition class.
 *
 * @copyright  2014 Patrick Spettel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_qtex_test extends question_testcase {

    public function assert_same_tex($expectedtex, $tex) {
        $this->assertEquals(str_replace("\r\n", "\n", $expectedtex),
                str_replace("\r\n", "\n", $tex));
    }

    public function test_import_001() {
        global $fixturesPath;
        $tex = file_get_contents($fixturesPath . '/fixtures/test001.tex');

        $importer = new qformat_qtex();
        $importer->qformat_qtex_initialize();
        $qs = $importer->readquestions($tex);

        // We expect 1 category and 3 questions.
        $this->assertEquals(count($qs), 4);
    }

    public function test_export_001() {
        $qdata = new stdClass();
        $qdata->id = 123;
        $qdata->contextid = 0;
        $qdata->qtype = 'multichoice';
        $qdata->name = 'Multiple choice question';
        $qdata->questiontext = 'Which are the even numbers?';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'The even numbers are 2 and 4.';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 2;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options = new stdClass();
        $qdata->options->single = 0;
        $qdata->options->shuffleanswers = 0;
        $qdata->options->answernumbering = 'abc';
        $qdata->options->correctfeedback = '<p>Your answer is correct.</p>';
        $qdata->options->correctfeedbackformat = FORMAT_HTML;
        $qdata->options->partiallycorrectfeedback =
            '<p>Your answer is partially correct.</p>';
        $qdata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $qdata->options->shownumcorrect = 1;
        $qdata->options->incorrectfeedback = '<p>Your answer is incorrect.</p>';
        $qdata->options->incorrectfeedbackformat = FORMAT_HTML;

        $qdata->options->answers = array(
            13 => new question_answer(13, '1', 0, '', FORMAT_HTML),
            14 => new question_answer(14, '2', 1, '', FORMAT_HTML),
            15 => new question_answer(15, '3', 0, '', FORMAT_HTML),
            16 => new question_answer(16, '4', 1, '', FORMAT_HTML),
        );

        $exporter = new qformat_qtex();
        $tex = $exporter->writequestion($qdata);

        print_r($tex);

        $expectedtex =
            '\question[Multiple choice question]{Which are the even numbers?}
\false{1}
\true{2}
\false{3}
\true{4}
\explanation{The even numbers are 2 and 4.}
';

        $this->assert_same_tex($expectedtex, $tex);
    }

}
