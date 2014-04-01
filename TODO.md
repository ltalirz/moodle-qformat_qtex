- FIX: In standalone converter, quotes must not be escaped
- Think about extensibility of question types
- Think about replacing zip by pclzip (moodle/lib/pclzip)

- FIX: qtex/format.php:
    readquestion of qformat_qtex has different set of parameters than
    readquestion of parent class qformat_default, which conflicts with
    PHP Strict standards. 