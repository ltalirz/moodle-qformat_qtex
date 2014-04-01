QuestionTeX
------------

QuestionTeX is a collection of LaTeX macros that enables authors 
to create multiple-choice tests.
The QuestionTeX package and its documentation can be found in the 
*questiontex/* folder and as *questiontex.zip* in the release section.
Further documentation and examples are available on

    www.lemuren.math.ethz.ch/coursesupport/multiplechoice
    www.lemuren.math.ethz.ch/projects/texweb/texweb_mc
    
QuestionTeX for Moodle
-----------------------

### Plugin

The QuestionTeX plugin for Moodle enables the import of questions written in 
QuestionTeX directly into the Moodle LMS. Export from Moodle to QuestionTeX
is also possible.

The plugin as well as installation instructions can be found in the *qtex/* 
subfolder. Its zip archive *qformat_qtex.zip* is also available from the 
Moodle plugin repository at www.moodle.org .

### Standalone Converter

Some Moodle users do not have permission to install new plugins at their 
Moodle server. For this scenario, we provide a set of PHP scripts, 
which convert from QuestionTeX to MoodleXML and vice versa. 
Since MoodleXML is natively supported by Moodle, the XML file may then be
imported into Moodle without requiring modifications at the admin level.

The converter as well as installation instructions can be found in the 
*standalone-converter/* subfolder and in the *standalone-converter.zip* 
archive in the release section.

### Moodle Converter (Experimental) 

An alternative to using the standalone converter is to set up a local
Moodle installation with QuestionTeX plugin. This Moodle can then be
used to convert between QuestionTeX and MoodleXML.

The *moodle-converter/* subfolder contains command-line and PHP scripts
that may be helpful in automating this process.
Note, however, that these scripts have not been well tested.

Contact
-------

This software was written in the projects LEMUREN and nemesis at the department
of mathematics of ETH Zurich.
For further information contact nemesis@ethz.ch
