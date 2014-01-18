#!/usr/bin/env bash

URL=$1

OUTPUT=`curl --silent --header "Content-Type: multipart/form-data" \
    --form "sent=yes" \
    --form "MAX_FILE_SIZE=10000000" \
    --form "conversion=x2t" \
    --form "renderengine=tex" \
    --form "gradingscheme=default" \
    --form "filetype=auto" \
    --form "input=@input.xml;type=text/xml" \
    $URL`

#printf "%s\n" "$OUTPUT"

DIFF=`echo "$OUTPUT" | diff - expected_output.tex`

if [ "$DIFF" == "" ]
then
    echo "Test 002 passed!"
else
    echo "Test 002 FAILED!"
fi

