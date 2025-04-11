<?php

namespace Neon\Admin\Resources\RedirectResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Redis;
use Neon\Admin\Resources\RedirectResource;
use Neon\Redirect\Models\Redirect;

class ManageRedirects extends ManageRecords
{
	protected static string $resource = RedirectResource::class;

	protected function getHeaderActions(): array
	{
		return [
			Action::make('Aktiválás')
				->icon('heroicon-m-arrow-down-tray')
				->color('gray')
				->action(function () {
					return $this->export();
				}),
			Actions\CreateAction::make()
				->slideOver(),
		];
	}

	private function export()
	{
		$keys = Redis::command('keys', ['redirects:*']);
		foreach ($keys as $key) {
			Redis::command('del', [$key]);
		}

		$redirects = Redirect::all();

		foreach ($redirects as $redirect) {
			Redis::command('set', [
				"redirects:" . $redirect->from,
				json_encode(
					[
						'to'   => $redirect->to,
						'code' => $redirect->code ?? null,
					]
				),
			]);
		}
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
