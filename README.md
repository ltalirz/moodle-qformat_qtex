QuestionTeX
-----------------

QuestionTeX is a collection of LaTeX macros that enables authors 
to create multiple-choice tests.
A documentation of the QuestionTeX format as well as several examples
are available on

    www.lemuren.math.ethz.ch
    www.lemuren.math.ethz.ch/coursesupport/multiplechoice
    www.lemuren.math.ethz.ch/projects/texweb/texweb_mc
    
QuestionTeX for Moodle
---------------------------------

The QuestionTeX plugin for Moodle enables the import of questions 
written in QuestionTeX directly into the Moodle LMS (as well as the
export from Moodle to QuestionTeX).

The source code for the plugin as well as installation instructions are contained in the 'qtex' subfolder.
The plugin is also available as a zip archive from the Moodle plugin repository at moodle.org

Sometimes, Moodle users may not have permission to install new plugins at their Moodle server.
For this scenario, we provide a PHP script, which converts QuestionTeX to MoodleXML. Since MoodleXML is natively supported by Moodle, the XML file may then be imported in Moodle without modifications at the admin level.
The script as well as installation instructions are located in the 'converter' subfolder.


This software has been written in the LEMUREN project at ETH Zurich.

For further information contact nemesis@math.ethz.ch
