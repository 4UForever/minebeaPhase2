<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class Process extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'title',
    'line_id',
    'number',
  );

  protected $guarded  = array();

  protected $hidden = array(
    'line_id',
  );

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'line' => array(self::BELONGS_TO, 'Line'),
    'users' => array(self::BELONGS_TO_MANY, 'User'),
    'activities' => array(self::HAS_MANY, 'Activity'),
    'document_indices' => array(self::HAS_MANY, 'DocumentIndex'),
    'parts' => array(self::HAS_MANY, 'Part'),
    'ng_details' => array(self::HAS_MANY, 'NgDetail'),
    'wips' => array(self::BELONGS_TO_MANY, 'Wip', 'table'=>'wip_process'),
    'lots' => array(self::BELONGS_TO_MANY, 'Lot')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'title' => 'required|max:128',
    'number' => 'required|unique:processes',
    'line_id' => 'required|exists:lines,id',
  );

  // ---------------------------------------------
  // Ardent Hooks

  public function beforeDelete() {
    $process_count = $this->line()->first()->processes()->count();

    if ($process_count < 2 && ! $this->parent_is_deleted) {
      $this->errors()->add('line_id', "A process id {$this->id} cannot be delete because it is only one process of a line {$this->line()->first()->title}");
      return FALSE;
    }

    return TRUE;
  }

  public function afterDelete() {
    $this->users()->sync(array());

    foreach ($this->document_indices()->get() as $docuemnt_index) {
      $docuemnt_index->delete();
    }

    return TRUE;
  }

  // ---------------------------------------------
  // Process status

  public function getStatusAttribute() {
    return isset($this->attributes['status']) ? $this->attributes['status'] : NULL;
  }

  public function setStatusAttribute($line_id, $process_id) {
    $activity = new Activity;
    $last_activity = $activity->findLastActivity($line_id, $process_id);

    $this->attributes['status'] = empty($last_activity) ? $activity::END_TYPE : $last_activity->type_id;
  }

  // ---------------------------------------------
  // Process availability

  public function getAvailableAttribute() {
    return isset($this->attributes['available']) ? $this->attributes['available'] : NULL;
  }

  public function setAvailableAttribute($user_id, $line_id, $process_id) {
    $activity = new Activity;
    $last_activity = $activity->findLastActivity($line_id, $process_id);
    $last_user_activity = $activity->findLastUserActivity($user_id);

    if (! empty($last_user_activity)) {
      if (
        $last_user_activity->type_id == Activity::START_TYPE &&
        $process_id != $last_user_activity->process->id
      ) {
        $this->attributes['available'] = FALSE;
        $this->attributes['available_message'] = "User {$last_user_activity->user_full_name} already work on process {$last_user_activity->process->title} of production line {$last_user_activity->line->title}";
        return;
      }
    }

    if (empty($last_activity)) {
      $this->attributes['available'] = TRUE;
    }
    elseif ($last_activity->type_id == Activity::START_TYPE && $last_activity->user_id != $user_id) {
      $this->attributes['available'] = FALSE;

      $line = Line::find($line_id);
      $process = Process::find($process_id);
      $this->attributes['available_message'] = "Process {$process->title} on production line {$line->title} is already started by user {$last_activity->user_full_name}";
    }
    else {
      $this->attributes['available'] = TRUE;
    }
  }

  // ---------------------------------------------
  // Misc

  public function makeNewProcessesTable($new_processes) {
    $id = 'new-processes';
    $heads = array('Title', 'Number', '');

    if (empty($new_processes)) {
      $new_processes = array();
    }

    $rows = array();

    foreach ($new_processes as $new_process) {
      $rows[] = $this->makeNewProcessesTableRow($new_process);
    }

    return View::make('admins.misc.table', compact('id', 'heads', 'rows'))->render();
  }

  public function makeNewProcessesTableRow($new_process) {
    $row = array();

    $uid = empty($new_process['id']) ? uniqid() : $new_process['id'];

    $params = array(
      'name' => "new_processes[$uid][title]",
      'value' => $new_process['title'],
    );

    $row['title'] = $new_process['title'] . View::make('admins.misc.input_hidden', $params)->render();

    $params = array(
      'name' => "new_processes[$uid][number]",
      'value' => $new_process['number'],
    );

    $row['number'] = $new_process['number'] . View::make('admins.misc.input_hidden', $params)->render();

    $buttons = array(
      array(
        'url' => url("admin/process/create-form") . '?' . http_build_query($new_process),
        'type' => 'warning',
        'text' => 'Edit',
      ),
      array(
        'url' => url("admin/process/delete-form"),
        'type' => 'danger',
        'text' => 'Delete',
      ),
    );

    $row['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();

    return View::make('admins.misc.table_row', compact('row'))->render();
  }

}
