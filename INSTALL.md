Prerequisites
-------------

- A working Moodle installation
- The web server needs write permissions to the $moodle_root/question/format directory
  (for convenient installation through the Moodle admin interface)
- A configured and enabled TeX Notation filter.
    See http://docs.moodle.org/25/en/TeX_notation_filter for instructions.
    For better image quality, I recommend the png format with a density > 200.  

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
 - This plugin was written for Moodle 1.9.4. Other versions have not been
   tested. A version for Moodle 2.0 will probably be released.  
 - For handling of images, PHP's Zip extension is required.

