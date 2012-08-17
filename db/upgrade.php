<?php

/**
**************************************************************************
**                              mynotepad                               **
**************************************************************************
* @package     block                                                    **
* @subpackage  mynotepad                                                **
* @name        mynotepad                                                **
* @copyright   oohoo.biz                                                **
* @link        http://oohoo.biz                                         **
* @author      Theodore Pham                                            **
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
**************************************************************************
**************************************************************************/

function xmldb_block_mynotepad_upgrade($oldversion, $block) {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

if ($oldversion < 2012081701) {
}

    return true;
}