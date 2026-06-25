<?php

class MessageModel
{
    public static function getUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL GetMessengerUsers(:user_id)");
        $query->execute(array(
            ':user_id' => Session::get('user_id')
        ));

        $result = $query->fetchAll();
        $query->closeCursor();

        return $result;
    }

public static function getMessages($partner_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "SELECT message_id,
                   sender_id,
                   receiver_id,
                   message_text,
                   file_filename,
                   file_original_name,
                   file_mime_type,
                   is_read,
                   created_at
            FROM messages
            WHERE (sender_id = :current_user_id AND receiver_id = :partner_id)
               OR (sender_id = :partner_id AND receiver_id = :current_user_id)
            ORDER BY created_at ASC";

    $query = $database->prepare($sql);
    $query->execute(array(
        ':current_user_id' => Session::get('user_id'),
        ':partner_id' => $partner_id
    ));

    return $query->fetchAll();
}

public static function sendMessage($receiver_id, $message_text, $file_filename = null, $file_original_name = null, $file_mime_type = null)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "INSERT INTO messages 
            (sender_id, receiver_id, message_text, file_filename, file_original_name, file_mime_type)
            VALUES 
            (:sender_id, :receiver_id, :message_text, :file_filename, :file_original_name, :file_mime_type)";

    $query = $database->prepare($sql);

    return $query->execute(array(
        ':sender_id' => Session::get('user_id'),
        ':receiver_id' => $receiver_id,
        ':message_text' => $message_text,
        ':file_filename' => $file_filename,
        ':file_original_name' => $file_original_name,
        ':file_mime_type' => $file_mime_type
    ));
}

    public static function countUnread()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL CountUnreadMessages(:user_id)");
        $query->execute(array(
            ':user_id' => Session::get('user_id')
        ));

        $result = $query->fetch();
        $query->closeCursor();

        return $result->amount;
    }

    public static function markAsRead($partner_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL MarkMessagesAsRead(:partner, :me)");
        $query->execute(array(
            ':partner' => $partner_id,
            ':me' => Session::get('user_id')
        ));

        $query->closeCursor();
    }

    public static function countUnreadFromUser($sender_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL CountUnreadFromUser(:sender_id, :current_user_id)");
        $query->execute(array(
            ':sender_id' => $sender_id,
            ':current_user_id' => Session::get('user_id')
        ));

        $result = $query->fetch();
        $query->closeCursor();

        return $result->amount;
    }

    public static function createGroup($group_name, $member_ids)
    {
        if (trim($group_name) === '') {
            return false;
        }

        if (!is_array($member_ids)) {
            $member_ids = array();
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL CreateMessageGroup(:group_name, :created_by)");
        $query->execute(array(
            ':group_name' => $group_name,
            ':created_by' => Session::get('user_id')
        ));

        $result = $query->fetch();
        $query->closeCursor();

        if (!$result) {
            return false;
        }

        $group_id = $result->group_id;

        self::addUserToGroup($group_id, Session::get('user_id'));

        foreach ($member_ids as $member_id) {
            self::addUserToGroup($group_id, $member_id);
        }

        return true;
    }

    public static function addUserToGroup($group_id, $user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL AddUserToMessageGroup(:group_id, :user_id)");
        $query->execute(array(
            ':group_id' => $group_id,
            ':user_id' => $user_id
        ));

        $query->closeCursor();
    }

    public static function getGroupsOfCurrentUser()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL GetGroupsOfUser(:user_id)");
        $query->execute(array(
            ':user_id' => Session::get('user_id')
        ));

        $result = $query->fetchAll();
        $query->closeCursor();

        return $result;
    }

    public static function getGroupMessages($group_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL GetGroupMessages(:group_id)");
        $query->execute(array(
            ':group_id' => $group_id
        ));

        $result = $query->fetchAll();
        $query->closeCursor();

        return $result;
    }

    public static function sendGroupMessage($group_id, $message_text)
    {
        if (trim($message_text) === '') {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL SendGroupMessage(:group_id, :sender_id, :message_text)");
        $query->execute(array(
            ':group_id' => $group_id,
            ':sender_id' => Session::get('user_id'),
            ':message_text' => $message_text
        ));

        $query->closeCursor();

        return true;
    }

    public static function countUnreadGroupMessages($group_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL CountUnreadGroupMessages(:group_id, :current_user_id)");
        $query->execute(array(
            ':group_id' => $group_id,
            ':current_user_id' => Session::get('user_id')
        ));

        $result = $query->fetch();
        $query->closeCursor();

        return $result->amount;
    }

    public static function markGroupMessagesAsRead($group_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL MarkGroupMessagesAsRead(:group_id, :current_user_id)");
        $query->execute(array(
            ':group_id' => $group_id,
            ':current_user_id' => Session::get('user_id')
        ));

        $query->closeCursor();
    }

    public static function getMessageById($message_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "SELECT message_id,
                   sender_id,
                   receiver_id,
                   message_text,
                   file_filename,
                   file_original_name,
                   file_mime_type,
                   is_read,
                   created_at
            FROM messages
            WHERE message_id = :message_id
            LIMIT 1";

    $query = $database->prepare($sql);
    $query->execute(array(
        ':message_id' => $message_id
    ));

    return $query->fetch();
}
}