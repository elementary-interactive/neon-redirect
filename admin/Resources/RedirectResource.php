<?php

namespace Neon\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Neon\Admin\Resources\RedirectResource\Pages;
use Neon\Admin\Resources\Traits\NeonAdmin;
use Neon\Redirect\Models\Redirect;

class RedirectResource extends Resource
{
	use NeonAdmin;

	protected static ?int $navigationSort = 6;

	protected static ?string $model = Redirect::class;

	protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

	protected static ?string $activeNavigationIcon = 'heroicon-s-arrow-path';

	protected static ?string $recordTitleAttribute = 'from';

	public static function getNavigationLabel(): string
	{
		return __('neon-admin::admin.navigation.redirect');
	}

	public static function getNavigationGroup(): string
	{
		return __('neon-admin::admin.navigation.web');
	}

	public static function getModelLabel(): string
	{
		return __('neon-admin::admin.models.redirect');
	}

	public static function getPluralModelLabel(): string
	{
		return __('neon-admin::admin.models.redirects');
	}

	public static function items(): array
	{
		$t = [
			Forms\Components\TextInput::make('from')
				->required()
				->label(__('neon-admin::admin.resources.redirect.form.fields.from.label')),
			Forms\Components\TextInput::make('to')
				->required()
				->label(__('neon-admin::admin.resources.redirect.form.fields.to.label')),
			Forms\Components\TextInput::make('code')
				->required()
				->numeric()
				->label(__('neon-admin::admin.resources.redirect.form.fields.code.label')),
		];

		return $t;
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns(
				[
					Tables\Columns\TextColumn::make('from')
						->label(__('neon-admin::admin.resources.redirect.form.fields.from.label'))
						->searchable()
						->sortable()
						->toggleable(isToggledHiddenByDefault: false),
					Tables\Columns\TextColumn::make('to')
						->label(__('neon-admin::admin.resources.redirect.form.fields.to.label'))
						->searchable()
						->sortable()
						->toggleable(isToggledHiddenByDefault: false),
					Tables\Columns\TextColumn::make('code')
						->label(__('neon-admin::admin.resources.redirect.form.fields.code.label'))
						->searchable()
						->sortable()
						->toggleable(isToggledHiddenByDefault: false),
				]
			)
			->actions(
				[
					Tables\Actions\EditAction::make()
						->slideOver(),
					Tables\Actions\DeleteAction::make(),
					Tables\Actions\ForceDeleteAction::make(),
					Tables\Actions\RestoreAction::make(),
				]
			)
			->bulkActions(self::bulkActions())
			->defaultSort('created_at')
			->paginatedWhileReordering();
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ManageRedirects::route('/'),
		];
	}

	public static function getEloquentQuery(): Builder
	{
		return parent::getEloquentQuery()
			->withoutGlobalScopes();
	}
}
