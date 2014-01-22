<?php
if (isset($_FILES['input']) &&
        isset($_POST['sent']) && $_POST['sent'] === 'yes') {
    // TODO: qtex zip files not yet handled.
    $moodleUrl = 'http://localhost/moodle/moodle-2.6.1/moodle/';
    $inputFile = uniqid(rand(), true);
    $outputFile = uniqid(rand(), true);

    $formatFrom = 'xml';
    $formatTo = 'xml';
    if (isset($_POST['conversion'])) {
        if ($_POST['conversion'] === 't2x') {
            $formatFrom = 'qtex';
            $inputFile .= '.tex';
            $formatTo = 'xml';
            $outputFile .= '.xml';
        } else if ($_POST['conversion'] === 'x2t') {
            $formatFrom = 'xml';
            $inputFile .= '.xml';
            $formatTo = 'qtex';
            $outputFile .= '.tex';
        }
    }

    move_uploaded_file($_FILES['input']['tmp_name'], $inputFile);

    // TODO: Make 100% sure that this is secure enough!!
    // E. g. no injections shall be possible.
    system('../commandline_moodle_question_converter/moodle_question_converter.sh ' .
           $moodleUrl . ' admin Admin12# ' . $formatFrom . ' ' . $formatTo .
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
<?xml version="1.0" encoding="UTF-8" ?>
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

