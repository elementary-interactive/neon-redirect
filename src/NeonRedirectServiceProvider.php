<?php

namespace Neon\Redirect;

use Illuminate\Foundation\Console\AboutCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NeonRedirectServiceProvider extends PackageServiceProvider
{
	const VERSION = '1.0.0-alpha-1';

	public function configurePackage(Package $package): void
	{
		AboutCommand::add('N30N', 'Redirect', self::VERSION);

		$package
			->name('neon-redirect')
			->hasMigration('create_redirects_table')
			->hasConfigFile()
			->runsMigrations();
	}
}
