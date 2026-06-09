DELIMITER $$

DROP PROCEDURE IF EXISTS GetMessengerUsers $$
CREATE PROCEDURE GetMessengerUsers(IN p_user_id INT)
BEGIN
    SELECT user_id, user_name
    FROM users
    WHERE user_id != p_user_id
      AND user_active = 1
      AND user_deleted = 0
    ORDER BY user_name;
END $$

DROP PROCEDURE IF EXISTS GetMessages $$
CREATE PROCEDURE GetMessages(IN p_me INT, IN p_partner INT)
BEGIN
    SELECT *
    FROM messages
    WHERE (sender_id = p_me AND receiver_id = p_partner)
       OR (sender_id = p_partner AND receiver_id = p_me)
    ORDER BY created_at ASC;
END $$

DROP PROCEDURE IF EXISTS SendMessage $$
CREATE PROCEDURE SendMessage(
    IN p_sender_id INT,
    IN p_receiver_id INT,
    IN p_message_text TEXT
)
BEGIN
    INSERT INTO messages (sender_id, receiver_id, message_text)
    VALUES (p_sender_id, p_receiver_id, p_message_text);
END $$

DROP PROCEDURE IF EXISTS CountUnreadMessages $$
CREATE PROCEDURE CountUnreadMessages(IN p_user_id INT)
BEGIN
    SELECT COUNT(*) AS amount
    FROM messages
    WHERE receiver_id = p_user_id
      AND is_read = 0;
END $$

DROP PROCEDURE IF EXISTS MarkMessagesAsRead $$
CREATE PROCEDURE MarkMessagesAsRead(IN p_partner INT, IN p_me INT)
BEGIN
    UPDATE messages
    SET is_read = 1
    WHERE sender_id = p_partner
      AND receiver_id = p_me;
END $$

DROP PROCEDURE IF EXISTS CountUnreadFromUser $$
CREATE PROCEDURE CountUnreadFromUser(IN p_sender_id INT, IN p_current_user_id INT)
BEGIN
    SELECT COUNT(*) AS amount
    FROM messages
    WHERE sender_id = p_sender_id
      AND receiver_id = p_current_user_id
      AND is_read = 0;
END $$

DROP PROCEDURE IF EXISTS CreateMessageGroup $$
CREATE PROCEDURE CreateMessageGroup(
    IN p_group_name VARCHAR(100),
    IN p_created_by INT
)
BEGIN
    INSERT INTO message_groups (group_name, created_by)
    VALUES (p_group_name, p_created_by);

    SELECT LAST_INSERT_ID() AS group_id;
END $$

DROP PROCEDURE IF EXISTS AddUserToMessageGroup $$
CREATE PROCEDURE AddUserToMessageGroup(IN p_group_id INT, IN p_user_id INT)
BEGIN
    INSERT IGNORE INTO message_group_members (group_id, user_id)
    VALUES (p_group_id, p_user_id);
END $$

DROP PROCEDURE IF EXISTS GetGroupsOfUser $$
CREATE PROCEDURE GetGroupsOfUser(IN p_user_id INT)
BEGIN
    SELECT message_groups.*
    FROM message_groups
    JOIN message_group_members
      ON message_groups.group_id = message_group_members.group_id
    WHERE message_group_members.user_id = p_user_id
    ORDER BY message_groups.group_name;
END $$

DROP PROCEDURE IF EXISTS GetGroupMessages $$
CREATE PROCEDURE GetGroupMessages(IN p_group_id INT)
BEGIN
    SELECT group_messages.*, users.user_name
    FROM group_messages
    JOIN users ON group_messages.sender_id = users.user_id
    WHERE group_messages.group_id = p_group_id
    ORDER BY group_messages.created_at ASC;
END $$

DROP PROCEDURE IF EXISTS SendGroupMessage $$
CREATE PROCEDURE SendGroupMessage(
    IN p_group_id INT,
    IN p_sender_id INT,
    IN p_message_text TEXT
)
BEGIN
    INSERT INTO group_messages (group_id, sender_id, message_text)
    VALUES (p_group_id, p_sender_id, p_message_text);
END $$

DROP PROCEDURE IF EXISTS CountUnreadGroupMessages $$
CREATE PROCEDURE CountUnreadGroupMessages(IN p_group_id INT, IN p_current_user_id INT)
BEGIN
    SELECT COUNT(*) AS amount
    FROM group_messages
    WHERE group_id = p_group_id
      AND sender_id != p_current_user_id
      AND group_message_id NOT IN (
          SELECT group_message_id
          FROM group_message_reads
          WHERE user_id = p_current_user_id
      );
END $$

DROP PROCEDURE IF EXISTS MarkGroupMessagesAsRead $$
CREATE PROCEDURE MarkGroupMessagesAsRead(IN p_group_id INT, IN p_current_user_id INT)
BEGIN
    INSERT IGNORE INTO group_message_reads (group_message_id, user_id)
    SELECT group_message_id, p_current_user_id
    FROM group_messages
    WHERE group_id = p_group_id
      AND sender_id != p_current_user_id;
END $$

DELIMITER ;