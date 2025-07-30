<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Player;
use App\Models\Score;
use App\Models\Team;
use Carbon\Carbon;
use DB;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $this->truncateModel();
        $this->createTeam();
        $this->createPlayer();
        $this->createGame();
        $this->createScore();
    }

    function truncateModel()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Score::truncate();
        Player::truncate();
        Game::truncate();
        Team::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    function createTeam()
    {
        $faker = Faker::create();

        $total = 10;

        $this->command->getOutput()->progressStart($total);

        for ($i = 1; $i <= $total; $i++) {
            Team::create([
                // column
                'name' => "{$faker->lastName()} FC",
                'logo_url' => "https://picsum.photos/id/{$i}/200/200",
                'founding_year' => $faker->year(),
                'address' => $faker->address,
                'city' => $faker->city,
            ]);

            // progress bar
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info("\n Team seeding completed!");
    }

    function createPlayer()
    {
        // read json
        $json_path = database_path('data/player_positions.json');
        $positions = json_decode(File::get($json_path), true);

        if (empty($positions)) {
            echo "Warning: player_positions.json is empty or invalid.\n";
            return;
        }

        $faker = Faker::create();

        $all_team_ids = Team::pluck('id')->toArray();

        $max_unique_back_number = 25;

        $players_per_team = min($max_unique_back_number, 18);

        $total = count($all_team_ids) * $players_per_team;

        $this->command->getOutput()->progressStart($total);

        foreach ($all_team_ids as $team_id) {
            // tracks back numbers already used in this team
            $used_back_numbers = [];

            $possible_back_number = range(1, $max_unique_back_number);

            for ($i = 0; $i < $players_per_team; $i++) {

                $selected_position = $faker->randomElement($positions);

                // select unique back number
                $available_back_numbers = array_diff($possible_back_number, $used_back_numbers);
                $selected_back_number = $faker->randomElement($available_back_numbers);
                $used_back_numbers[] = $selected_back_number;

                Player::create([
                    'team_id'     => $team_id,
                    'name'        => $faker->name,
                    'body_height' => $faker->numberBetween(150, 190),
                    'body_weight' => $faker->numberBetween(45, 90),
                    'position'    => $selected_position,
                    'back_number' => $selected_back_number,
                ]);

                $this->command->getOutput()->progressAdvance();
            }
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info("\n Player seeding completed!");
    }

    function createGame()
    {
        $faker = Faker::create();

        $total = 10;

        $this->command->getOutput()->progressStart($total);

        for ($i = 1; $i <= $total; $i++) {

            $teams = Team::inRandomOrder()->limit(2)->get();

            Game::create([
                // fk
                'home_team_id' => $teams[0]->id,
                'away_team_id' => $teams[1]->id,
                // column
                'location' => $faker->address,
                'datetime' => $faker->dateTime(),
            ]);

            // progress bar
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info("\n Game seeding completed!");
    }

    function createScore()
    {
        $faker = Faker::create();

        $total = 50;

        $this->command->getOutput()->progressStart($total);

        for ($i = 1; $i <= $total; $i++) {
            $game = Game::inRandomOrder()->first();

            // select random team from home_team or away_team
            $scoring_team_id = $faker->randomElement([$game->home_team_id, $game->away_team_id]);

            // select random player
            $player = Player::where('team_id', $scoring_team_id)->inRandomOrder()->first();

            $game_start_time = Carbon::parse($game->datetime);

            // 90 minutes for maximum score time
            $max_score_time = $game_start_time->copy()->addMinutes(90);

            // select random game_start_time dan max_score_time
            $score_datetime = $faker->dateTimeBetween($game_start_time, $max_score_time);

            Score::create([
                'game_id'   => $game->id,
                'team_id'   => $scoring_team_id,
                'player_id' => $player->id,
                'datetime'  => $score_datetime,
            ]);

            // progress bar
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info("\n Score seeding completed!");
    }
}
