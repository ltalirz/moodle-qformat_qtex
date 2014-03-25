QuestionTeX to MoodleXML converter
----------------------------------

Sometimes, Moodle users may not have permission to install new plugins at their Moodle server.
For this scenario, we provide this PHP script, which converts QuestionTeX to MoodleXML. 
Since MoodleXML is natively supported by Moodle, the XML file may then be imported in Moodle without modifications at the admin level.

Installation instructions
-------------------------

- prerequisites: A web server running PHP. PHP's zip extension should be enabled.
- Copy the folders converter/ and qtex/ to your web server

In order to use the converter, navigate to 
  http://<server>/converter/gui.php
and follow instructions