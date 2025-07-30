<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Score\StoreRequest;
use App\Models\Score;
use DB;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    /**
     * Store
     */
    public function store(StoreRequest $request)
    {
        try {
            $request->validated();

            DB::beginTransaction();

            $score = Score::create([
                // fk
                'game_id' => $request->game_id,
                'team_id' => $request->team_id,
                'player_id' => $request->player_id,
                // column
                'datetime' => $request->datetime,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Score created successfully',
                'data' => [
                    'score' => $score,
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

            $score = Score::findOrFail($id);

            DB::beginTransaction();

            $score->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Score deleted successfully',
                'data' => [
                    'score' => $score,
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
}
