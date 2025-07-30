<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Team\StoreRequest;
use App\Http\Requests\Api\Team\UpdateRequest;
use App\Models\Team;
use DB;
use Illuminate\Http\Request;

/**
 *
 * @tags Team
 *
 */
class TeamController extends Controller
{
    /**
     * List Teams.
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

            $teams = Team::when($request->search, callback: function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })->latest()->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'teams' => $teams,
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

            $team = Team::findOrFail($request->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'team' => $team,
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

            $team = Team::create([
                'name' => $request->name,
                'logo_url' => $request->logo_url,
                'founding_year' => $request->founding_year,
                'address' => $request->address,
                'city' => $request->city,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Team created successfully',
                'data' => [
                    'team' => $team,
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

            $team = Team::findOrFail($request->id);

            $team->update([
                'name' => $request->name,
                'logo_url' => $request->logo_url,
                'founding_year' => $request->founding_year,
                'address' => $request->address,
                'city' => $request->city,
            ]);

            $team->refresh();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Team updated successfully',
                'data' => [
                    'team' => $team,
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

            $team = Team::findOrFail($id);

            if (
                !$team->players()->exists() ||
                !$team->home_games()->exists() ||
                !$team->away_games()->exists() ||
                !$team->scores()->exists()
            ) {
                DB::beginTransaction();

                $team->delete();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Team deleted successfully',
                    'data' => [
                        'team' => $team,
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to delete: this data is associated with other records',
                    'data' => [
                        'team' => $team,
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
