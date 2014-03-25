QuestionTeX <-> MoodleXML converter
-----------------------------------

Some Moodle users do not have permission to install new plugins at their 
Moodle server. For this scenario, we provide a set of PHP scripts, 
which convert from QuestionTeX to MoodleXML and vice versa. 
Since MoodleXML is natively supported by Moodle, the XML file may then be
imported into Moodle without requiring modifications at the admin level.

Installation instructions
-------------------------

- Prerequisites
  - A web server running PHP.
    See e.g. www.apachefriends.org on how to set up a local web server.  
  - PHP's zip extension should be enabled. 
    This is a default on many installations.
    
- Copy the folders standalone-converter/ and qtex/ to a path on your web server

In order to use the converter, navigate to 
  http://<server>/standalone-converter/gui.php
which should display a form allowing to select the type of conversion etc.