<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Facades\Activity;

class BaseModelObserver
{
    public function updating(Model $model)
    {
        $oldValues = $model->getOriginal();

        activity()
            ->performedOn($model)
            ->causedBy(auth()->user() ?? null) // Handles cases where no user is logged in
            ->withProperties([
                'attributes' => $model->getChanges(),
                'old' => $oldValues,
            ])
            ->log(class_basename($model) . ' updated');
    }

    public function created(Model $model)
    {
        activity()
            ->performedOn($model)
            ->causedBy(auth()->user() ?? null)
            ->log(class_basename($model) . ' created');
    }

    public function deleted(Model $model)
    {
        activity()
            ->performedOn($model)
            ->causedBy(auth()->user() ?? null)
            ->log(class_basename($model) . ' deleted');
    }
}
