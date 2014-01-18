18.01.2013
==========
- Fixes in the export file handling.
  Now images can be exported using the qtex format.
- Fixes in the converter.
  Both directions work now, but no images are handled.

14.10.2013
=========
- qtex now works with Moodle 2.5
- qtex can be installed conveniently via the Moodle admin interface

3.9.2013
=======
- Since qtex is an import/export format and *not* a question type,
  the repository should be named 'moodle-qformat_qtex'

14.9.2011
=======
- File import (png images) has been implemented
- Export has been implemented, but still some stuff to do
  (if images are included, it really returns a zip file, but with tex
  extension that needs to be renamed)


13.6.2011
=======
- The plugin (import) works on Moodle 2.1
- File import and all the export still todo

23.5.2011
=======
- The conversion process is finally updated to the new LaTeX syntax
- The standalone GUI works again


Base
====
- From now on, the format goes under the name "QuestionTeX". Changed naming
  conventions and dialogues. 
- Moved some stuff from config to language file  
- Fixed error concerning spaces in front of curly braces
- Fixed issue with identifying the correct tex filter
- During zip import, the content of the folder '__MACOSX' is now ignored
