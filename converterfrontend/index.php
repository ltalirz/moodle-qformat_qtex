<?php
if (isset($_FILES['input']) && isset($_POST['conversion']) &&
        isset($_POST['sent']) && $_POST['sent'] === 'yes') {
    $moodleUrl = 'http://localhost/moodle/moodle-2.6.1/moodle/';
    $username = "admin";
    $password = "Admin12#";
    $inputFile = uniqid(rand(), true);
    $outputFile = uniqid(rand(), true);
    $paramFile = uniqid(rand(), true) . '_params.json';

    $formatFrom = 'xml';
    $formatTo = 'xml';
    if ($_POST['conversion'] === 't2x') {
        $formatFrom = 'qtex';
        $inputFile .= '.zip';
        $formatTo = 'xml';
        $outputFile .= '.xml';
    } else if ($_POST['conversion'] === 'x2t') {
        $formatFrom = 'xml';
        $inputFile .= '.xml';
        $formatTo = 'qtex';
        $outputFile .= '.zip';
    }

    move_uploaded_file($_FILES['input']['tmp_name'], $inputFile);

    if ($_POST['conversion'] === 't2x') {
        $renderengine = 'tex';
        if (isset($_POST['renderengine'])) {
            if ($_POST['renderengine'] === 'tex') {
                $renderengine = 'tex';
            } else if ($_POST['renderengine'] === 'jsmath') {
                $renderengine = 'jsmath';
            } else if ($_POST['renderengine'] === 'mathjax') {
                $renderengine = 'mathjax';
            }
        }

        $gradingscheme = 'default';
        if (isset($_POST['gradingscheme'])) {
            if ($_POST['gradingscheme'] === 'default') {
                $gradingscheme = 'default';
            } else if ($_POST['gradingscheme'] === 'akveld') {
                $gradingscheme = 'akveld';
            } else if ($_POST['gradingscheme'] === 'akveld-exam') {
                $gradingscheme = 'akveld-exam';
            }
        }

        $params = array(
                        'renderengine' => $renderengine,
                        'gradingscheme' => $gradingscheme
                        );
        $zip = new ZipArchive();
        $zipOpen = $zip->open($inputFile);
        if ($zipOpen === true) {
            $zip->addFromString($paramFile, json_encode($params));
            $zip->close();
        }
    }

    // TODO: Make 100% sure that this is secure enough!!
    // E. g. no injections shall be possible.
    system('../commandline_moodle_question_converter/moodle_question_converter.sh ' .
           $moodleUrl . ' ' . $username . ' ' .  $password . ' ' .
           $formatFrom . ' ' . $formatTo .
           ' ' . $inputFile . ' ' . $outputFile);

    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: public');
    header('Content-Description: File Transfer');
    header('Content-Transfer-Encoding: binary');

    header('Content-Disposition: attachment; filename=' . $outputFile . ';');

    echo file_get_contents($outputFile);

    unlink($inputFile);
    unlink($outputFile);
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
  <title>QuestionTeX Converter</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
</head>
<body>
  <form enctype="multipart/form-data" action="index.php" method="POST">
    <input type="hidden" name="sent" value="yes" />
    <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
    <table>
      <tr>
        <td>Desired conversion</td>
        <td>
          <select name="conversion">
            <option value="t2x">QuestionTeX to Moodle XML</option>
            <option value="x2t">Moodle XML to QuestionTeX</option>
         </select>
        </td>
        <td></td>
      </tr>
      <tr>
        <td>Render engine</td>
        <td>
          <select name="renderengine">
            <option value="tex">TeX or MimeTeX plugin</option>
            <option value="jsmath">JsMath plugin</option>
            <option value="mathjax">MathJax plugin</option>
          </select>
        <td>The text filter used by Moodle to display formulae.</td>
      </tr>
      <tr>
        <td>Grading scheme</td>
        <td>
          <select name="gradingscheme">
            <option value="default">default scheme</option>
            <option value="akveld">Meike Akveld 11-2013</option>
            <option value="akveld-exam">Meike Akveld 2-2014</option>
          </select>
        <td>The scheme employed to grade the answers.</td>
      </tr>
      <tr>
        <td>Input file</td>
        <td><input type="file" name="input" /></td>
      </tr>
    </table>
    <br />
    <input type="reset" value="Reset">
    <input type="submit" value="Translate">
  </form>
</body>
</html>
<?php
}
?>

