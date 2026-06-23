<?php

class FriendModel
{
    public static function getAllOtherUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, user_name, user_email
                FROM users
                WHERE user_id != :user_id
                  AND user_deleted = 0
                ORDER BY user_name ASC";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_id' => Session::get('user_id')
        ));

        return $query->fetchAll();
    }

    public static function getFriendshipStatus($other_user_id)
    {
        $currentUserId = Session::get('user_id');

        if ($currentUserId == $other_user_id) {
            return 'self';
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT request_id, sender_id, receiver_id, status
                FROM friend_requests
                WHERE (
                    (sender_id = :me1 AND receiver_id = :other1)
                    OR
                    (sender_id = :other2 AND receiver_id = :me2)
                )
                ORDER BY request_id DESC
                LIMIT 1";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':me1' => $currentUserId,
            ':other1' => $other_user_id,
            ':other2' => $other_user_id,
            ':me2' => $currentUserId
        ));

        $request = $query->fetch();

        if (!$request) {
            return 'none';
        }

        if ($request->status == 'accepted') {
            return 'friends';
        }

        if ($request->status == 'pending' && $request->sender_id == $currentUserId) {
            return 'request_sent';
        }

        if ($request->status == 'pending' && $request->receiver_id == $currentUserId) {
            return 'request_received';
        }

        return 'none';
    }

    public static function sendRequest($receiver_id)
    {
        $sender_id = Session::get('user_id');

        if (!$receiver_id || $sender_id == $receiver_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT request_id, status
                FROM friend_requests
                WHERE (
                    (sender_id = :sender1 AND receiver_id = :receiver1)
                    OR
                    (sender_id = :receiver2 AND receiver_id = :sender2)
                )
                ORDER BY request_id DESC
                LIMIT 1";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':sender1' => $sender_id,
            ':receiver1' => $receiver_id,
            ':receiver2' => $receiver_id,
            ':sender2' => $sender_id
        ));

        $existingRequest = $query->fetch();

        if ($existingRequest) {
            if ($existingRequest->status == 'pending' || $existingRequest->status == 'accepted') {
                return false;
            }

            $updateSql = "UPDATE friend_requests
                          SET sender_id = :sender_id,
                              receiver_id = :receiver_id,
                              status = 'pending'
                          WHERE request_id = :request_id";

            $updateQuery = $database->prepare($updateSql);

            return $updateQuery->execute(array(
                ':sender_id' => $sender_id,
                ':receiver_id' => $receiver_id,
                ':request_id' => $existingRequest->request_id
            ));
        }

        $insertSql = "INSERT INTO friend_requests (sender_id, receiver_id, status)
                      VALUES (:sender_id, :receiver_id, 'pending')";

        $insertQuery = $database->prepare($insertSql);

        return $insertQuery->execute(array(
            ':sender_id' => $sender_id,
            ':receiver_id' => $receiver_id
        ));
    }

    public static function acceptRequest($request_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE friend_requests
                SET status = 'accepted'
                WHERE request_id = :request_id
                  AND receiver_id = :receiver_id
                  AND status = 'pending'
                LIMIT 1";

        $query = $database->prepare($sql);

        return $query->execute(array(
            ':request_id' => $request_id,
            ':receiver_id' => Session::get('user_id')
        ));
    }

    public static function declineRequest($request_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE friend_requests
                SET status = 'declined'
                WHERE request_id = :request_id
                  AND receiver_id = :receiver_id
                  AND status = 'pending'
                LIMIT 1";

        $query = $database->prepare($sql);

        return $query->execute(array(
            ':request_id' => $request_id,
            ':receiver_id' => Session::get('user_id')
        ));
    }

    public static function removeFriend($other_user_id)
    {
        $currentUserId = Session::get('user_id');

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM friend_requests
                WHERE status = 'accepted'
                  AND (
                    (sender_id = :me1 AND receiver_id = :other1)
                    OR
                    (sender_id = :other2 AND receiver_id = :me2)
                  )
                LIMIT 1";

        $query = $database->prepare($sql);

        return $query->execute(array(
            ':me1' => $currentUserId,
            ':other1' => $other_user_id,
            ':other2' => $other_user_id,
            ':me2' => $currentUserId
        ));
    }

    public static function getPendingRequests()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT friend_requests.request_id,
                       friend_requests.sender_id,
                       users.user_name,
                       users.user_email
                FROM friend_requests
                INNER JOIN users
                    ON friend_requests.sender_id = users.user_id
                WHERE friend_requests.receiver_id = :user_id
                  AND friend_requests.status = 'pending'
                ORDER BY friend_requests.created_at DESC";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_id' => Session::get('user_id')
        ));

        return $query->fetchAll();
    }

    public static function getSentRequests()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT friend_requests.request_id,
                       friend_requests.receiver_id,
                       users.user_name,
                       users.user_email
                FROM friend_requests
                INNER JOIN users
                    ON friend_requests.receiver_id = users.user_id
                WHERE friend_requests.sender_id = :user_id
                  AND friend_requests.status = 'pending'
                ORDER BY friend_requests.created_at DESC";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_id' => Session::get('user_id')
        ));

        return $query->fetchAll();
    }

    public static function getFriends()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT friend_requests.request_id,
                       users.user_id,
                       users.user_name,
                       users.user_email
                FROM friend_requests
                INNER JOIN users
                    ON users.user_id =
                        CASE
                            WHEN friend_requests.sender_id = :me1
                            THEN friend_requests.receiver_id
                            ELSE friend_requests.sender_id
                        END
                WHERE (friend_requests.sender_id = :me2 OR friend_requests.receiver_id = :me3)
                  AND friend_requests.status = 'accepted'
                ORDER BY users.user_name ASC";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':me1' => Session::get('user_id'),
            ':me2' => Session::get('user_id'),
            ':me3' => Session::get('user_id')
        ));

        return $query->fetchAll();
    }
    public static function countPendingRequests()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT COUNT(*) AS amount
                FROM friend_requests
                WHERE receiver_id = :user_id
                  AND status = 'pending'";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_id' => Session::get('user_id')
        ));

        $result = $query->fetch();

        return $result ? (int)$result->amount : 0;
    }

}