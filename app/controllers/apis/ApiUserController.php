<?php

use Carbon\Carbon;

class ApiUserController extends ApiBaseController {

  // -------------------------------------------------------------
  // Dependancies

  protected $user;

  // -------------------------------------------------------------
  // Constructor

  public function __construct(User $user) {
    $this->user = $user;
  }

  // -------------------------------------------------------------
  // Testing

  public function test() {

  }

  // -------------------------------------------------------------
  // Authentication

  public function login() {
    $user = $this->user->qrCodeLogin(Input::get('qr_code'));
    return Response::api('You are successfully logged-in', 200, $user);
  }

  public function logout() {
    $user = $this->user->qrCodeLogout(Input::get('qr_code'));
    return Response::api('You are successfully logged-out', 200, $user);
  }

  public function getLineLeader() {
    $qr_code = Input::get('qr_code');

    if (empty($qr_code)) {
      $messages = array("Please input qr_code");
      throw new ApiException($messages, 404);
    }

    $users = $this->user->whereHas('groups', function($q) {
              $q->where('permissions', 'like', '%work_on_model_processes%');
            })
            ->where('leader', '1')
            ->where('qr_code', '<>', $qr_code)->get();
    //print_r($users->toArray());
    $message = "Successfully received request.";
    return Response::api($message, 200, $users);
  }

}