<?php
/**
 * GUI for conversion from LaTeX question format to MoodleXML question format.
 *
 * Reads LaTeX/MoodleXML from user input file and prompts for download of
 * translated MoodleXML/LaTeX-File. Supports upload of zip file with included
 * images.
 *
 * @uses moodle_emulator.php
 *
 * @author Leopold Talirz, Patrick Spettel
 */

// Configuration start
$path_to_moodle_emulator = 'moodle_emulator.php';
// End configuration

$gui_redirect = $_SERVER['SCRIPT_NAME'];

if (isset($_POST['sent']) && $_POST['sent']==='yes') {
    // Get me a moodle
    require_once($path_to_moodle_emulator); // Then Moodle Emulator

    // Get uploaded file
    $inputdata = $_FILES['input'];
    if($inputdata['error'] != UPLOAD_ERR_OK){
        print_form(1, "File upload failed.\n");
        die;
    }

    $inputfilepath = $inputdata['tmp_name'];
    $inputfilename = $inputdata['name'];
    // If specified, we add an extra extension (no harm done if we doubled it)
    if($_POST['filetype'] != 'auto'){
        $inputfilename .= $_POST['filetype'];
    }

    // Get other variables
    $targetfilename = $_POST['target'];
    $direction = $_POST['conversion'];
    switch($_POST['renderengine']){
        case 'tex': $CFG->textfilters = array('tex'); break;
        case 'jsmath': $CFG->textfilters = array('jsmath'); break;
        case 'mathjax': $CFG->textfilters = array('mathjax'); break;
    }
    $CFG->gradingscheme = $_POST['gradingscheme'];

    $CFG->notify = (isset($_POST['errorhandling'])) ? true : false;

    // If we shall translate from LaTeX to MoodleXML
    if($direction == 't2x'){
        $startformat = 'qtex_emulator';
        $endformat = 'xml_emulator';
    }
    // If we shall translate from MoodleXML to TeX
    elseif($direction == 'x2t'){
        $startformat = 'xml_emulator';
        $endformat = 'qtex_emulator';
    }
    else{
        print_form(1, "Unknown conversion requested.\n");
        die;
    }
    
    $startclass = 'qformat_'.$startformat;
    $endclass = 'qformat_'.$endformat;
    
    // Import questions into array of objects
    $qimport = new $startclass();
    if ($direction === 't2x') {
        $qimport->qformat_qtex_initialize();
    }
    $qimport->setFilename($inputfilepath);
    // Realfilename is used to determine the type of the file
    $qimport->setRealfilename($inputfilename);
    $lines = $qimport->readdata($qimport->filename);

    $questions = $qimport->readquestions($lines);
    // Regretfully, the questions don't pop out of the database as they
    // come in. Thus we have to do some cosmetics before passing them
    // back again.
    $questions = process_for_export($questions);
    $fs->questions = $questions;
    
    
    // Export questions
    $qexport = new $endclass();

    if ($direction === 'x2t') {
        $qexport->qformat_qtex_initialize();
    }

    // Handle name of target file
    $qexport->setFilename('temp');
    $qexport->setRealfilename('temp');
    $qexport->course = new stdClass();
    $qexport->course->id = 'courseid';

    $qexport->questions = $questions;
    $targetfile = $qexport->exportprocess();
    // Get name of target file
    if($targetfilename == '') $targetfilename = 'moodlequiz'.$qexport->export_file_extension();

    // Write headers
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: public');
    header('Content-Description: File Transfer');
    header('Content-Transfer-Encoding: binary');

    header('Content-Disposition: attachment; filename='.$targetfilename.';');


    if($qexport->export_file_extension() === '.zip'){
        header('Content-type: application/zip');
    }
    else{
        header('Content-type: text/plain');
    }
    echo($targetfile);

}

// Print form for data input
else{
    print_form(0, "");
}


/**
 * Prints the appropriate form
 *
 * @param int $flag If value is 1, some error occured during the import process and $errormessage is displayed above the form
 * @param string $errormessage Error message describing error in conversion process
 */
