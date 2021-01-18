<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'event';

    protected $fillable = ['*'];

    /**
     * the method will produce summary of events count.
     * events has accelerate, decelerate, eventImpact, impact, turnLeft, turnRight and button.
     * How to use the method
     * example App\Models\Event::getEventSummary($stkUser); or App\Models\Event::getEventSummary();
     * @param $builder
     * @param string $stkUser (optional) Event's owner
     */
    public function scopeGetEventSummary($builder, $stkUser = null, $startTime, $endTime)
    {
        // declare event ids.
        $accelerate = 16;
        $decelerate = 17;
        $eventImpact = 20;
        $turnLeft = 21;
        $turnRight = 22;
        $button = 14;

        // declare Raw query
        $rawQuery = "
            COUNT(*) as total,
            SUM(case when type=$accelerate then 1 else 0 end) as accelerate,
            SUM(case when type=$decelerate then 1 else 0 end) as decelerate,
            SUM(case when type=$eventImpact then 1 else 0 end) as impact,
            SUM(case when type=$turnLeft then 1 else 0 end) as turn_left,
            SUM(case when type=$turnRight then 1 else 0 end) as turn_right,
            SUM(case when type=$button then 1 else 0 end) as button";

        $eventIds = [
            $accelerate,
            $decelerate,
            $eventImpact,
            $turnLeft,
            $turnRight,
            $button
        ];

        $queryBuilder = DB::table('event')
            ->select(DB::raw($rawQuery))
            ->whereIn('type', $eventIds)
            ->whereBetween('time', [$startTime, $endTime]);

        if ($stkUser != null) {
            $queryBuilder->where('username', '=', $stkUser);
        }

        $result = $queryBuilder->first();
        if (!$result) {
            throw new NotFoundResourceException();
        }

        return [
            'total' => $result->total,

            // inneed to cast these values becuse PHP represent DECIMAL value as String.
            // https://stackoverflow.com/questions/36056686/why-sumcolumn-returns-a-string-instead-of-an-integer/36057647
            'accelerate' => (int) $result->accelerate,
            'decelerate' => (int) $result->decelerate,
            'impact' => (int) $result->impact,
            'turn_left' => (int) $result->turn_left,
            'turn_right' => (int) $result->turn_right,
            'button' => (int) $result->button,
        ];
    }

    public function device()
    {
        return $this->belongsTo('App\Models\Device', 'device_id', 'device_id');
    }

    public function cameras()
    {
        return $this->hasMany('App\Models\Camera', 'device_id', 'device_id');
    }

    public function videos()
    {
        return $this->hasMany('App\Models\Video', 'event_id', 'video_id');
    }

    public function video_converted()
    {
        return $this->hasOne('App\Models\VideoConverted', 'id', 'event_id');
    }
}
