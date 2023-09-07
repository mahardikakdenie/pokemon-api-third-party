<?php

namespace App\Http\Controllers;

use Brryfrmnn\Transformers\Json;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Lib\Helper;
use App\Models\Ability;
use App\Models\Favorite;
use Illuminate\Support\Facades\DB;

class PokemonController extends Controller
{
    private $endpoint = 'https://pokeapi.co/api/v2/pokemon';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $res = Helper::get($this->endpoint, [
                'limit' => $request->input('limit', 20),
                'offset' => $request->input('offset', 0),
            ]);
            $data = $res['results'];
            $result = array_map(function ($curr) {
                $pokemon = Helper::get("{$this->endpoint}/" . $curr['name']);
                $detail =
                    $curr['abilities'] = $pokemon['abilities'];

                return $curr;
            }, $data);
            return Json::response($result);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Json::exception('Error Exceptions ' . $debug = env('APP_DEBUG', false) == true ? $e : '');
        } catch (\Illuminate\Database\QueryException $e) {
            return Json::exception('Error Query ' . $debug = env('APP_DEBUG', false) == true ? $e : '');
        } catch (\ErrorException $e) {
            return Json::exception('Error Exception ' . $debug = env('APP_DEBUG', false) == true ? $e : '');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $res = Helper::get("{$this->endpoint}/$id");
            return Json::response($res);
        } catch (\ErrorException $e) {
            return Json::exception('Error Exception ' . $debug = env('APP_DEBUG', false) == true ? $e : '');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
