<?php

use Carbon\Carbon;

class ApiActivityController extends ApiBaseController {

  // -------------------------------------------------------------
  // Dependancies

  protected $activity;
  protected $product;
  protected $line;
  protected $process;

  // -------------------------------------------------------------
  // Constructor

  public function __construct(Activity $activity, Product $product, Line $line, Process $process) {
    $this->activity = $activity;
    $this->product = $product;
    $this->line = $line;
    $this->process = $process;
  }

  // -------------------------------------------------------------
  // Testing

  public function test() {

  }

  // -------------------------------------------------------------
  // Actions

  public function processStart() {
    $line_id = Input::get('line_id');
    $process_id = Input::get('process_id');
    $product_id = Input::get('product_id');

    $user = Sentry::getUser();

    $activity = new $this->activity;
    $activity->type_id = $activity::START_TYPE;
    $activity->user_id = $user->id;
    $activity->line_id = $line_id;
    $activity->process_id = $process_id;
    $activity->product_id = $product_id;

    if (! $activity->save()) {
      $errors = $activity->errors()->all();
      throw new ApiException($errors, 400);
    }

    $activity = $this->activity
                     ->with('user', 'line', 'process')
                     ->find($activity->id);

    $message = "Process {$activity->process->title} of "
             . "model {$activity->product->title} is sucessfully started "
             . "on production line {$activity->line->title} "
             . "by {$activity->user->first_name} {$activity->user->last_name}";

    return Response::api($message, 200, $activity);
  }

  public function processEnd() {
    $line_id = Input::get('line_id');
    $process_id = Input::get('process_id');
    $product_id = Input::get('product_id');
    $comment = Input::get('comment');

    $user = Sentry::getUser();

    $activity = new $this->activity;
    $activity->type_id = $activity::END_TYPE;
    $activity->user_id = $user->id;
    $activity->line_id = $line_id;
    $activity->process_id = $process_id;
    $activity->product_id = $product_id;
    $activity->comment = $comment;

    if (! $activity->save()) {
      $errors = $activity->errors()->all();
      throw new ApiException($errors, 400);
    }

    $activity = $this->activity
                     ->with('user', 'line', 'process', 'product')
                     ->find($activity->id);

    $message = "Process {$activity->process->title} of "
             . "model {$activity->product->title} is sucessfully ended "
             . "on production line {$activity->line->title} "
             . "by {$activity->user->first_name} {$activity->user->last_name}";

    return Response::api($message, 200, $activity);
  }

}
