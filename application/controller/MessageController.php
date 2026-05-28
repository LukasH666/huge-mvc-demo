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
        $messages = array();

        if ($partner_id) {
            MessageModel::markAsRead($partner_id);
            $messages = MessageModel::getMessages($partner_id);
        }

        $this->View->render('message/index', array(
            'users' => $users,
            'messages' => $messages,
            'partner_id' => $partner_id
        ));
    }

    public function send($receiver_id)
    {
        MessageModel::sendMessage($receiver_id, Request::post('message_text'));
        Redirect::to('message/index/' . $receiver_id);
    }
}