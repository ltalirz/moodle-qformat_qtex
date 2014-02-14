QuestionTeX format plugin
-------------------------

QuestionTeX is a collection of LaTeX macros that enables authors 
to create multiple-choice tests.
A documentation of the QuestionTeX format as well as several examples
are available on

    www.lemuren.math.ethz.ch
    www.lemuren.math.ethz.ch/coursesupport/multiplechoice
    
The QuestionTeX format plugin enables the import of questions 
written in QuestionTeX directly into Moodle (as well as the
export from Moodle to QuestionTeX).

This plugin has been written in the LEMUREN project at ETH Zurich.

For further information contact nemesis@math.ethz.ch


Installation instructions
-------------------------

Following standard procedure, go to the administration panel under
  Site Administration -> Plugins -> Install add-ons
upload the zip file and enjoy!

The QuestionTeX format should now appear in question export/import dialogues.


Prerequisites:
- A working Moodle installation
- For the convenient installation through the Moodle admin interface, the web
  server needs write permissions to the $moodle_root/question/format directory.
  For manual installation, copy the folder "qtex" to $moodle_root/question/format.
  Then, in the admin panel purge the Plugin list 
    Site Administration -> Plugins -> Caching -> Configuration
- A configured and enabled TeX Notation filter is required to render the formulae.
  See http://docs.moodle.org/25/en/TeX_notation_filter for instructions.
  Hint: In order to improve vertical alignment of images, add the following css to your theme
        .texrender {border:0px;vertical-align:middle;}
- Handling of images requires PHP's zip extension to be enabled

