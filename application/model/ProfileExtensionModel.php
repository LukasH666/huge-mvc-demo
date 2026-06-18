<?php

class ProfileExtensionModel
{
    public static function getProfileByUserId($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

$sql = "SELECT user_id, bio, location, hobby, birthday, profile_picture_filename, cover_picture_filename, updated_at
        FROM user_profiles
        WHERE user_id = :user_id
        LIMIT 1";

        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));

        $profile = $query->fetch();

        if ($profile) {
            return $profile;
        }

        $emptyProfile = new stdClass();
        $emptyProfile->user_id = $user_id;
        $emptyProfile->bio = '';
        $emptyProfile->location = '';
        $emptyProfile->hobby = '';
        $emptyProfile->birthday = '';
        $emptyProfile->profile_picture_filename = '';
        $emptyProfile->cover_picture_filename = '';
        $emptyProfile->updated_at = '';

        return $emptyProfile;
    }

    public static function saveProfile($user_id, $bio, $location, $hobby, $birthday)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO user_profiles (user_id, bio, location, hobby, birthday)
                VALUES (:user_id, :bio, :location, :hobby, :birthday)
                ON DUPLICATE KEY UPDATE
                    bio = :bio_update,
                    location = :location_update,
                    hobby = :hobby_update,
                    birthday = :birthday_update";

        $query = $database->prepare($sql);

        return $query->execute(array(
            ':user_id' => $user_id,
            ':bio' => $bio,
            ':location' => $location,
            ':hobby' => $hobby,
            ':birthday' => $birthday,
            ':bio_update' => $bio,
            ':location_update' => $location,
            ':hobby_update' => $hobby,
            ':birthday_update' => $birthday
        ));
    }

    public static function saveProfilePictureFilename($user_id, $filename)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "INSERT INTO user_profiles (user_id, profile_picture_filename)
            VALUES (:user_id, :profile_picture_filename)
            ON DUPLICATE KEY UPDATE
                profile_picture_filename = :profile_picture_filename_update";

    $query = $database->prepare($sql);

    return $query->execute(array(
        ':user_id' => $user_id,
        ':profile_picture_filename' => $filename,
        ':profile_picture_filename_update' => $filename
    ));
}

public static function saveCoverPictureFilename($user_id, $filename)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "INSERT INTO user_profiles (user_id, cover_picture_filename)
            VALUES (:user_id, :cover_picture_filename)
            ON DUPLICATE KEY UPDATE
                cover_picture_filename = :cover_picture_filename_update";

    $query = $database->prepare($sql);

    return $query->execute(array(
        ':user_id' => $user_id,
        ':cover_picture_filename' => $filename,
        ':cover_picture_filename_update' => $filename
    ));
}

}