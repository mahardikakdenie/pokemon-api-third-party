<?php

namespace App\Http\Lib;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class Helper
{
    public static function get($endpoint, $queryParam = [])
    {
        try {
            $response = Http::get($endpoint, $queryParam);
            $res = json_decode($response->getBody(), true);
            return $res;
        } catch (RequestException $e) {
            return response()->json(['error' => 'Terjadi masalah dengan permintaan HTTP'], 500);
        }
    }

    public static function entities($query, $entities)
    {
        if ($entities != null || $entities != '') {
            $entities = str_replace(' ', '', $entities);
            $entities = explode(',', $entities);

            try {
                return $query = $query->with($entities);
            } catch (\Throwable $th) {
                return Json::exception(null, validator()->errors());
            }
        }
    }
}
