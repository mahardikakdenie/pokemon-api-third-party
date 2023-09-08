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
use Illuminate\Support\Facades\Validator;

class PokemonController extends Controller
{
    private $endpoint = 'https://pokeapi.co/api/v2/pokemon';
    /**
     * Display a listing of the resource.
     * @param Request
     */
    public function index(Request $request)
    {
        try {
            // Create RULES VALIDATION
            $validator = Validator::make($request->all(), [
                'limit' => 'integer|min:1',
                'offset' => 'integer|min:0',
            ]);

            // Checking Validation
            if ($validator->fails()) {
                return Json::exception($validator->errors()->first());
            }

            // receive limit and offset value
            $limit = $request->input('limit', 8);
            $offset = $request->input('offset', 0);


            // get data from Api
            $res = Helper::get($this->endpoint, compact('limit', 'offset'));
            $data = $res['results'];
            $result = array_map(function ($curr) {
                $pokemon = Helper::get("{$this->endpoint}/" . $curr['name']);
                $curr['abilities'] = $pokemon['abilities'];
                $curr['is_favorite'] = false;
                $favorite = Favorite::where('name', $curr['name']);;
                if ($favorite->exists()) {
                    $curr['is_favorite'] = true;
                    $curr['favorite_id'] = $favorite->first()->id;
                }
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Json::exception('Error Exceptions ' . $debug = env('APP_DEBUG', false) == true ? $e : '');
        } catch (\Illuminate\Database\QueryException $e) {
            return Json::exception('Error Query ' . $debug = env('APP_DEBUG', false) == true ? $e : '');
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
