#!/usr/bin/env bash

../../moodle_question_converter.sh xml qtex input.xml output.tex
../../moodle_question_converter.sh qtex xml input.tex output.xml

DIFF1=`cat output.tex | diff - expected_output.tex`
DIFF2=`cat output.xml | diff - expected_output.xml`

if [[ "$DIFF1" == "" && "$DIFF2" == "" ]]
then
    echo "Test 001 passed!"
else
    echo "Test 001 FAILED!"
fi

