<?php

namespace MalcolmHire\Connect4;

use Illuminate\Support\ServiceProvider;

class Connect4ServiceProvider extends ServiceProvider {

    /**
    * Bootstrap the application services.
    *
    * @return void
    */
    public function boot()
    {
    //
    }

    /**
    * Register the application services.
    *
    * @return void
    */
    public function register()
    {
        $this->registerCommands();
    }

    public function registerCommands()
    {
        $this->registerConnect4Command();

        $this->commands(
            'connect4::commands.play'
        );
    }

    public function registerConnect4Command()
    {
        $this->app['connect4::commands.play'] = $this->app->share(function($app)
        {
            return new Console\Connect4Command;
        });
    }
}
