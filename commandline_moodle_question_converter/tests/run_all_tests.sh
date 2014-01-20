#!/usr/bin/env bash

function usage {
    printf "usage: %s <URL to Moodle> <username> <password>" $(basename $0)
    exit 1
}

nCommandLineArgs=$#
if (( $nCommandLineArgs != 3 ))
then
    usage
fi

URL=$1
username=$2
password=$3

for test in ./test*
do
    cd $test
    ./run_test.sh $URL $username $password
    cd ..
done

