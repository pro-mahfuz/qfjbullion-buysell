<?php

namespace App\Models;

use App\Exceptions\RedirectException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Session;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use HasFactory, LogsActivity;

    protected static function booted()
    {
        $business_id = Session::get('bussinessId');

        if (is_null($business_id)) {
            // throw new \Exception('Please Select Business!!!');
            throw new RedirectException('Please Select Business!!!');
        }


        static::addGlobalScope('business_id', function (Builder $builder) use ($business_id) {
            $builder->where('business_id', $business_id);
        });


        static::creating(function ($model) use ($business_id) {
            if (is_null($model->business_id)) {
                $model->business_id = $business_id;
            }
        });

        // static::updating(function ($model) use ($business_id) {
        //     if (is_null($model->business_id)) {
        //         $model->business_id = $business_id;
        //     }
        // });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Log all attributes
            ->logOnlyDirty() // Only log changes
            ->useLogName('default'); // Set a default log name
    }
}
