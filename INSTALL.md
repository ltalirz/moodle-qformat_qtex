Prerequisites
-------------

- A working Moodle installation
- The web server needs write permissions to the $moodle_root/question/format directory
  (for convenient installation through the Moodle admin interface)
- A configured and enabled TeX Notation filter.
    See http://docs.moodle.org/25/en/TeX_notation_filter for instructions.
- Note: In order to vertically align images, add the following css to your theme
  .texrender {border:0px;vertical-align:middle;}

Installation through Admin interface
------------------------------------

In the administration panel under
  Site Administration -> Plugins -> Install add-ons
Upload the zip file and enjoy!

A new "qtex" format will appear in question export/import dialogues.

Manual installation
-------------------

Copy the folder "qtex" to $moodle_root/question/format

In the administration panel under 
  Site Administration -> Plugins -> Caching -> Configuration
purge the Plugin list


###Notes
 - For handling of images, PHP's Zip extension is required.

