<?php

class ProfileController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This method controls what happens when you move to /overview/index in your app.
     * Shows a list of all users.
     */
public function index()
{
    $this->View->render('profile/index', array(
        'users' => UserRoleModel::getAllUsersWithGroups()
    ));
}

    /**
     * This method controls what happens when you move to /overview/showProfile in your app.
     * Shows the (public) details of the selected user.
     * @param $user_id int id the the user
     */
public function showProfile($user_id)
{
    Auth::checkAuthentication();

    if (!isset($user_id)) {
        Redirect::to('profile/index');
        return;
    }

    $user = UserModel::getPublicProfileOfUser($user_id);
    $extendedProfile = ProfileExtensionModel::getProfileByUserId($user_id);

    $this->View->render('profile/showProfile', array(
        'user' => $user,
        'profile' => $extendedProfile,
        'posts' => ProfilePostModel::getPostsByUserId($user_id)
    ));
}

    public function edit()
{
    Auth::checkAuthentication();

    $userId = Session::get('user_id');

    $this->View->render('profile/edit', array(
        'profile' => ProfileExtensionModel::getProfileByUserId($userId),
        'posts' => ProfilePostModel::getPostsByUserId($userId)
    ));
}

public function save()
{
    Auth::checkAuthentication();

    $userId = Session::get('user_id');

    $bio = strip_tags(trim(Request::post('bio')));
    $location = strip_tags(trim(Request::post('location')));
    $hobby = strip_tags(trim(Request::post('hobby')));
    $birthday = Request::post('birthday');

    if (empty($birthday)) {
        $birthday = null;
    }

    ProfileExtensionModel::saveProfile($userId, $bio, $location, $hobby, $birthday);

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['profile_picture']['size'] <= 3 * 1024 * 1024) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['profile_picture']['tmp_name']);

            $allowed = array('image/jpeg', 'image/png', 'image/gif');

            if (in_array($mime, $allowed)) {
                if ($mime === 'image/jpeg') {
                    $extension = 'jpg';
                } elseif ($mime === 'image/png') {
                    $extension = 'png';
                } elseif ($mime === 'image/gif') {
                    $extension = 'gif';
                }

                $profilePictureFolder = dirname(__DIR__, 2) . '/profilepictures/' . $userId;

                if (!is_dir($profilePictureFolder)) {
                    mkdir($profilePictureFolder, 0777, true);
                }

                foreach (array('jpg', 'png', 'gif') as $oldExtension) {
                    $oldFile = $profilePictureFolder . '/profile_' . $userId . '.' . $oldExtension;

                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $safeName = 'profile_' . $userId . '.' . $extension;
                $target = $profilePictureFolder . '/' . $safeName;

                move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target);

                ProfileExtensionModel::saveProfilePictureFilename($userId, $safeName);
            }
        }
    }
    if (isset($_FILES['cover_picture']) && $_FILES['cover_picture']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['cover_picture']['size'] <= 5 * 1024 * 1024) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['cover_picture']['tmp_name']);

        $allowed = array('image/jpeg', 'image/png', 'image/gif');

        if (in_array($mime, $allowed)) {
            if ($mime === 'image/jpeg') {
                $extension = 'jpg';
            } elseif ($mime === 'image/png') {
                $extension = 'png';
            } elseif ($mime === 'image/gif') {
                $extension = 'gif';
            }

            $profilePictureFolder = dirname(__DIR__, 2) . '/profilepictures/' . $userId;

            if (!is_dir($profilePictureFolder)) {
                mkdir($profilePictureFolder, 0777, true);
            }

            foreach (array('jpg', 'png', 'gif') as $oldExtension) {
                $oldFile = $profilePictureFolder . '/cover_' . $userId . '.' . $oldExtension;

                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $safeName = 'cover_' . $userId . '.' . $extension;
            $target = $profilePictureFolder . '/' . $safeName;

            move_uploaded_file($_FILES['cover_picture']['tmp_name'], $target);

            ProfileExtensionModel::saveCoverPictureFilename($userId, $safeName);
        }
    }
}
    Redirect::to('profile/edit');
}

public function picture($user_id)
{
    Auth::checkAuthentication();

    $user_id = (int)$user_id;

    if ($user_id <= 0) {
        exit;
    }

    $profile = ProfileExtensionModel::getProfileByUserId($user_id);

    if (empty($profile->profile_picture_filename)) {
        exit;
    }

    $safeName = basename($profile->profile_picture_filename);
    $profilePictureFolder = dirname(__DIR__, 2) . '/profilepictures/' . $user_id;
    $filePath = $profilePictureFolder . '/' . $safeName;

    if (!file_exists($filePath)) {
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($filePath);

    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="' . $safeName . '"');
    header('Content-Length: ' . filesize($filePath));

    readfile($filePath);
    exit;
}

public function cover($user_id)
{
    Auth::checkAuthentication();

    $user_id = (int)$user_id;

    if ($user_id <= 0) {
        exit;
    }

    $profile = ProfileExtensionModel::getProfileByUserId($user_id);

    if (empty($profile->cover_picture_filename)) {
        exit;
    }

    $safeName = basename($profile->cover_picture_filename);
    $profilePictureFolder = dirname(__DIR__, 2) . '/profilepictures/' . $user_id;
    $filePath = $profilePictureFolder . '/' . $safeName;

    if (!file_exists($filePath)) {
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($filePath);

    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="' . $safeName . '"');
    header('Content-Length: ' . filesize($filePath));

    readfile($filePath);
    exit;
}

}
