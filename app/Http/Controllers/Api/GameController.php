<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Game\StoreRequest;
use App\Http\Requests\Api\Game\UpdateRequest;
use App\Models\Game;
use DB;
use Illuminate\Http\Request;

/**
 *
 * @tags Game
 *
 */
class GameController extends Controller
{
    /**
     * List Games.
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                /**
                 * search.
                 * @var string
                 */
                'search' => ['string'],
            ]);

            $games = Game::when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('location', 'like', "%{$search}%")
                        ->orWhereRelation('home_team', 'name', 'like', "%{$search}%")
                        ->orWhereRelation('away_team', 'name', 'like', "%{$search}%");
                });
            })->with([
                        'home_team',
                        'away_team',
                    ])->latest()->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'games' => $games,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Show
     */
    public function show(Request $request)
    {
        try {
            $request->validate([
                /**
                 * id.
                 * @var string
                 */
                'id' => ['required', 'string', 'uuid'],
            ]);

            $game = Game::with([
                'home_team',
                'away_team',
            ])->findOrFail($request->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'game' => $game,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Store
     */
    public function store(StoreRequest $request)
    {
        try {
            $request->validated();

            DB::beginTransaction();

            $game = Game::create([
                // fk
                'home_team_id' => $request->home_team_id,
                'away_team_id' => $request->away_team_id,
                // column
                'location' => $request->location,
                'datetime' => $request->datetime,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Game created successfully',
                'data' => [
                    'game' => $game,
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update
     */
    public function update(UpdateRequest $request)
    {
        try {
            $request->validated();

            DB::beginTransaction();

            $game = Game::findOrFail($request->id);

            $game->update([
                // column
                'location' => $request->location,
                'datetime' => $request->datetime,
            ]);

            $game->refresh();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Game updated successfully',
                'data' => [
                    'game' => $game,
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete
     */
    public function delete($id)
    {
        try {
            $validator = validator(['id' => $id], [
                'id' => ['required', 'string', 'uuid'],
            ]);

            $validator->validate();

            $game = Game::findOrFail($id);

            if (!$game->scores()->exists()) {
                DB::beginTransaction();

                $game->delete();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Game deleted successfully',
                    'data' => [
                        'game' => $game,
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to delete: this data is associated with other records',
                    'data' => [
                        'game' => $game,
                    ]
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
