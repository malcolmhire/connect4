<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\Connect4Play;

class CommandServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.connect4.command', function()
        {
            return new Connect4Play;
        });

        $this->commands(
            'command.connect4.command'
        );
    }
}
