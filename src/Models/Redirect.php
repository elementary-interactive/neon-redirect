<?php

namespace Neon\Redirect\Models;

use Neon\Models\Traits\Uuid;
use Neon\Models\Basic as BasicModel;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\EloquentSortable\SortableTrait;

class Redirect extends BasicModel
{
  use LogsActivity;
  use SortableTrait;
  use Uuid; // N30N UUID to forget auto increment stuff.

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'from',
    'to',
  ];

  protected $casts = [
    'from' => 'string',
    'to' => 'string',
  ];

  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults();
  }
}
