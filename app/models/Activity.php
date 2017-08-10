<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class Activity extends Ardent {

  // --------------------------------------------------------
  // Constants

  const START_TYPE = 1;
  const START_TYPE_TEXT = 'Start';
  const END_TYPE = 0;
  const END_TYPE_TEXT = 'End';

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'type_id',
    'type_title',
    'user_id',
    'user_full_name',
    'user_email',
    'line_id',
    'line_title',
    'process_id',
    'process_title',
    'product_id',
    'product_title',
  );

  protected $guarded  = array();

  protected $hidden = array(
    'type_id',
    'user_id',
    'line_id',
    'product_id',
    'process_id',
  );

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'user' => array(self::BELONGS_TO, 'User'),
    'line' => array(self::BELONGS_TO, 'Line'),
    'product' => array(self::BELONGS_TO, 'Product'),
    'process' => array(self::BELONGS_TO, 'Process'),
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'type_id' => 'required|in:0,1',
    'user_id' => 'required|exists:users,id',
    'line_id' => 'required|exists:lines,id',
    'product_id' => 'required|exists:products,id',
    'process_id' => 'required|exists:processes,id',
  );

  // ---------------------------------------------
  // Ardent Hooks

  public function beforeSave() {
    $this->type_title = $this->type_id == self::START_TYPE ? self::START_TYPE_TEXT : self::END_TYPE_TEXT;
    $this->user_full_name = Sentry::getUser()->first_name . ' ' . Sentry::getUser()->last_name;
    $this->user_email = Sentry::getUser()->email;
    $this->line_title = Line::find($this->line_id)->title;
    $this->product_title = Product::find($this->product_id)->title;
    $this->process_title = Process::find($this->process_id)->title;

    $this->validateRelations($this->line_id, $this->product_id, $this->process_id, $this->user_id);

    switch ($this->type_id) {
      case self::START_TYPE:
        $this->validateProcessStartAvailability($this->line_id, $this->process_id);
        break;
      case self::END_TYPE:
        $this->validateProcessEndAvailability($this->line_id, $this->process_id);
        break;
    }

    $errors = $this->errors()->all();

    return empty($errors);
  }

  // ---------------------------------------------
  // Activity actions

  public function validateProcessStartAvailability($line_id, $process_id) {
    $user = Sentry::getUser();

    $last_activity = $this->findLastActivity($line_id, $process_id);

    if (empty($last_activity)) {
      return TRUE;
    }

    if ($last_activity->type_id == $this::START_TYPE) {
      $line_title = $last_activity->line->title;
      $process_title = $last_activity->process->title;

      $message = "Process $process_title on a production line $line_title "
               . "is already started by a user {$last_activity->user_full_name}";

      $this->errors()->add('process_id', $message);
    }
  }

  public function validateProcessEndAvailability($line_id, $process_id) {
    $user = Sentry::getUser();

    $last_activity = $this->findLastActivity($line_id, $process_id);

    if (empty($last_activity)) {
      $line_title = Line::find($line_id)->title;
      $process_title = Process::find($process_id)->title;

      $message = "Process $process_title on a production line $line_title "
               . "is not started yet";

      $this->errors()->add('process_id', $message);
      return FALSE;
    }

    if ($last_activity->type_id == $this::END_TYPE) {
      $user_full_name = "{$user->first_name} {$user->last_name}";
      $line_title = $last_activity->line->title;
      $process_title = $last_activity->process->title;

      $message = "Process $process_title on a production line $line_title "
               . "is not started yet";

      $this->errors()->add('process_id', $message);
    }

    if ($last_activity->type_id == $this::START_TYPE && $last_activity->user_id != $user->id) {
      $line_title = $last_activity->line->title;
      $process_title = $last_activity->process->title;

      $message = "Process $process_title on a production line $line_title "
               . "is already started by a user {$last_activity->user_full_name}";

      $this->errors()->add('process_id', $message);
    }
  }

  public function validateRelations($line_id, $product_id, $process_id, $user_id) {
    $eagor_load = array(
      'products' => function($query) use($product_id) {
        $query->where('products.id', $product_id);
      },
      'processes' => function($query) use($process_id) {
        $query->where('processes.id', $process_id);
      },
      'processes.users' => function($query) use($process_id) {
        $query->where('users.id', Sentry::getUser()->id);
      },
    );


    $line = Line::with($eagor_load)->find($line_id);

    if ($line->products->isEmpty()) {
      $this->errors()->add('product_id', "Product id $product_id does not belongs to line id $line_id");
    }

    if ($line->processes->isEmpty()) {
      $this->errors()->add('process_id', "Process id $process_id does not belongs to line id $line_id");
    }
    elseif ($line->processes->first()->users->isEmpty()) {
      $this->errors()->add('user_id', "User id $user_id does not belongs to process id $process_id");
    }
  }

  public function findLastActivity($line_id, $process_id, $user_id = NULL) {
    $query = $this->with('user', 'line', 'process')
                  ->where('line_id', $line_id)
                  ->where('process_id', $process_id)
                  ->orderBy('updated_at', 'desc')
                  ->take(1);

    if (! empty($user_id)) {
      $query->where('user_id', $user_id);
    }

    $activities = $query->get();

    if ($activities->isEmpty()) {
      return FALSE;
    }

    return $activities->first();
  }

  public function findLastUserActivity($user_id) {
    $query = $this->with('user', 'line', 'process')
                  ->where('user_id', $user_id)
                  ->orderBy('updated_at', 'desc')
                  ->take(1);

    $activities = $query->get();

    if ($activities->isEmpty()) {
      return FALSE;
    }

    return $activities->first();
  }

}
