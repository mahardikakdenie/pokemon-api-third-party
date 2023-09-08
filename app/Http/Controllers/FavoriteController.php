<?php

namespace App\Http\Controllers;

use App\Http\Lib\Helper;
use App\Models\Ability;
use App\Models\AbilityFavorite;
use App\Models\AbiltyFavorite;
use App\Models\Favorite;
use Brryfrmnn\Transformers\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    private $endpoint = 'https://pokeapi.co/api/v2/pokemon';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $favorites = Favorite::entities($request->entities)
                ->paginate($request->input('paginate', 8));

            return Json::response($favorites);
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $pokemonId = $request->pokemon_id;
            $pokemonResponse = Helper::get("{$this->endpoint}/$pokemonId");
            $pokemon = $pokemonResponse['forms'][0];
            $abilities = $pokemonResponse['abilities'];

            // Validation
            $findPokemon = Favorite::where('name', $pokemon['name'])->first();
            if ($findPokemon) {
                return Json::exception("This Pokemon is already in the Favorites list");
            }

            $pokemonFavorite = new Favorite();
            $pokemonFavorite->name = $pokemon['name'];
            $pokemonFavorite->url = $pokemon['url'];
            $pokemonFavorite->pokemon_id = $pokemonId;
            $pokemonFavorite->save();

            foreach ($abilities as $key => $value) {
                $url = $value['ability']['url'];
                $name = $value['ability']['name'];
                $findAbilit = Ability::where('name', $name)->first();
                if (!$findAbilit) {
                    $ability = new Ability();
                    $ability->name = $name;
                    $ability->url = $url;
                    DB::commit();
                    $ability->save();

                    $abilityFavorite = new AbilityFavorite();
                    $abilityFavorite->favorite_id = $pokemonFavorite->id;
                    $abilityFavorite->ability_id = $ability->id;
                    DB::commit();
                    $abilityFavorite->save();
                } else {
                    $abilityFavorite = new AbilityFavorite();
                    $abilityFavorite->favorite_id = $pokemonFavorite->id;
                    $abilityFavorite->ability_id = $findAbilit->id;
                    DB::commit();
                    $abilityFavorite->save();
                }
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        try {
            DB::beginTransaction();
            $favorite = Favorite::findOrFail($id);
            $abilityFavorite = AbilityFavorite::where('favorite_id', $favorite->id)->get();

            foreach ($abilityFavorite as $key => $value) {
                $value->delete();
            }
            $favorite->delete();
            DB::commit();
            return Json::response($favorite);
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
}
