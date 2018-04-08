<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Geoip;
use FluidXml\FluidXml;

class ApiController extends Controller
{
    
    public function json(Request $request, $ip = null)
    {
    
        $geo = new Geoip($request, $ip);
        
        return response()->json($geo->geoData);

    }

    public function xml(Request $request, $ip = null)
    {
        
        $geo = new Geoip($request, $ip);

        $xml = new FluidXml($geo->geoData);
        
        return response($xml);

    }

}
