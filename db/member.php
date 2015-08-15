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

function db_GetMemberTableName() {
    return $wpdb->prefix . "wmms_member_data";
}
?>