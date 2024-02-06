<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Arr;

class FilterRequest extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use HasUlids;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['data'];

    public static function requestGet(array $data): FilterRequest|null
    {
        $data = serialize(Arr::sortRecursive($data));

        return self::where('data', $data)->first();
    }

    public static function requestSet(array $data): FilterRequest|null
    {
        $data = serialize(Arr::sortRecursive($data));

        return self::firstOrCreate(['data' => $data]);
    }
}
