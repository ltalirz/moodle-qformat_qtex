Introduction
------------

QuestionTeX is LaTeX package for writing multiple-choice tests.
The QuestionTeX package and its documentation can be found
on https://github.com/ltalirz/QuestionTeX.

The QuestionTeX format plugin makes it possible to import of questions written
in QuestionTeX directly into Moodle and to export questions from Moodle to
QuestionTeX.

Installation instructions
-------------------------

### Prerequisites

- A working Moodle installation
- A configured and enabled TeX Notation filter is required to render the formulae.
  See http://docs.moodle.org/25/en/TeX_notation_filter for instructions.
  Hint: In order to improve vertical alignment of images,
        add the following css to your theme:
          .texrender {border:0px;vertical-align:middle;}
- (optional) Handling of images requires PHP's zip extension to be enabled

### Installation

Simply follow the standard procedure for installing plugins:
- go to Site Administration -> Plugins -> Install add-ons
- select plugin type 'Question  import/export format'
- the QuestionTeX format should now appear in question export/import dialogues

For QuestionTeX examples to test the import, see the *examples/* folder.

If you prefer a manual installation (or if your Moodle version is < 2.5),
copy the folder "qtex" to $moodle_root/question/format.
Then, in the admin panel purge the Plugin list on
Site Administration -> Plugins -> Caching -> Configuration

Contact
-------

This software was written in the projects LEMUREN and nemesis at the department
of mathematics of ETH Zurich.
For further information please contact echo@ethz.ch
