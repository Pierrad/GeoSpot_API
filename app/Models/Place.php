<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Place extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'geolocation',
        'creator',
        'image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'geolocation' => 'json',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function getAround(mixed $geolocation, int $radius): array
    {
        $places = Place::all();
        $placesAround = [];
        foreach ($places as $place) {
            if ($this->isAround($place->geolocation, $geolocation, $radius)) {
                $placesAround[] = $place;
            }
        }
        return $placesAround;
    }

    private function isAround(mixed $geolocation1, mixed $geolocation2, int $radius): bool
    {
        $distance = $this->distance($geolocation1, $geolocation2);
        return $distance <= $radius;
    }

    private function distance(mixed $geolocation1, mixed $geolocation2): float
    {
        $lat1 = $geolocation1['latitude'];
        $lon1 = $geolocation1['longitude'];
        $lat2 = $geolocation2->latitude;
        $lon2 = $geolocation2->longitude;
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles * 1.609344; // convert to km
    }

}
