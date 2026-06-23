<?php

class FriendController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    public function index()
    {
        $users = FriendModel::getAllOtherUsers();

        $statuses = array();

        foreach ($users as $user) {
            $statuses[$user->user_id] = FriendModel::getFriendshipStatus($user->user_id);
        }

        $this->View->render('friend/index', array(
            'users' => $users,
            'statuses' => $statuses,
            'pendingRequests' => FriendModel::getPendingRequests(),
            'sentRequests' => FriendModel::getSentRequests(),
            'friends' => FriendModel::getFriends()
        ));
    }

    public function sendRequest()
    {
        FriendModel::sendRequest(Request::post('receiver_id'));

        Redirect::to('friend/index');
    }

    public function acceptRequest()
    {
        FriendModel::acceptRequest(Request::post('request_id'));

        Redirect::to('friend/index');
    }

    public function declineRequest()
    {
        FriendModel::declineRequest(Request::post('request_id'));

        Redirect::to('friend/index');
    }

    public function removeFriend()
    {
        FriendModel::removeFriend(Request::post('user_id'));

        Redirect::to('friend/index');
    }
}