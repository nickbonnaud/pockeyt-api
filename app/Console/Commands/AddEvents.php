<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Profile;
use GuzzleHttp\Exception\RequestException;

class AddEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new events to business posts';

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
        $businesses = Profile::whereNotNull('fb_page_id')->whereNotNull('fb_app_id')->get();
    }
}
