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

/**
 * This function upgrades the plugin when there is a new version available.
 *
 * @global type $DB
 * @param type $oldversion This string is for the old plugin version
 * @param type $block This string was a default
 * @return boolean Returns whether the current version is new than the older version
 */
function xmldb_block_mynotepad_upgrade($oldversion, $block) {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

if ($oldversion < 2012083000) {
}

    return true;
}