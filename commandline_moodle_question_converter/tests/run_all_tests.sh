#!/usr/bin/env bash

for test in ./test*
do
    cd $test
    ./run_test.sh
    cd ..
done

