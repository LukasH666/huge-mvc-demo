<?php

class MessageModel
{
    public static function getUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, user_name
                FROM users
                WHERE user_id != :user_id
                AND user_active = 1
                AND user_deleted = 0
                ORDER BY user_name";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_id' => Session::get('user_id')
        ));

        return $query->fetchAll();
    }

    public static function getMessages($partner_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT *
                FROM messages
                WHERE
                    (sender_id = :me AND receiver_id = :partner)
                    OR
                    (sender_id = :partner AND receiver_id = :me)
                ORDER BY created_at ASC";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':me' => Session::get('user_id'),
            ':partner' => $partner_id
        ));

        return $query->fetchAll();
    }

    public static function sendMessage($receiver_id, $message_text)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO messages (sender_id, receiver_id, message_text)
                VALUES (:sender_id, :receiver_id, :message_text)";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':sender_id' => Session::get('user_id'),
            ':receiver_id' => $receiver_id,
            ':message_text' => $message_text
        ));
    }

    public static function countUnread()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT COUNT(*) AS amount
                FROM messages
                WHERE receiver_id = :user_id
                AND is_read = 0";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_id' => Session::get('user_id')
        ));

        return $query->fetch()->amount;
    }

    public static function markAsRead($partner_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE messages
                SET is_read = 1
                WHERE sender_id = :partner
                AND receiver_id = :me";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':partner' => $partner_id,
            ':me' => Session::get('user_id')
        ));
    }

    public static function countUnreadFromUser($sender_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "SELECT COUNT(*) AS amount
            FROM messages
            WHERE sender_id = :sender_id
            AND receiver_id = :current_user_id
            AND is_read = 0";

    $query = $database->prepare($sql);
    $query->execute(array(
        ':sender_id' => $sender_id,
        ':current_user_id' => Session::get('user_id')
    ));

    return $query->fetch()->amount;
}

public static function createGroup($group_name, $member_ids)
{
    if (trim($group_name) === '') {
        return false;
    }

    $database = DatabaseFactory::getFactory()->getConnection();

    $query = $database->prepare("
        INSERT INTO message_groups (group_name, created_by)
        VALUES (:group_name, :created_by)
    ");

    $query->execute(array(
        ':group_name' => $group_name,
        ':created_by' => Session::get('user_id')
    ));

    $group_id = $database->lastInsertId();

    self::addUserToGroup($group_id, Session::get('user_id'));

    foreach ($member_ids as $member_id) {
        self::addUserToGroup($group_id, $member_id);
    }

    return true;
}

public static function addUserToGroup($group_id, $user_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $query = $database->prepare("
        INSERT IGNORE INTO message_group_members (group_id, user_id)
        VALUES (:group_id, :user_id)
    ");

    $query->execute(array(
        ':group_id' => $group_id,
        ':user_id' => $user_id
    ));
}

public static function getGroupsOfCurrentUser()
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $query = $database->prepare("
        SELECT message_groups.*
        FROM message_groups
        JOIN message_group_members
        ON message_groups.group_id = message_group_members.group_id
        WHERE message_group_members.user_id = :user_id
        ORDER BY message_groups.group_name
    ");

    $query->execute(array(
        ':user_id' => Session::get('user_id')
    ));

    return $query->fetchAll();
}

public static function getGroupMessages($group_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $query = $database->prepare("
        SELECT group_messages.*, users.user_name
        FROM group_messages
        JOIN users ON group_messages.sender_id = users.user_id
        WHERE group_messages.group_id = :group_id
        ORDER BY group_messages.created_at ASC
    ");

    $query->execute(array(
        ':group_id' => $group_id
    ));

    return $query->fetchAll();
}

public static function sendGroupMessage($group_id, $message_text)
{
    if (trim($message_text) === '') {
        return false;
    }

    $database = DatabaseFactory::getFactory()->getConnection();

    $query = $database->prepare("
        INSERT INTO group_messages (group_id, sender_id, message_text)
        VALUES (:group_id, :sender_id, :message_text)
    ");

    $query->execute(array(
        ':group_id' => $group_id,
        ':sender_id' => Session::get('user_id'),
        ':message_text' => $message_text
    ));

    return $query->rowCount() == 1;
}

public static function countUnreadGroupMessages($group_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "SELECT COUNT(*) AS amount
            FROM group_messages
            WHERE group_id = :group_id
            AND sender_id != :current_user_id
            AND group_message_id NOT IN (
                SELECT group_message_id
                FROM group_message_reads
                WHERE user_id = :current_user_id
            )";

    $query = $database->prepare($sql);
    $query->execute(array(
        ':group_id' => $group_id,
        ':current_user_id' => Session::get('user_id')
    ));

    return $query->fetch()->amount;
}

public static function markGroupMessagesAsRead($group_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "INSERT IGNORE INTO group_message_reads (group_message_id, user_id)
            SELECT group_message_id, :current_user_id
            FROM group_messages
            WHERE group_id = :group_id
            AND sender_id != :current_user_id";

    $query = $database->prepare($sql);
    $query->execute(array(
        ':group_id' => $group_id,
        ':current_user_id' => Session::get('user_id')
    ));
}

}