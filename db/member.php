<?php

require_once('table_names.php');

function db_CreateRoleTableIfNotExists() {
    global $wpdb;
    $tableName = db_GetMemberTableName();
    if($wpdb->get_var("show tables like $tableName") != $tableName) {
        $sql = "CREATE TABLE $tableName(" .
                " user VARCHAR(60) NOT NULL," .
                " paid_through DATE," .
                " rfid_tag VARCHAR(255)," .
                " PRIMARY KEY ( user_id ));";
        require_once(ABSPATH . "wp_admin/includes/upgrade.php");
        dbDelta($sql);
        register_activation_hook( __FILE__, 'db_CreateRoleTableIfNotExists' );
    }
}

function db_GetMemberDataForUserId($userId) {
    global $wpdb;
    $tableName = db_GetMemberTableName();
    $user = strip_tags(addslashes($userId));
    db_CreateRoleTableIfNotExists();
    return $wpdb->get_row("SELECT * FROM $tableName WHERE user = $user");
}

function db_GetAllUsersWithRole($roleId) {
    global $wpdb;
    $memberTableName = db_GetMemberTableName();
    $roleTableName = db_GetRoleTableName();
    $dbRoleId = intval($roleId);
    db_CreateRoleTableIfNotExists();
    $sql = "SELECT * FROM $memberTableName LEFT OUTER JOIN $roleTableName" . 
            " ON $memberTableName.user = $roleTableName.user" .
            " WHERE $roleTableName.role = $dbRoleId";
    return $wpdb->get_results($sql, ARRAY_A);
}

function db_InsertOrUpdateMember($userId, $paidThrough, $rfidTag) {
    global $wpdb;
    $tableName = db_GetMemberTableName();
    
    $dbUser = strip_tags(addslashes($userId));
    $dbPaidThrough = strip_tags(addslashes($paidThrough));
    $dbRfidTag = strip_tags(addslashes($rfidTag));
    $memberRecord = db_GetMemberDataForUserId($userId);
    db_CreateRoleTableIfNotExists();
    if($memberRecord != null) {
        $wpdb->update($tableName, 
            array('paid_through' => $dbPaidThrough, 'rfid_tag' => $dbRfidTag),
            array('user' => $dbUser));
    } else {
        $wpdb->insert($tableName, array('user' => $dbUser, 'paid_through' => $dbPaidThrough, 'rfid_tag' => $dbRfidTag));
    }
}

?>