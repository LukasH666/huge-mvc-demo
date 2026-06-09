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

        $query = $database->prepare("CALL GetMessages(:me, :partner)");
        $query->execute(array(
            ':me' => Session::get('user_id'),
            ':partner' => $partner_id
        ));

        $result = $query->fetchAll();
        $query->closeCursor();

        return $result;
    }

    public static function sendMessage($receiver_id, $message_text)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL SendMessage(:sender_id, :receiver_id, :message_text)");
        $query->execute(array(
            ':sender_id' => Session::get('user_id'),
            ':receiver_id' => $receiver_id,
            ':message_text' => $message_text
        ));

        $query->closeCursor();
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
}