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
    * Register service provider.
    *
    * @return void
    */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * Register commands.
     *
     * @return void
     */
    public function registerCommands()
    {
        $this->registerConnect4Command();

        $this->commands(
            'connect4::commands.play'
        );
    }

    /**
     * Register connect4:play command.
     *
     * @return void
     */
    public function registerConnect4Command()
    {
        $this->app['connect4::commands.play'] = $this->app->share(function($app)
        {
            return new Console\Connect4Command;
        });
    }
}
