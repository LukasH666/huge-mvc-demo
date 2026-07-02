<?php

/**
 * RegisterController
 * Register new user
 */
class RegisterController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Register page
     * Only admins are allowed to create new users.
     */
    public function index()
    {
        if (!LoginModel::isUserLoggedIn() || Session::get("user_account_type") != 7) {
            Redirect::home();
            exit;
        }

        $this->View->render('register/index');
    }

    /**
     * Register page action
     * POST-request after form submit
     */
    public function register_action()
    {
        $recaptchaResponse = Request::post('g-recaptcha-response');

        if (!ReCaptchaModel::verify($recaptchaResponse)) {
            Session::add('feedback_negative', 'Bitte bestätige reCAPTCHA.');
            Redirect::to('register/index');
            return;
        }

        $registration_successful = RegistrationModel::registerNewUser();

        if ($registration_successful) {
            Redirect::to('login/index');
        } else {
            Redirect::to('register/index');
        }
    }

    /**
     * Verify user after activation mail link opened
     *
     * @param int $user_id user's id
     * @param string $user_activation_verification_code user's verification token
     */
    public function verify($user_id, $user_activation_verification_code)
    {
        if (isset($user_id) && isset($user_activation_verification_code)) {
            RegistrationModel::verifyNewUser($user_id, $user_activation_verification_code);
            $this->View->render('register/verify');
        } else {
            Redirect::to('login/index');
        }
    }
}