function print_form($flag, $errormessage) {
    global $gui_redirect;

    $html = "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso- 8859-1\" />
<title>QuestionTeX Converter</title>
</head>
<body>";
    if($flag == 1){
        $html .= "<b>Error:</b> $errormessage <br><br>";
    }
    $html .= "
This script converts between QuestionTeX and Moodle XML.<br><br>
<form enctype='multipart/form-data' action='$gui_redirect' method='POST'>
   <input type='hidden' name='sent' value='yes'>
   <input type='hidden' name='MAX_FILE_SIZE' value='10000000' />
<table>
    <tr>
        <td><font size=-1>Desired conversion</font></td>
        <td>
         <select name='conversion'>
          <option value='t2x'".(isset($_POST['conversion']) && $_POST['conversion']==='t2x' ? ' selected' : '').">QuestionTeX to Moodle XML</option>
          <option value='x2t'".(isset($_POST['conversion']) && $_POST['conversion']==='x2t' ? ' selected' : '').">Moodle XML to QuestionTeX</option>".
    //<option value='t2t'".(isset($_POST['conversion']) && $_POST['conversion']==='t2t' ? ' selected' : '').">LaTeX macro update</option>
         "</select>
        <td><font size=-1> </td>
    </tr>
    <tr>
        <td><font size=-1>Render engine</font></td>
        <td>
         <select name='renderengine'>
          <option value='tex'".(isset($_POST['renderengine']) && $_POST['renderengine']==='tex' ? ' selected' : '').">TeX or MimeTeX plugin</option>
          <option value='jsmath'".(isset($_POST['renderengine']) && $_POST['renderengine']==='jsmath' ? ' selected' : '').">JsMath plugin</option>
          <option value='mathjax'".(isset($_POST['renderengine']) && $_POST['renderengine']==='mathjax' ? ' selected' : '').">MathJax plugin</option>
         </select>
        <td><font size=-1> The text filter used by Moodle to display formulae</td>
    </tr>
    <tr>
        <td><font size=-1>Grading scheme</font></td>
        <td>
         <select name='gradingscheme'>
          <option value='default'".(isset($_POST['gradingscheme']) && $_POST['gradingscheme']==='default' ? ' selected' : '').">default scheme</option>
          <option value='akveld'".(isset($_POST['gradingscheme']) && $_POST['gradingscheme']==='akveld' ? ' selected' : '').">Meike Akveld 11-2013</option>
          <option value='akveld-exam'".(isset($_POST['gradingscheme']) && $_POST['gradingscheme']==='akveld-exam' ? ' selected' : '').">Meike Akveld 2-2014</option>         </select>
        <td><font size=-1> The scheme employed to grade the answers.</td>
    </tr>
                <tr>
        <td><font size=-1>Type of input</font></td>
        <td>
         <select name='filetype'>
          <option value='auto'".(isset($_POST['filetype']) && $_POST['filetype']==='auto' ? ' selected' : '').">autodetect</option>
          <option value='tex'".(isset($_POST['filetype']) && $_POST['filetype']==='tex' ? ' selected' : '').">TeX</option>
          <option value='xml'".(isset($_POST['filetype']) && $_POST['filetype']==='xml' ? ' selected' : '').">XML</option>
          <option value='zip'".(isset($_POST['filetype']) && $_POST['filetype']==='zip' ? ' selected' : '').">ZIP</option>
         </select>
        <td><font size=-1> ZIP may be used to provide TeX with images</td>
    </tr>
    <tr>
        <td><font size=-1>Input file<font color=red>*</font></td>
        <td><input type='file' name='input' value='".(isset($_FILES['input']['name']) ? $_FILES['input']['name'] : '')."'></td>
        <td><font size=-1> </td>
    </tr>
    <tr>
        <td><font size=-1>Target file</td>
        <td><input type='Text' name='target' maxlength='30' value='".(isset($_POST['target']) ? $_POST['target'] : '')."'></td>
        <td><font size=-1> </td>
    </tr>
        <tr>
        <td><font size=-1>Error analysis</td>
        <td><input type='checkbox' name='errorhandling' value='notify' ".(isset($_POST['errorhandling']['notify']) ? 'checked': '')."></td>
        <td><font size=-1> May be useful, if conversion is erroneous.</td>
    </tr>
</table>
<br>
<font color=red>*</font> mandatory <BR>
<br>
<input type=reset value='Reset'> <input type=submit value='Translate'></form>
<br>
<i>Beta 6, 05.02.2014</i>
</body>
</html>
";
    echo $html;
}

?>
