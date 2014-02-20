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

    public function test_import_jsmath_inline() {
        global $fixturesPath;
        $tex = '\documentclass[a4paper,oneside]{article}
\input{MoodleQuiz_Macros.tex}
\showsolution
\showfeedback

\begin{document}
\question[Multiple choice question]{Which are the even numbers $\frac{5}{5}$?}
\false{1}
\true{$\frac{4}{2} = 2$}
\false{3}
\true{4}
\explanation{The even numbers are 2 and 4.}
\end{document}
';

        $importer = new qformat_qtex();
        $importer->qformat_qtex_initialize();
        $importer->set_renderengine(qformat_qtex::FLAG_FILTER_JSMATH);
        $importer->set_gradingscheme(new DefaultGradingScheme());
        $qs = $importer->readquestions($tex);

        // We expect 1 category and 1 question.
        $this->assertEquals(count($qs), 2);

        $this->assertEquals($qs[1]->questiontext,
                            'Which are the even numbers \(\frac{5}{5}\)?');
        $this->assertEquals($qs[1]->answer[1]['text'],
                            '\(\frac{4}{2} = 2\)');
    }

    public function test_import_tex_inline() {
        global $fixturesPath;
        $tex = '\documentclass[a4paper,oneside]{article}
\input{MoodleQuiz_Macros.tex}
\showsolution
\showfeedback

\begin{document}
\question[Multiple choice question]{Which are the even numbers $\frac{5}{5}$?}
\false{1}
\true{$\frac{4}{2} = 2$}
\false{3}
\true{4}
\explanation{The even numbers are 2 and 4.}
\end{document}
';

        $importer = new qformat_qtex();
        $importer->qformat_qtex_initialize();
        $importer->set_renderengine(qformat_qtex::FLAG_FILTER_TEX);
        $importer->set_gradingscheme(new DefaultGradingScheme());
        $qs = $importer->readquestions($tex);

        // We expect 1 category and 1 question.
        $this->assertEquals(count($qs), 2);

        $this->assertEquals($qs[1]->questiontext,
                            'Which are the even numbers $$\frac{5}{5}$$?');
        $this->assertEquals($qs[1]->answer[1]['text'],
                            '$$\frac{4}{2} = 2$$');
    }

    public function test_import_mathjax_inline() {
        global $fixturesPath;
        $tex = '\documentclass[a4paper,oneside]{article}
\input{MoodleQuiz_Macros.tex}
\showsolution
\showfeedback

\begin{document}
\question[Multiple choice question]{Which are the even numbers $\frac{5}{5}$?}
\false{1}
\true{$\frac{4}{2} = 2$}
\false{3}
\true{4}
\explanation{The even numbers are 2 and 4.}
\end{document}
';

        $importer = new qformat_qtex();
        $importer->qformat_qtex_initialize();
        $importer->set_renderengine(qformat_qtex::FLAG_FILTER_MATHJAX);
        $importer->set_gradingscheme(new DefaultGradingScheme());
        $qs = $importer->readquestions($tex);

        // We expect 1 category and 1 question.
        $this->assertEquals(count($qs), 2);

        $this->assertEquals($qs[1]->questiontext,
                            'Which are the even numbers \(\frac{5}{5}\)?');
        $this->assertEquals($qs[1]->answer[1]['text'],
                            '\(\frac{4}{2} = 2\)');
    }

    public function test_import_jsmath_equation() {
        global $fixturesPath;
        $tex = '\documentclass[a4paper,oneside]{article}
\input{MoodleQuiz_Macros.tex}
\showsolution
\showfeedback

\begin{document}
\question[Multiple choice question]{Which are the even numbers $$\frac{5}{5}$$?}
\false{1}
\true{$$\frac{4}{2} = 2$$}
\false{3}
\true{4}
\explanation{The even numbers are 2 and 4.}
\end{document}
';

        $importer = new qformat_qtex();
        $importer->qformat_qtex_initialize();
        $importer->set_renderengine(qformat_qtex::FLAG_FILTER_JSMATH);
        $importer->set_gradingscheme(new DefaultGradingScheme());
        $qs = $importer->readquestions($tex);

        // We expect 1 category and 1 question.
        $this->assertEquals(count($qs), 2);

        $this->assertEquals($qs[1]->questiontext,
                            "Which are the even numbers <p style='text-align:" .
                            " center' class='formula'>\\(\\frac{5}{5}\\)</p>?");
        $this->assertEquals($qs[1]->answer[1]['text'],
                            "<p style='text-align: center' " .
                            "class='formula'>\\(\\frac{4}{2} = 2\\)</p>");
    }

    public function test_import_tex_equation() {
        global $fixturesPath;
        $tex = '\documentclass[a4paper,oneside]{article}
\input{MoodleQuiz_Macros.tex}
\showsolution
\showfeedback

\begin{document}
\question[Multiple choice question]{Which are the even numbers $$\frac{5}{5}$$?}
\false{1}
\true{$$\frac{4}{2} = 2$$}
\false{3}
\true{4}
\explanation{The even numbers are 2 and 4.}
\end{document}
';

        $importer = new qformat_qtex();
        $importer->qformat_qtex_initialize();
        $importer->set_renderengine(qformat_qtex::FLAG_FILTER_TEX);
        $importer->set_gradingscheme(new DefaultGradingScheme());
        $qs = $importer->readquestions($tex);

        // We expect 1 category and 1 question.
        $this->assertEquals(count($qs), 2);

        $this->assertEquals($qs[1]->questiontext,
                            "Which are the even numbers <p style='text-align:" .
                            " center' class='formula'>$$\\frac{5}{5}$$</p>?");
        $this->assertEquals($qs[1]->answer[1]['text'],
                            "<p style='text-align: center' " .
                            "class='formula'>$$\\frac{4}{2} = 2$$</p>");
    }

    public function test_import_mathjax_equation() {
        global $fixturesPath;
        $tex = '\documentclass[a4paper,oneside]{article}
\input{MoodleQuiz_Macros.tex}
\showsolution
\showfeedback

\begin{document}
\question[Multiple choice question]{Which are the even numbers $$\frac{5}{5}$$?}
\false{1}
\true{$$\frac{4}{2} = 2$$}
\false{3}
\true{4}
\explanation{The even numbers are 2 and 4.}
\end{document}
';

        $importer = new qformat_qtex();
        $importer->qformat_qtex_initialize();
        $importer->set_renderengine(qformat_qtex::FLAG_FILTER_MATHJAX);
        $importer->set_gradingscheme(new DefaultGradingScheme());
        $qs = $importer->readquestions($tex);

        // We expect 1 category and 1 question.
        $this->assertEquals(count($qs), 2);

        $this->assertEquals($qs[1]->questiontext,
                            'Which are the even numbers $$\frac{5}{5}$$?');
        $this->assertEquals($qs[1]->answer[1]['text'],
                            '$$\frac{4}{2} = 2$$');
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

    public function test_export_002() {
        $qdata = new stdClass();
        $qdata->id = 123;
        $qdata->contextid = 0;
        $qdata->qtype = 'multichoice';
        $qdata->name = 'Multiple choice question';
        $qdata->questiontext = 'Which are the even numbers $\frac{5}{5}$?';
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
            14 => new question_answer(14, '$\frac{4}{2} = 2$',
                                      1, '', FORMAT_HTML),
            15 => new question_answer(15, '3', 0, '', FORMAT_HTML),
            16 => new question_answer(16, '4', 1, '', FORMAT_HTML),
        );

        $exporter = new qformat_qtex();
        $exporter->qformat_qtex_initialize();
        $exporter->set_renderengine(qformat_qtex::FLAG_FILTER_JSMATH);
        $exporter->set_gradingscheme(new DefaultGradingScheme());
        $tex = $exporter->writequestion($qdata);
        $tex = $exporter->export_prepare_for_display($tex);

        $expectedtex =
            '% QuestionTeX Template
% Version of 8.5.2009

% User Information
% ================
%   - This LaTeX source may be compiled by either latex or pdflatex.
%   - The compilation process requires a file "Questiontex_Macros.tex" to be
%     found in the same folder. It can be downloaded from
%       www.lemuren.math.ethz.ch/coursesupport/multiplechoice
%     where you can also find a more detailed documentation.
%   - Please do not make use of user-defined macros.

\documentclass[a4paper,oneside]{article}
\input{MoodleQuiz_Macros.tex}
\showsolution
\showfeedback

\begin{document}
\question[Multiple choice question]{Which are the even numbers $\frac{5}{5}$?}
\false{1}
\true{$\frac{4}{2} = 2$}
\false{3}
\true{4}
\explanation{The even numbers are 2 and 4.}
\end{document}
';

        $this->assert_same_tex($expectedtex, $tex);
    }

}
