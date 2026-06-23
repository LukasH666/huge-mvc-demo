<?php

class ProfilePostController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    public function create()
    {
        $userId = Session::get('user_id');

        if (!isset($_FILES['post_image']) || $_FILES['post_image']['error'] !== UPLOAD_ERR_OK) {
            Redirect::to('profile/edit');
            return;
        }

        if ($_FILES['post_image']['size'] > 5 * 1024 * 1024) {
            Redirect::to('profile/edit');
            return;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['post_image']['tmp_name']);

        $allowed = array('image/jpeg', 'image/png', 'image/gif');

        if (!in_array($mime, $allowed)) {
            Redirect::to('profile/edit');
            return;
        }

        if ($mime === 'image/jpeg') {
            $extension = 'jpg';
        } elseif ($mime === 'image/png') {
            $extension = 'png';
        } elseif ($mime === 'image/gif') {
            $extension = 'gif';
        } else {
            Redirect::to('profile/edit');
            return;
        }

        $postFolder = dirname(__DIR__, 2) . '/profileposts/' . $userId;

        if (!is_dir($postFolder)) {
            mkdir($postFolder, 0777, true);
        }

        $safeName = 'post_' . $userId . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
        $target = $postFolder . '/' . $safeName;

        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target)) {
            $caption = strip_tags(trim(Request::post('caption')));
            ProfilePostModel::createPost($userId, $safeName, $caption);
        }

        Redirect::to('profile/edit');
    }

    public function image($post_id)
    {
        $post = ProfilePostModel::getPostById($post_id);

        if (!$post) {
            exit;
        }

        $safeName = basename($post->image_filename);
        $filePath = dirname(__DIR__, 2) . '/profileposts/' . $post->user_id . '/' . $safeName;

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

    public function delete($post_id)
    {
        $post = ProfilePostModel::getPostById($post_id);

        if ($post && $post->user_id == Session::get('user_id')) {
            $safeName = basename($post->image_filename);
            $filePath = dirname(__DIR__, 2) . '/profileposts/' . $post->user_id . '/' . $safeName;

            ProfilePostModel::deletePost($post_id);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        Redirect::to('profile/edit');
    }

    public function show($post_id)
{
    Auth::checkAuthentication();

    $post = ProfilePostModel::getPostById($post_id);

    if (!$post) {
        Redirect::to('profile/index');
        return;
    }

    $user = UserModel::getPublicProfileOfUser($post->user_id);
    $profile = ProfileExtensionModel::getProfileByUserId($post->user_id);

    $this->View->render('profilePost/show', array(
        'post' => $post,
        'user' => $user,
        'profile' => $profile,
        'comments' => ProfilePostModel::getCommentsByPostId($post_id)
    ));
}

public function addComment($post_id)
{
    Auth::checkAuthentication();

    $post = ProfilePostModel::getPostById($post_id);

    if (!$post) {
        Redirect::to('profile/index');
        return;
    }

    $commentText = strip_tags(trim(Request::post('comment_text')));

    if (!empty($commentText)) {
        ProfilePostModel::createComment($post_id, Session::get('user_id'), $commentText);
    }

    Redirect::to('profilePost/show/' . $post_id);
}

public function deleteComment($comment_id)
{
    Auth::checkAuthentication();

    $comment = ProfilePostModel::getCommentById($comment_id);

    if (!$comment) {
        Redirect::to('profile/index');
        return;
    }

    $postId = $comment->post_id;

    ProfilePostModel::deleteComment($comment_id);

    Redirect::to('profilePost/show/' . $postId);
}

}