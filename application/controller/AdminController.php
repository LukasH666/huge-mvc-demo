<?php

class AdminController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // special authentication check for the entire controller: Note the check-ADMIN-authentication!
        // All methods inside this controller are only accessible for admins (= users that have role type 7)
        Auth::checkAdminAuthentication();
    }

    /**
     * This method controls what happens when you move to /admin or /admin/index in your app.
     */
public function index()
{
    $this->View->render('admin/index', array(
        'users' => UserRoleModel::getAllUsersWithGroups(),
        'groups' => UserRoleModel::getAllGroups()
    ));
}

    public function actionAccountSettings()
{
    AdminModel::setAccountSuspensionAndDeletionStatus(
        Request::post('suspension'),
        Request::post('softDelete'),
        Request::post('user_id')
    );

    UserRoleModel::changeUserRoleByAdmin(
        Request::post('user_id'),
        Request::post('user_account_type')
    );

    Redirect::to("admin");
}

public function changeUserRole_action()
{
    if (Session::get('user_account_type') != 7) {
        Redirect::home();
        exit;
    }

    $user_id = Request::post('user_id');
    $new_type = Request::post('user_account_type');

    if (UserRoleModel::changeUserRoleByAdmin($user_id, $new_type)) {
        Session::add('feedback_positive', 'User role successfully changed.');
    } else {
        Session::add('feedback_negative', 'User role could not be changed.');
    }

    Redirect::to('admin/index');
}

}
