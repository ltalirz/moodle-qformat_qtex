#!/usr/bin/env bash

URL=$1

OUTPUT=`curl --silent --header "Content-Type: multipart/form-data" \
    --form "sent=yes" \
    --form "MAX_FILE_SIZE=10000000" \
    --form "conversion=t2x" \
    --form "renderengine=tex" \
    --form "gradingscheme=default" \
    --form "filetype=auto" \
    --form "input=@input.zip;type=application/zip" \
    $URL`

#printf "%s\n" "$OUTPUT"

DIFF=`echo "$OUTPUT" | diff - expected_output.xml`

if [ "$DIFF" == "" ]
then
    echo "Test 001 passed!"
else
    echo "Test 001 FAILED!"
fi

