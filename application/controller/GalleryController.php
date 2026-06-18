<?php

class GalleryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    public function index()
    {
        $userId = Session::get('user_id');
        $userFolder = dirname(__DIR__, 2) . '/userpictures/' . $userId;

        if (!is_dir($userFolder)) {
            mkdir($userFolder, 0777, true);
        }

        $files = array_diff(scandir($userFolder), array('.', '..'));

        $this->View->render('gallery/index', array(
            'files' => $files
        ));
    }

    public function upload()
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            Redirect::to('gallery/index');
            return;
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            Redirect::to('gallery/index');
            return;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['image']['tmp_name']);

        $allowed = array('image/jpeg', 'image/png', 'image/gif');

        if (!in_array($mime, $allowed)) {
            Redirect::to('gallery/index');
            return;
        }

        $userId = Session::get('user_id');
        $userFolder = dirname(__DIR__, 2) . '/userpictures/' . $userId;

        if (!is_dir($userFolder)) {
            mkdir($userFolder, 0777, true);
        }

        $imageName = trim($_POST['image_name']);

        $imageName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $imageName);

        if (empty($imageName)) {
            $imageName = 'bild';
        }

        if ($mime === 'image/jpeg') {
            $extension = 'jpg';
        } elseif ($mime === 'image/png') {
            $extension = 'png';
        } elseif ($mime === 'image/gif') {
            $extension = 'gif';
        } else {
            Redirect::to('gallery/index');
            return;
        }

        $safeName = $imageName . '.' . $extension;

        $target = $userFolder . '/' . $safeName;

        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        Redirect::to('gallery/index');
    }

    public function download($filename)
    {
        $userId = Session::get('user_id');
        $userFolder = dirname(__DIR__, 2) . '/userpictures/' . $userId;

        $safeName = basename(rawurldecode($filename));
        $filePath = $userFolder . '/' . $safeName;

        if (!file_exists($filePath)) {
            Redirect::to('gallery/index');
            return;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($filePath);

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $safeName . '"');
        header('Content-Length: ' . filesize($filePath));

        readfile($filePath);
        exit;
    }

    public function show($filename)
    {
        $userId = Session::get('user_id');
        $userFolder = dirname(__DIR__, 2) . '/userpictures/' . $userId;

        $safeName = basename(rawurldecode($filename));
        $filePath = $userFolder . '/' . $safeName;

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

public function delete()
{
    if (!isset($_GET['file'])) {
        Redirect::to('gallery/index');
        return;
    }

    $userId = Session::get('user_id');
    $userFolder = dirname(__DIR__, 2) . '/userpictures/' . $userId;

    $safeName = basename(rawurldecode($_GET['file']));
    $filePath = $userFolder . '/' . $safeName;

    if (file_exists($filePath)) {
        unlink($filePath);
    }

    header('Location: ' . Config::get('URL') . 'gallery/index');
    exit;
    }
}