#!/usr/bin/env bash

# change this to the path to `gui.php`
URL="http://localhost/moodle-qformat_qtex/converter/gui.php"

for test in ./test*
do
    cd $test
    ./run_test.sh $URL
    cd ..
done

