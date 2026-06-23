<?php

class ProfilePostModel
{
    public static function createPost($user_id, $image_filename, $caption)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO profile_posts (user_id, image_filename, caption)
                VALUES (:user_id, :image_filename, :caption)";

        $query = $database->prepare($sql);

        return $query->execute(array(
            ':user_id' => $user_id,
            ':image_filename' => $image_filename,
            ':caption' => $caption
        ));
    }

    public static function getPostsByUserId($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT post_id, user_id, image_filename, caption, created_at
                FROM profile_posts
                WHERE user_id = :user_id
                ORDER BY created_at DESC";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':user_id' => $user_id
        ));

        return $query->fetchAll();
    }

    public static function getPostById($post_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT post_id, user_id, image_filename, caption, created_at
                FROM profile_posts
                WHERE post_id = :post_id
                LIMIT 1";

        $query = $database->prepare($sql);
        $query->execute(array(
            ':post_id' => $post_id
        ));

        return $query->fetch();
    }

public static function deletePost($post_id)
{
    $post = self::getPostById($post_id);

    if (!$post) {
        return false;
    }

    if ($post->user_id != Session::get('user_id')) {
        return false;
    }

    $database = DatabaseFactory::getFactory()->getConnection();

    $deleteCommentsSql = "DELETE FROM profile_post_comments
                          WHERE post_id = :post_id";

    $deleteCommentsQuery = $database->prepare($deleteCommentsSql);
    $deleteCommentsQuery->execute(array(
        ':post_id' => $post_id
    ));

    $sql = "DELETE FROM profile_posts
            WHERE post_id = :post_id
              AND user_id = :user_id
            LIMIT 1";

    $query = $database->prepare($sql);

    return $query->execute(array(
        ':post_id' => $post_id,
        ':user_id' => Session::get('user_id')
    ));
}

public static function getCommentsByPostId($post_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "SELECT profile_post_comments.comment_id,
                   profile_post_comments.post_id,
                   profile_post_comments.user_id,
                   profile_post_comments.comment_text,
                   profile_post_comments.created_at,
                   users.user_name,
                   user_profiles.profile_picture_filename
            FROM profile_post_comments
            INNER JOIN users
                ON profile_post_comments.user_id = users.user_id
            LEFT JOIN user_profiles
                ON profile_post_comments.user_id = user_profiles.user_id
            WHERE profile_post_comments.post_id = :post_id
            ORDER BY profile_post_comments.created_at ASC";

    $query = $database->prepare($sql);
    $query->execute(array(
        ':post_id' => $post_id
    ));

    return $query->fetchAll();
}

public static function createComment($post_id, $user_id, $comment_text)
{
    if (empty($comment_text)) {
        return false;
    }

    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "INSERT INTO profile_post_comments (post_id, user_id, comment_text)
            VALUES (:post_id, :user_id, :comment_text)";

    $query = $database->prepare($sql);

    return $query->execute(array(
        ':post_id' => $post_id,
        ':user_id' => $user_id,
        ':comment_text' => $comment_text
    ));
}

public static function getCommentById($comment_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "SELECT comment_id, post_id, user_id, comment_text, created_at
            FROM profile_post_comments
            WHERE comment_id = :comment_id
            LIMIT 1";

    $query = $database->prepare($sql);
    $query->execute(array(
        ':comment_id' => $comment_id
    ));

    return $query->fetch();
}

public static function deleteComment($comment_id)
{
    $comment = self::getCommentById($comment_id);

    if (!$comment) {
        return false;
    }

    $post = self::getPostById($comment->post_id);

    if (!$post) {
        return false;
    }

    $currentUserId = Session::get('user_id');

    if ($comment->user_id != $currentUserId && $post->user_id != $currentUserId) {
        return false;
    }

    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "DELETE FROM profile_post_comments
            WHERE comment_id = :comment_id
            LIMIT 1";

    $query = $database->prepare($sql);

    return $query->execute(array(
        ':comment_id' => $comment_id
    ));
}
}