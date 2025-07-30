<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\StoreRequest;
use App\Http\Requests\Api\Player\UpdateRequest;
use App\Models\Player;
use DB;
use Illuminate\Http\Request;

/**
 *
 * @tags Player
 *
 */
class PlayerController extends Controller
{
    /**
     * List Players.
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

            $players = Player::when($request->search, callback: function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })->with('team')->latest()->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'players' => $players,
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

            $player = Player::with('team')->findOrFail($request->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'player' => $player,
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

            $player = Player::create([
                // fk
                'team_id' => $request->team_id,
                // column
                'name' => $request->name,
                'body_height' => $request->body_height,
                'body_weight' => $request->body_weight,
                'position' => $request->position,
                'back_number' => $request->back_number,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Player created successfully',
                'data' => [
                    'player' => $player,
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

            $player = Player::findOrFail($request->id);

            $player->update([
                'name' => $request->name,
                'body_height' => $request->body_height,
                'body_weight' => $request->body_weight,
                'position' => $request->position,
                'back_number' => $request->back_number,
            ]);

            $player->refresh();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Player updated successfully',
                'data' => [
                    'player' => $player,
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

            $player = Player::findOrFail($id);

            if (!$player->scores()->exists()) {
                DB::beginTransaction();

                $player->delete();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Player deleted successfully',
                    'data' => [
                        'player' => $player,
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to delete: this data is associated with other records',
                    'data' => [
                        'player' => $player,
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
