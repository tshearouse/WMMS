<?php
global $wpdb;

function db_CreateRoleTableIfNotExists() {
    $tableName = db_GetMemberTableName();
    if($wpdb->get_var("show tables like $tablename") != $tableName) {
        $sql = "CREATE TABLE $tableName(" .
                " user_id VARCHAR(60) NOT NULL," .
                " paid_through DATE," .
                " rfid_tag VARCHAR(255)," .
                " PRIMARY KEY ( user_id ));"
        require_once(ABSPATH . "wp_admin/includes/upgrade.php");
        dbDelta($sql);
        register_activation_hook( __FILE__, 'db_CreateRoleTableIfNotExists' );
    }
}

function db_GetMemberDataForUserId($userId) {
    $tableName = db_GetMemberTableName();
    $user = strip_tags(addslashes($userId));
    return $wpdb->get_row("SELECT * FROM $tableName WHERE user_id = $user");
}

function db_InsertOrUpdateMember($userId, $paidThrough, $rfidTag) {
    $tableName = db_GetMemberTableName();
    
    $dbUser = strip_tags(addslashes($userId));
    $dbPaidThrough = strip_tags(addslashes($paidThrough));
    $dbRfidTag = strip_tags(addslashes($rfid_tag));
    $memberRecord = db_GetMemberDataForUserId($userId);
    if($memberRecord != null) {
        $wpdb->update($tableName, 
            array('paid_through' => $dbPaidThrough, 'rfid_tag' => $dbRfidTag),
            array('user_id' => $dbUser));
    } else {
        $wpdb->insert($tableName, array('user_id' => $dbUser, 'paid_through' => $dbPaidThrough, 'rfid_tag' => $dbRfidTag));
    }
}

function db_GetMemberTableName() {
    return $wpdb->prefix . "wmms_member_data";
}
?>