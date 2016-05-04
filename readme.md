# Connect4

A a PHP CLI connect4 game, where a human can play the computer with simple AI.
Game is build on Lumen framework just for minimal installation and use of artisan.

## Lumen PHP Framework

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.


## Install

This guide assumes that you have git and composer installed globally on your system

**Clone repository**
`git clone https://github.com/malcolmhire/connect4`

**Composer dependances**
`composer install`


## Play Game

To play game in command line run

`php artisan connect4:play`

## Code Review

The main package is located in packages/MalcolmHire/Connect4/Console/Connect4Command.php, this could be abstracted into a composer package but I have left in place for easy review.
I have utilised a `Illuminate\Console\Command` to easily work with CLI and used Laravel Lumen for the use of Artisan to run commands in he CLI.
All code should be to a PSR-4 standard
Tests, there are no test currently in place, if this was a production app I would write a few tests to go with the app.
