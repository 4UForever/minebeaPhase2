<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request) {

});


App::after(function($request, $response) {

});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

// -----------------------------------------------------------
// Check permissions filter

Route::filter('auth.has_access', function($route, $request, $value) {
  if (! Sentry::check()) {
    return Redirect::to('admin/user/login');
  }

  $user = Sentry::getUser();

  if(! $user->hasAccess($value)) {
    return Redirect::to('admin/denie');
  }
});

Route::filter('auth.api.has_access', function($route, $request, $value) {
  $user = User::qrCodeLogin(Input::get('qr_code'));
  $user = User::find($user['id']);

  Sentry::login($user);

  $user = Sentry::getUser();

  if(! $user->hasAccess($value)) {
    $errors = array('You are not authorized to access this feature');
    throw new ApiException($errors, 401);
  }
});

Route::filter('auth.login', function() {
	if (Sentry::check()) {
    return Redirect::to('/');
  }
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function() {
	if (Session::token() !== Input::get('_token')) {
		throw new Illuminate\Session\TokenMismatchException;
	}
});
