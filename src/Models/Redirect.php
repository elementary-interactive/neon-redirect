<?php

namespace Neon\Redirect\Models;

use Neon\Models\Basic as BasicModel;
use Neon\Models\Traits\Uuid;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\SortableTrait;

class Redirect extends BasicModel
{
	use LogsActivity;
	use SortableTrait;
	use Uuid;

	// N30N UUID to forget auto increment stuff.

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'from',
		'to',
		'code',
	];

	protected $casts = [
		'from' => 'string',
		'to'   => 'string',
		'code' => 'string',
	];

	public function getActivitylogOptions(): LogOptions
	{
		return LogOptions::defaults();
	}
}
