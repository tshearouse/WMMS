<?php
function db_GetRoleTableName() {
    return $wpdb->prefix . "wmms_user_roles"
}

function db_GetMemberTableName() {
    return $wpdb->prefix . "wmms_member_data";
}
?>