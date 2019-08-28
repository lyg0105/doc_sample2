<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MyTest extends Command
{
    protected $signature = 'mytest';
    protected $description = 'mytest Command !';

    public function handle()
    {
        echo "Hello World!";
    }
}
