<?php

class MessageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    public function index($partner_id = null)
    {
        $users = MessageModel::getUsers();
        $groups = MessageModel::getGroupsOfCurrentUser();
        $messages = array();

        if ($partner_id) {
            MessageModel::markAsRead($partner_id);
            $messages = MessageModel::getMessages($partner_id);
        }

        $this->View->render('message/index', array(
            'users' => $users,
            'groups' => $groups,
            'messages' => $messages,
            'partner_id' => $partner_id,
            'group_id' => null,
            'chat_type' => 'user'
        ));
    }

    public function group($group_id)
    {
        $users = MessageModel::getUsers();
        $groups = MessageModel::getGroupsOfCurrentUser();

        MessageModel::markGroupMessagesAsRead($group_id);

        $messages = MessageModel::getGroupMessages($group_id);

        $this->View->render('message/index', array(
            'users' => $users,
            'groups' => $groups,
            'messages' => $messages,
            'partner_id' => null,
            'group_id' => $group_id,
            'chat_type' => 'group'
        ));
    }

    public function send($receiver_id)
    {
        $messageText = trim(Request::post('message_text'));

        $fileFilename = null;
        $fileOriginalName = null;
        $fileMimeType = null;

        if (!empty($_FILES['message_file']['name'])) {

            if ($_FILES['message_file']['error'] === UPLOAD_ERR_OK) {

                $maxFileSize = 5 * 1024 * 1024;

                if ($_FILES['message_file']['size'] <= $maxFileSize) {

                    $allowedMimeTypes = array(
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'application/pdf',
                        'text/plain',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    );

                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $_FILES['message_file']['tmp_name']);
                    finfo_close($finfo);

                    if (in_array($mimeType, $allowedMimeTypes)) {

                        $uploadDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR
           . 'messagefiles' . DIRECTORY_SEPARATOR
           . 'user' . DIRECTORY_SEPARATOR
           . Session::get('user_id') . DIRECTORY_SEPARATOR;

                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $extension = pathinfo($_FILES['message_file']['name'], PATHINFO_EXTENSION);
                        $safeFilename = 'message_' . Session::get('user_id') . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . strtolower($extension);

                        if (move_uploaded_file($_FILES['message_file']['tmp_name'], $uploadDir . $safeFilename)) {
                            $fileFilename = $safeFilename;
                            $fileOriginalName = basename($_FILES['message_file']['name']);
                            $fileMimeType = $mimeType;
                        }
                    }
                }
            }
        }

        if (!empty($messageText) || !empty($fileFilename)) {
            MessageModel::sendMessage(
                $receiver_id,
                $messageText,
                $fileFilename,
                $fileOriginalName,
                $fileMimeType
            );
        }

        Redirect::to('message/index/' . $receiver_id);
    }

public function download($message_id)
{
    Auth::checkAuthentication();

    $message = MessageModel::getMessageById($message_id);

    if (!$message) {
        Redirect::to('message/index');
        return;
    }

    $currentUserId = Session::get('user_id');

    if ($message->sender_id != $currentUserId && $message->receiver_id != $currentUserId) {
        Redirect::to('message/index');
        return;
    }

    if (empty($message->file_filename)) {
        Redirect::to('message/index');
        return;
    }

    $projectRoot = dirname(__DIR__, 2);

    $filePath = $projectRoot . DIRECTORY_SEPARATOR
              . 'messagefiles' . DIRECTORY_SEPARATOR
              . 'user' . DIRECTORY_SEPARATOR
              . $message->sender_id . DIRECTORY_SEPARATOR
              . $message->file_filename;

    $oldFilePath = $projectRoot . DIRECTORY_SEPARATOR
                 . 'public' . DIRECTORY_SEPARATOR
                 . 'messagefiles' . DIRECTORY_SEPARATOR
                 . 'user' . DIRECTORY_SEPARATOR
                 . $message->sender_id . DIRECTORY_SEPARATOR
                 . $message->file_filename;

    if (!file_exists($filePath) && file_exists($oldFilePath)) {
        $filePath = $oldFilePath;
    }

    if (!file_exists($filePath)) {
        Redirect::to('message/index');
        return;
    }

    if (ob_get_length()) {
        ob_end_clean();
    }

    header('Content-Type: ' . $message->file_mime_type);
    header('Content-Disposition: inline; filename="' . basename($message->file_original_name) . '"');
    header('Content-Length: ' . filesize($filePath));

    readfile($filePath);
    exit;
}

    public function sendGroup($group_id)
    {
        MessageModel::sendGroupMessage($group_id, Request::post('message_text'));
        Redirect::to('message/group/' . $group_id);
    }

    public function createGroup()
    {
        MessageModel::createGroup(
            Request::post('group_name'),
            Request::post('members')
        );

        Redirect::to('message/index');
    }
}