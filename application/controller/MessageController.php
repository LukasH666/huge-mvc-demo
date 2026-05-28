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

    public function send($receiver_id)
    {
        MessageModel::sendMessage($receiver_id, Request::post('message_text'));
        Redirect::to('message/index/' . $receiver_id);
    }
}