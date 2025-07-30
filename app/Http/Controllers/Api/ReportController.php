<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;

class ReportController extends Controller
{
    /**
     * List Reports.
     */
    public function index()
    {
        try {
            $games = Game::with([
                'home_team',
                'away_team',
                'scores.player'
            ])->latest('datetime')
                ->get();

            $reports = [];

            foreach ($games as $game) {
                // calculate the final score
                $home_score = $game->scores->where('team_id', $game->home_team_id)->count();
                $away_score = $game->scores->where('team_id', $game->away_team_id)->count();
                $final_score = "{$home_score} - {$away_score}";

                // determine the final status of the match
                $match_status = '';
                if ($home_score > $away_score) {
                    $match_status = 'Home Team Win';
                } elseif ($away_score > $home_score) {
                    $match_status = 'Away Team Win';
                } else {
                    $match_status = 'Draw';
                }

                // find the playee with the most goals scores
                $player_goals = [];
                foreach ($game->scores as $score) {
                    // make sure the player exists (it should exist because it is eager loaded)
                    if ($score->player) {
                        $player_goals[$score->player->name] = ($player_goals[$score->player->name] ?? 0) + 1;
                    }
                }

                // top scorer player
                $top_scorer = 'N/A';
                if (!empty($player_goals)) {
                    $top_scorer = array_keys($player_goals, max($player_goals))[0];
                }

                // accumulated total wins of home and away teams until this match
                $homee_team_accumulated_win = $this->calculateTeamWinsUpToDate(
                    $game->home_team_id,
                    $game->datetime
                );
                $away_team_accumulated_win = $this->calculateTeamWinsUpToDate(
                    $game->away_team_id,
                    $game->datetime
                );

                $reports[] = [
                    'match_schedule' => $game->datetime->format('Y-m-d H:i:s'),
                    'home_team' => $game->home_team->name,
                    'away_team' => $game->away_team->name,
                    'final_score' => $final_score,
                    'status' => $match_status,
                    'top_goal_scorer' => $top_scorer,
                    'home_team_wins_accumulated' => $homee_team_accumulated_win,
                    'away_team_wins_accumulated' => $away_team_accumulated_win,
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'reports' => $reports,
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
     * calculate total team wins up to a certain date.
     *
     * @param string $team_id
     * @param string|\Carbon\Carbon $up_to_date_time
     * @return int
     */
    protected function calculateTeamWinsUpToDate(string $team_id, $up_to_date_time): int
    {
        $wins = 0;

        // retrieve all historical games in which this team was involved, up to the specified date
        $historical_games = Game::with('scores')
            ->where(function ($query) use ($team_id) {
                $query->where('home_team_id', $team_id)
                    ->orWhere('away_team_id', $team_id);
            })
            ->where('datetime', '<=', $up_to_date_time)
            ->get();

        foreach ($historical_games as $historical_game) {
            $home_score = $historical_game->scores->where('team_id', $historical_game->home_team_id)->count();
            $away_score = $historical_game->scores->where('team_id', $historical_game->away_team_id)->count();

            // tetermine whether the team being checked team_id won this match
            if ($historical_game->home_team_id === $team_id) {
                if ($home_score > $away_score) {
                    $wins++;
                }
            } elseif ($historical_game->away_team_id === $team_id) {
                if ($away_score > $home_score) {
                    $wins++;
                }
            }
        }

        return $wins;
    }
}
