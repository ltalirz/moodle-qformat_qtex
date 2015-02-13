QuestionTeX format plugin
-------------------------

QuestionTeX is a collection of LaTeX macros that enables authors 
to create multiple-choice tests.

Further documentation and examples are available on

    www.lemuren.math.ethz.ch/coursesupport/multiplechoice
    www.lemuren.math.ethz.ch/projects/texweb/texweb_mc
  
The QuestionTeX format plugin enables the import of questions 
written in QuestionTeX directly into Moodle (as well as the
export from Moodle to QuestionTeX).

This plugin has been written in the projects LEMUREN and nemesis 
at the department of mathematics of ETH Zurich.
For further information contact nemesis@ethz.ch.


Installation instructions
-------------------------

Prerequisites:
- A working Moodle installation
- For the convenient installation through the Moodle admin interface, the web
  server needs write permissions to the $moodle_root/question/format directory.
  For manual installation (or if your Moodle version is < 2.5), 
  copy the folder "qtex" to $moodle_root/question/format.
  Then, in the admin panel purge the Plugin list on
    Site Administration -> Plugins -> Caching -> Configuration
- A configured and enabled TeX Notation filter is required to render the formulae.
  See http://docs.moodle.org/25/en/TeX_notation_filter for instructions.
  Hint: In order to improve vertical alignment of images, 
        add the following css to your theme:
          .texrender {border:0px;vertical-align:middle;}
- Handling of images requires PHP's zip extension to be enabled

Following standard procedure, go to the administration panel under
  Site Administration -> Plugins -> Install add-ons
and select the plugin type 'Question  import/export format'.

The QuestionTeX format should now appear in question export/import dialogues.

If you are looking for some QuestionTeX examples to test the import,
check out the *examples/* folder. 
