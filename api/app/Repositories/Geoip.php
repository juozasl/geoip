<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use MaxMind\Db\Reader;

class Geoip
{
    
    public $databaseFile;
    public $ip;
    public $geoData;

    function __construct($request, $ip = null)
    {

        $this->databaseFile = storage_path('app/GeoLite2-City.mmdb');
        
        if ($ip == null)
        {

            $this->ip = $request->ip();

        }
        else
        {

            $this->ip = $ip;

        }

        $this->get();
        $this->selectData();

    }

    
    public function get()
    {

        $reader = new Reader($this->databaseFile);
        $this->geoData = $reader->get($this->ip);
        $reader->close();

    }


    public function selectData()
    {

        $array = [];

        $geoData = $this->geoData;

        $array['ip'] = $this->ip;

        $array['country_code'] = isset($geoData['country']['iso_code']) ? $geoData['country']['iso_code'] : null;

        $array['country_name'] = isset($geoData['country']['names']['en']) ? $geoData['country']['names']['en'] : null;

        $array['region_code'] =  isset($geoData['subdivisions'][0]['iso_code']) ? $geoData['subdivisions'][0]['iso_code'] : null;

        $array['region_name'] =  isset($geoData['subdivisions'][0]['names']['en']) ? $geoData['subdivisions'][0]['names']['en'] : null;

        $array['city'] =  isset($geoData['city']['names']['en']) ? $geoData['city']['names']['en'] : null;

        $array['zip_code'] =  isset($geoData['postal']['code']) ? $geoData['postal']['code'] : null;
        
        $array['time_zone'] =  isset($geoData['location']['time_zone']) ? $geoData['location']['time_zone'] : null;

        $array['latitude'] = isset($geoData['location']['latitude']) ? $geoData['location']['latitude'] : null;

        $array['longitude'] = isset($geoData['location']['longitude']) ? $geoData['location']['longitude'] : null;

        $array['metro_code'] =  isset($geoData['location']['metro_code']) ? $geoData['location']['metro_code'] : null;

        $this->geoData = $array;


    }
        

}

