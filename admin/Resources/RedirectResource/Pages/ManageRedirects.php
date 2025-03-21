<?php

namespace Neon\Admin\Resources\RedirectResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Neon\Redirect\Models\Redirect;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Neon\Admin\Resources\RedirectResource;
use Filament\Resources\Pages\ManageRecords;

class ManageRedirects extends ManageRecords
{
  protected static string $resource = RedirectResource::class;

  protected function getHeaderActions(): array
  {
    return [
		Action::make('Export')
			->icon('heroicon-m-arrow-down-tray')
			->color('gray')
			->action(function(){
				return $this->export();
			}),
		Actions\CreateAction::make()
        	->slideOver(),
    ];
  }

  private function export() {
	$redirects = Redirect::all();

	// Convert database records into an adjacency list (redirectList)
	$redirectList = [];
	foreach ($redirects as $redirect) {
		$redirectList[$redirect->from] = $redirect->to;
	}

	$validRedirections = [];
	$visited = [];

	foreach ($redirectList as $from => $to) {
		$stack = [];
		if (!$this->hasCycle($from, $redirectList, $visited, $stack)) {
			$validRedirections[$from] = $to;
		}
	}
	
	$validRedirections = $this->fixRedirections($validRedirections);

	$htaccess = '<IfModule mod_rewrite.c>' . PHP_EOL;
	$htaccess .= "\t<IfModule mod_negotiation.c>" . PHP_EOL;
	$htaccess .= "\t\tOptions -MultiViews -Indexes" . PHP_EOL;
	$htaccess .= "\t</IfModule>" . PHP_EOL;
	$htaccess .= PHP_EOL;
	$htaccess .= "\tRewriteEngine On" . PHP_EOL;
	$htaccess .= PHP_EOL;
	$htaccess .= "\t# Redirect to https" . PHP_EOL;
	$htaccess .= "\tRewriteCond %{HTTPS} !=on" . PHP_EOL;
	$htaccess .= "\tRewriteCond %{HTTP:X-Forwarded-Proto} !=https" . PHP_EOL;
	$htaccess .= "\tRewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]" . PHP_EOL;
	$htaccess .= PHP_EOL;
	$htaccess .= "\t# Handle Authorization Header" . PHP_EOL;
	$htaccess .= "\tRewriteCond %{HTTP:Authorization} ." . PHP_EOL;
	$htaccess .= "\tRewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]" . PHP_EOL;
	$htaccess .= PHP_EOL;
	$htaccess .= "\t# Redirect Trailing Slashes If Not A Folder..." . PHP_EOL;
	$htaccess .= "\tRewriteCond %{REQUEST_FILENAME} !-d" . PHP_EOL;
	$htaccess .= "\tRewriteCond %{REQUEST_URI} (.+)/$" . PHP_EOL;
	$htaccess .= "\tRewriteRule ^ %1 [L,R=301]" . PHP_EOL;
	$htaccess .= PHP_EOL;
	$htaccess .= "\t# Handle Front Controller..." . PHP_EOL;
	$htaccess .= "\tRewriteCond %{REQUEST_FILENAME} !-d" . PHP_EOL;
	$htaccess .= "\tRewriteCond %{REQUEST_FILENAME} !-f" . PHP_EOL;
	$htaccess .= "\tRewriteRule ^ index.php [L]" . PHP_EOL;
	$htaccess .= PHP_EOL;
	$htaccess .= "\t# Redirects" . PHP_EOL;
	
	foreach ($validRedirections as $from => $to) {
		$htaccess .= "\tRedirect 301 " . $from . " " . $to . PHP_EOL;
	}
	$htaccess .= '</IfModule>';

	\Storage::disk('public')->put('.htaccess', $htaccess);

	$file = storage_path('app/public/' . '.htaccess');
	File::copy($file, './.htaccess');
	
	return Response::download($file, '.htaccess');

  }

  private function hasCycle($node, $redirectList, &$visited, &$stack)
  {
	  if (isset($stack[$node])) return true; // Cycle detected
	  if (isset($visited[$node])) return false; // Already checked

	  $visited[$node] = true;
	  $stack[$node] = true;

	  if (isset($redirectList[$node]) && $this->hasCycle($redirectList[$node], $redirectList, $visited, $stack)) {
		  return true;
	  }

	  unset($stack[$node]);
	  return false;
  }

  private function fixRedirections($redirectList)
  {
	  $fixedRedirections = [];

	  foreach ($redirectList as $from => $to) {
		  $finalDestination = $to;
		  
		  // Follow the chain until we reach the final destination
		  while (isset($redirectList[$finalDestination])) {
			  $finalDestination = $redirectList[$finalDestination];
		  }

		  $fixedRedirections[$from] = $finalDestination;
	  }

	  return $fixedRedirections;
  }
}
