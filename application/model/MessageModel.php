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
}