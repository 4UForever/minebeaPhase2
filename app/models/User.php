<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Cartalyst\Sentry\Users\Eloquent\User as BaseUser;
use Carbon\Carbon;

class User extends BaseUser {

  // ---------------------------------------------------------------------------
  // Configurations

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
    'password',
    'remember_token',//Remove hidden qr_code on 01/02/2016 for export user to Excel sheet
    'activated',
    'activation_code',
    'activated_at',
    'reset_password_code',
    'permissions',
    'persist_code',
  );

  // ---------------------------------------------------------------------------
  // Relationships

  public function processes() {
    return $this->belongsToMany('Process');
  }

  public function activities() {
    return $this->hasMany('Activity');
  }

  // ---------------------------------------------------------------------------
  // Authenticate

  public static function qrCodeLogin($qr_code) {
    $user = self::where('qr_code', $qr_code)->first();

    if (empty($user)) {
      $messages = array(
        'qr_code' => 'Invalid QR code'
      );

      throw new ApiException($messages, 400);
    }

    $products = Product::with('lines.processes.users')->get()->toArray();

    $user->last_login = new Carbon();

    if (! $user->save()) {
      $messages = $user->errors()->all();
      throw new ApiException($messages, 400);
    }

    foreach ($products as $product_key => &$product) {
      foreach ($product['lines'] as $line_key => &$line) {
        foreach ($line['processes'] as $process_key => &$process) {
          $process_user_ids = array_fetch($process['users'], 'id');

          if (! in_array($user->id, $process_user_ids)) {
            unset($line['processes'][$process_key]);
          }

          unset($process['users']);
        }

        $line['processes'] = array_values($line['processes']);

        if (empty($line['processes'])) {
          unset($product['lines'][$line_key]);
        }
      }

      $product['lines'] = array_values($product['lines']);

      if (empty($product['lines'])) {
        unset($products[$product_key]);
      }
    }

    $products = array_values($products);

    $result = $user->toArray();
    $result['groups'] = $user->getGroups();
    $result['permissions'] = $user->getMergedPermissions();

    foreach ($result['groups'] as &$group) {
      unset($group['permissions']);
      unset($group['pivot']);
    }

    $result['models'] = $products;

    return $result;
  }

  public function qrCodeLogout($qr_code) {
    $user = self::where('qr_code', $qr_code)->first();

    if (empty($user)) {
      $messages = array(
        'qr_code' => 'Invalid QR code'
      );

      throw new ApiException($messages, 400);
    }

    $products = Product::with('lines.processes.users')->get()->toArray();

    $user->last_logout = new Carbon();

    if (! $user->save()) {
      $messages = $user->errors()->all();
      throw new ApiException($messages, 400);
    }

    foreach ($products as $product_key => &$product) {
      foreach ($product['lines'] as $line_key => &$line) {
        foreach ($line['processes'] as $process_key => &$process) {
          $process_user_ids = array_fetch($process['users'], 'id');

          if (! in_array($user->id, $process_user_ids)) {
            unset($line['processes'][$process_key]);
          }

          unset($process['users']);
        }

        if (empty($line['processes'])) {
          unset($product['lines'][$line_key]);
        }
      }

      if (empty($product['lines'])) {
        unset($products[$product_key]);
      }
    }

    $result = $user->toArray();
    $result['models'] = $products;

    return $result;
  }

  // ---------------------------------------------------------------------------
  // Mockup data

  public function getMockUpData() {
    return array(
      'id' => 7,
      'first_name' => 'James',
      'last_name' => 'Bond',
      'last_login' => '2015-01-01 08:00:00',
      'last_logout' => '2015-01-01 17:00:00',
      'created_at' => '2000-01-01 01:00:00',
      'updated_at' => '2000-01-01 01:00:00',
      'roles' => array(
        array(
          'id' => 1,
          'title' => 'Product engineer',
          'created_at' => '2000-01-01 01:00:00',
          'updated_at' => '2000-01-01 01:00:00'
        ),
        array(
          'id' => 2,
          'title' => 'Secret agent',
          'created_at' => '2000-01-01 01:00:00',
          'updated_at' => '2000-01-01 01:00:00'
        ),
      ),
      'models' => array(
        array(
          'id' => 1,
          'title' => 'ASTON MARTIN - V8 VANTAGE',
          'created_at' => '2000-01-01 01:00:00',
          'updated_at' => '2000-01-01 01:00:00',
          'lines' => array(
            array(
              'id' => 1,
              'title' => 'Line no. 1',
              'created_at' => '2000-01-01 01:00:00',
              'updated_at' => '2000-01-01 01:00:00',
              'processes' => array(
                array(
                  'id' => 1,
                  'title' => 'Process no. 1',
                  'created_at' => '2000-01-01 01:00:00',
                  'updated_at' => '2000-01-01 01:00:00'
                ),
                array(
                  'id' => 2,
                  'title' => 'Process no. 2',
                  'created_at' => '2000-01-01 01:00:00',
                  'updated_at' => '2000-01-01 01:00:00'
                ),
              ),
            ),
            array(
              'id' => 2,
              'title' => 'Line no. 2',
              'created_at' => '2000-01-01 01:00:00',
              'updated_at' => '2000-01-01 01:00:00',
              'processes' => array(
                array(
                  'id' => 3,
                  'title' => 'Process no. 3',
                  'created_at' => '2000-01-01 01:00:00',
                  'updated_at' => '2000-01-01 01:00:00'
                ),
                array(
                  'id' => 4,
                  'title' => 'Process no. 4',
                  'created_at' => '2000-01-01 01:00:00',
                  'updated_at' => '2000-01-01 01:00:00'
                ),
              ),
            ),
          ),
        ),
        array(
          'id' => 2,
          'title' => 'Jetpack',
          'created_at' => '2000-01-01 01:00:00',
          'updated_at' => '2000-01-01 01:00:00',
          'lines' => array(
            array(
              'id' => 3,
              'title' => 'Line no. 3',
              'created_at' => '2000-01-01 01:00:00',
              'updated_at' => '2000-01-01 01:00:00',
              'processes' => array(
                array(
                  'id' => 5,
                  'title' => 'Process no. 5',
                  'created_at' => '2000-01-01 01:00:00',
                  'updated_at' => '2000-01-01 01:00:00'
                ),
                array(
                  'id' => 6,
                  'title' => 'Process no. 6',
                  'created_at' => '2000-01-01 01:00:00',
                  'updated_at' => '2000-01-01 01:00:00'
                ),
              ),
            ),
            array(
              'id' => 4,
              'title' => 'Line no. 4',
              'created_at' => '2000-01-01 01:00:00',
              'updated_at' => '2000-01-01 01:00:00',
              'processes' => array(
                array(
                  'id' => 7,
                  'title' => 'Process no. 7',
                  'created_at' => '2000-01-01 01:00:00',
                  'updated_at' => '2000-01-01 01:00:00'
                ),
                array(
                  'id' => 8,
                  'title' => 'Process no. 8',
                  'created_at' => '2000-01-01 01:00:00',
                  'updated_at' => '2000-01-01 01:00:00'
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

}
