<?php

namespace App\Console\Commands;

use App\CV;
use App\Job;
use App\Members;
use App\User;
use Illuminate\Console\Command;

class CreateRecommendationListOfUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:recommendation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create list recommendation job or cv to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cv_ids = CV::all()->pluck('id')->toArray();
        $job_ids = Job::all()->pluck('id')->toArray();
        for($i = 0; $i < 100; $i++) {
            Members::create([
               "name" => "user_".$i,
               "password" => "123456",
               "cv_id" => array_pop($cv_ids),
                "job_id" => array_pop($job_ids),
            ]);
            shuffle($cv_ids);
            shuffle($job_ids);
        }
    }
}
