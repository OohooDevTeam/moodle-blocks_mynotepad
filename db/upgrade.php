<?php

function xmldb_block_mynotepad_upgrade($oldversion, $block) {
    global $DB;
    
    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

//    if ($oldversion < 2011062000) {
//        global $DB;
//        $dbman = $DB->get_manager();
//
//        // Define table block_mynotepad to be created
//        $table = new xmldb_table('block_mynotepad');
//
//        // Adding fields to table block_mynotepad
//        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
//        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
//        $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
//        $table->add_field('text', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
//        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
//
//        // Adding keys to table block_mynotepad
//        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
//
//        // Conditionally launch create table for block_mynotepad
//        if (!$dbman->table_exists($table)) {
//            $dbman->create_table($table);
//        }
//
//        // mynotes savepoint reached
//        upgrade_block_savepoint(true,2011062201, 'mynotes');
//    }
if ($oldversion < 2012081500) {
}

    return true;
}