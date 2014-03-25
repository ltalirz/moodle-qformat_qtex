#!/usr/bin/env bash

function usage {
    printf "usage: %s <URL to 'gui.php'>\n" $(basename $0)
    exit 1
}

nCommandLineArgs=$#
if (( $nCommandLineArgs != 1 ))
then
    usage
fi

URL=$1
#read -p "Please enter the URL pointing to 'gui.php' " URL

for test in ./test*
do
    cd $test
    ./run_test.sh $URL
    cd ..
done

