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
     * Show the form for creating a new resource.
     */
    public function addFavorite(Request $request)
    {
        try {
            DB::beginTransaction();
            $pokemonId = $request->pokemon_id;
            $pokemonResponse = Helper::get("{$this->endpoint}/$pokemonId");
            $pokemon = $pokemonResponse['forms'][0];
            $abilities = $pokemonResponse['abilities'];

            // dd($abilities);


            $pokemonFavorite = new Favorite();
            $pokemonFavorite->name = $pokemon['name'];
            $pokemonFavorite->url = $pokemon['url'];
            $pokemonFavorite->pokemon_id = $pokemonId;
            $pokemonFavorite->save();

            foreach ($abilities as $key => $value) {
                $url = $value['ability']['url'];
                $name = $value['ability']['name'];
                $ability = new Ability();
                $ability->name = $name;
                $ability->url = $url;
                $ability->favorite_id = $pokemonFavorite->id;
                DB::commit();
                $ability->save();
            }


            DB::commit();
            return Json::response($pokemonFavorite);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return Json::exception('Error Exceptions ' . $debug = env('APP_DEBUG', false) == true ? $e : '');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Json::exception('Error Query ' . $debug = env('APP_DEBUG', false) == true ? $e : '');
        } catch (\ErrorException $e) {
            DB::rollBack();
            return Json::exception('Error Exception ' . $debug = env('APP_DEBUG', false) == true ? $e : '');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function listFavorites(Request $request)
    {
        try {
            $favorites = Favorite::entities($request->entities)->get();

            return Json::response($favorites);
        } catch (\Throwable $th) {
            //throw $th;
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
