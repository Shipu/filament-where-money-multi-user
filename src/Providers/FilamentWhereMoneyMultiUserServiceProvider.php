<?php

namespace Shipu\FilamentWhereMoneyMultiUser\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentWhereMoneyMultiUserServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        $package->name('filament-where-money-multi-user')
            ->hasMigrations(['add_user_id_relative_tables'])
            ->hasConfigFile();

        $this->loadRelations();
    }

    public function packageBooted()
    {
        $this->loadModelEvents();
        $this->loadLocalScopes();
    }

    public function loadRelations()
    {
        foreach (config('filament-where-money-multi-user.active_models', []) as $model) {
            $model::resolveRelationUsing('user', function (Model $model) {
                return $model->belongsTo(config('filament-where-money-multi-user.user_model'), config('filament-where-money-multi-user.user_foreign_key_column_name'));
            });
        }
    }

    public function loadModelEvents()
    {
        foreach (config('filament-where-money-multi-user.active_models', []) as $model) {
            $model::saving(function (Model $model) {
                if(Schema::hasColumn($model->getTable(), config('filament-where-money-multi-user.user_foreign_key_column_name'))) {
                    $columnName = config('filament-where-money-multi-user.user_foreign_key_column_name');
                    $model->{$columnName} = auth()->id();
                }
            });
        }
    }

    public function loadLocalScopes()
    {
        foreach (config('filament-where-money-multi-user.active_models', []) as $model) {
            $model::addGlobalScope('user_id', function (Builder $builder) {
                if(Schema::hasColumn($builder->getModel()->getTable(), config('filament-where-money-multi-user.user_foreign_key_column_name'))) {
                    $builder->where(config('filament-where-money-multi-user.user_foreign_key_column_name'), auth()->id());
                }
            });
        }
    }
}
