#!/usr/bin/env bash

function usage {
    printf \
        "usage: %s <URL to Moodle> <username> <password> \
<format from> <format to> <input file> <output file>\n" $(basename $0)
    exit 1
}

nCommandLineArgs=$#
if (( $nCommandLineArgs != 7 ))
then
    usage
fi

argUrlToMoodle=$1
argUsername=$2
argPassword=$3
argFormatFrom=$4
argFormatTo=$5
argInputFile=$6
argOutputFile=$7

formatFrom="xml"
if [[ $argFormatFrom = "xml" ]]
then
    formatFrom="xml"
elif [[ $argFormatFrom = "qtex" ]]
then
    formatFrom="qtex"
else
    echo "Unknown format $argFormatFrom, using xml."
fi

formatTo="xml"
if [[ $argFormatTo = "xml" ]]
then
    formatTo="xml"
elif [[ $argFormatTo = "qtex" ]]
then
    formatTo="qtex"
else
    echo "Unknown format $argFormatFrom, using xml."
fi

COOKIE_FILE="/tmp/cookies.txt"
LOGIN_URL="$argUrlToMoodle/login/index.php"
UPLOAD_URL="$argUrlToMoodle/repository/repository_ajax.php?action=upload"
IMPORT_URL="$argUrlToMoodle/question/import.php"
EXPORT_URL="$argUrlToMoodle/question/export.php"
CATEGORY_URL="$argUrlToMoodle/question/category.php"
PAGE_LIMIT=1000

# Moodle login using the given username and password.
# @param 1 username
# @param 2 password
# As a result echoes the sesskey.
function login {
    username=$1
    password=$2
    local loginResponse=$(curl --silent --cookie-jar $COOKIE_FILE --location \
        --data "username=$username" --data "password=$password" \
        $LOGIN_URL)

    local sesskey=""
    if [[ $loginResponse =~ sesskey=([^=\"]*) ]]
    then
        sesskey=${BASH_REMATCH[1]}
    fi
    echo "$sesskey"
}

# Moodle file upload.
# @param 1 The file to upload.
# @param 2 itemid, the id to associate with the uploaded file.
# @param 3 clientid
# @param 4 sesskey
function upload {
    local file=$1
    local itemid=$2
    local clientid=$3
    local sesskey=$4
    local uploadResponse=$(curl --silent --cookie $COOKIE_FILE --location \
        --header "Content-Type: multipart/form-data" \
        --form "repo_upload_file=@$file" \
        --form "title=" --form "author=" --form "license=" \
        --form "itemid=$itemid" --form "repo_id=4" --form "p=" \
        --form "page=" --form "env=filepicker" --form "sesskey=$sesskey" \
        --form "client_id=$clientid" --form "maxbytes=-1" \
        --form "areamaxbytes=-1" \
        --form "ctx_id=2" --form "savepath=/" \
        $UPLOAD_URL)

    #printf "%s\n" "$uploadResponse"
}

# Moodle question import.
# @param 1 The category into which the questions should be imported.
# @param 2 itemid, the id of the file to import the questions from.
# @param 3 sesskey
# @param 4 The format of the import file.
function importQuestions {
    local category=$1
    local itemid=$2
    local sesskey=$3
    local format=$4
    local importResponse=$(curl --silent --cookie $COOKIE_FILE --location \
        --data "courseid=1" --data "cat=$category" --data "sesskey=$sesskey" \
        --data "_qf__question_import_form=1" \
        --data "mform_isexpanded_id_fileformat=1" \
        --data "mform_isexpanded_id_general=0" \
        --data "mform_isexpanded_id_importfileupload=1" \
        --data "format=$format" --data "category=$category" \
        --data "catfromfile=0" --data "contextfromfile=0" \
        --data "matchgrades=error" --data "stoponerror=1" \
        --data "newfile=$itemid" --data "submitbutton=Import" \
        $IMPORT_URL)

    #printf "%s\n" "$importResponse"
}

# Moodle question export.
# @param 1 The category from which to export the questions.
# @param 2 sesskey
# @param 3 The format of the exported file.
# Echoes the export result.
function exportQuestions {
    local category=$1
    local sesskey=$2
    local format=$3
    local exportResponse=$(curl --silent --cookie $COOKIE_FILE --location \
        --data "courseid=1" --data "cat=$category" --data "sesskey=$sesskey" \
        --data "_qf__question_export_form=1" \
        --data "mform_isexpanded_id_fileformat=1" \
        --data "mform_isexpanded_id_general=1" \
        --data "format=$format" --data "category=$category" \
        --data "cattofile=0" --data "contexttofile=0" \
        --data "submitbutton=Export questions to file" \
        $EXPORT_URL)

    #printf "%s\n" "$exportResponse"

    local downloadUrl=""
    if [[ $exportResponse =~ document\.location\.replace\(\"([^\"]*)\"\)\; ]]
    then
        downloadUrl=${BASH_REMATCH[1]}
    fi
    downloadUrl=$(echo $downloadUrl | sed -e "s/\\\//g")

    curl --silent --cookie $COOKIE_FILE --location $downloadUrl
}

# Creates a new Moodle question category.
# @param 1 The name to use for the new category.
# @param 2 sesskey
# Echoes the new category id.
function addCategory {
    local categoryName=$1
    local sesskey=$2
    local response=$(curl --silent --cookie $COOKIE_FILE --location \
        --data "courseid=1" --data "id=0" --data "sesskey=$sesskey" \
        --data "_qf__question_category_edit_form=1" \
        --data "mform_isexpanded_id_categoryheader=1" \
        --data "parent=0,2" --data "name=$categoryName" \
        --data "info[text]=" --data "info[format]=1" \
        --data "submitbutton=Add category" \
        $CATEGORY_URL)

    # Search for the id of the new category.
    # Because the categories are printed in pages, this
    # code linearly searches all pages up to a configurable limit.
    local category=""
    local page=1
    while [[ -z $category ]]
    do
        if (( $page > $PAGE_LIMIT ))
        then
            break
        fi
        response=$(curl --silent --cookie $COOKIE_FILE --location \
            "$CATEGORY_URL?courseid=1&cpage=$page")
        if [[ $response =~ edit=([0-9]+)\"\>$categoryName ]]
        then
            category=${BASH_REMATCH[1]}
        fi
        (( page = $page + 1 ))
    done
    echo $category
}

# Echoes a timestamp, i. e. the seconds since 1970-01-01 00:00:00 UTC
function timestamp {
    date +"%s"
}

itemid=$RANDOM
clientid=$RANDOM
categoryName="$(timestamp)_$RANDOM"

sesskey=$(login $argUsername $argPassword)
category="$(addCategory $categoryName $sesskey),2"
upload $argInputFile $itemid $clientid $sesskey
importQuestions $category $itemid $sesskey $formatFrom
exportQuestions $category $sesskey $formatTo > $argOutputFile

# TODO: logout, i. e. invalidate the session.


