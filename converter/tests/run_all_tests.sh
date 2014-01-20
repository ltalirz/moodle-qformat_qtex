#!/usr/bin/env bash

read -p "Please enter the URL pointig to 'gui.php' " URL


for test in ./test*
do
    cd $test
    ./run_test.sh $URL
    cd ..
done

