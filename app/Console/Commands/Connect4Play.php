<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class Connect4Play extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'connect4:play
                            {rows=7 : Rows on board}
                            {columns=7 : Columns on board}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play a game of connect 4 with the computer.';

    /**
     *  Default rows
     *
     * @var int
     */
    protected $rows = 7;

    /**
     *  Default columns
     *
     * @var int
     */
    protected $columns = 7;

    /**
     * Current player
     *
     * @var int
     */
    protected $current_player;

    /**
     * Counter positions array
     *
     * @var array
     */
    protected $board = [];

    /**
     * Track moves executed by both players.
     *
     * @var int
     */
    protected $moves = 0;

    /**
     * Class constructor
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
     * @return void
     */
    public function fire()
    {
        // Setup game
        $this->setUpGame([
            'rows' => $this->argument('rows'),
            'columns' => $this->argument('columns')
        ]);

        // Initalise game
        $this->initGame();
    }

    /**
     * Setup game from config
     *
     * @return void
     */
    protected function setUpGame($config)
    {
        // Set rows
        $this->setRows(isset($config['rows']) ? $config['rows'] : null);

        // Set columns
        $this->setColumns(isset($config['columns']) ? $config['columns'] : null);
    }

    /**
     * Initilise game
     *
     * @return void
     */
    protected function initGame()
    {
        // Empty root array
        $board = [];

        for ($i = 0; $i < $this->getRows(); $i++) {
            // Empty row array
            $board[$i] = [];

            for ($x = 0; $x < $this->getColumns(); $x++) {
                // Add column array
                $board[$i][$x] = null;
            }
        }

        // Set board
        $this->setBoard($board);

        // Set player
        $this->setCurrentPlayer();

        // Show instructions
        $this->showInstructions();

        // Play Game
        $this->playGame($board);
    }

    /**
     * Play game recursive function
     *
     * @return void
     */
    protected function playGame()
    {
        // Check if max turns
        if ($this->maximumTurnsCheck()) {
            // No winner then
            $this->showNoWinnerMessage();

            // Exit script
            exit;
        }

        // If computer
        if ($this->getCurrentPlayer() === 2) {
            // Sleep for 1 second
            sleep(1);
        }

        // Pick random column for computer
        $column = rand(0, $this->getColumns()-1);

        // If human player ask what column
        if ($this->getCurrentPlayer() === 1) {
            // Display question
            $column = $this->ask('Pick a column to play')-1;
        }

        // Set board variable
        $board = $this->getBoard();

        for ($row = $this->getRows()-1; $row >= 0; $row--) {
            // If slot is currently empty
            if ($board[$row][$column] === null) {
                // Current player
                $board[$row][$column] = $this->getCurrentPlayer();

                // Update moves
                $this->moves++;

                // Update the board
                $this->setBoard($board);

                // Print board
                $this->createBoard();

                // Check for winner
                if ($this->winnerCheck($row, $column)) {
                    // If winner is found
                    $this->showWinnerMessage();

                    // Exit script
                    exit;

                } else {
                    // Change player
                    $this->changePlayer();

                    // Play again
                    $this->playGame();
                }

                // Exit script
                exit;
            }

        }

        // Redo move again
        $this->playGame();
    }

    /**
     * Create connect4 board
     *
     * @return mixed
     */
    protected function createBoard()
    {
        // Add empty line
        $this->line('');

        // Display currnt player
        $this->line('Player '. $this->getCurrentPlayer() .': Move No. ' . $this->moves);

        // Add empty line
        $this->line('');

        // Print column headings
        for ($i = 0; $i < $this->getRows(); $i ++) {
            echo "\033[33m[".($i+1)."] \033[0m";
        }

        // Add empty line
        $this->line('');

        // Get board
        $board = $this->getBoard();

        // Loop through rows
        for ($row = 0; $row < $this->getRows(); $row++) {
            // Lopp though columns
            for ($column = 0; $column < $this->getColumns(); $column++) {
                // Add player color
                if ($board[$row][$column] === 1) {
                    // Add player 1 column
                    echo "\033[31m|O|\033[0m ";
                } else if($board[$row][$column] === 2) {
                    // Add player 2 column
                    echo "\033[34m|O|\033[0m ";
                } else {
                    // Add empty column
                    echo "\033[0m| |\033[0m ";
                }
            }

            // Add empty line
            $this->line('');
        }
    }

    /**
     * Print message to console with instructions
     *
     * @return string
     */
    protected function showInstructions()
    {
        // Add empty line
        $this->line('');

        // Shoe message
        $this->info('Player 1 (You) - Red , Player 2 (Computer) - Blue');

        // Add empty line
        $this->line('');
    }

    /**
     * Setter for number of rows
     *
     * @param $row [int]
     */
    public function setRows($rows = null)
    {
        if (!$rows) {
            return;
        }

        $this->rows = $rows;
    }

    /**
     * Getter to return rows
     *
     * @return [int]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Setter for number of columns
     *
     * @param $columns [int]
     */
    public function setColumns($columns = null)
    {
        if (!$columns) {
            return;
        }

        $this->columns = $columns;
    }

    /**
     * Getter to return columns
     *
     * @return [int]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Setter for current player
     *
     * @param $player [int]
     */
    protected function setCurrentPlayer($player = null)
    {
        if (!$player) {
            // Randomise if no player set
            $player = rand(1,2);
        }

        return $this->current_player = $player;
    }

    /**
     * Getter to return current player
     *
     * @return [int]
     */
    public function getCurrentPlayer()
    {
        return $this->current_player;
    }

    /**
     * Getter to return board
     *
     * @return array
     */
    protected function getBoard()
    {
        return $this->board;
    }

    /**
     * Sets the board
     *
     * @param $board [array]
     */
    protected function setBoard($board)
    {
        $this->board = $board;
    }

    /**
     * Winner message
     *
     * @return string
     */
    protected function showWinnerMessage()
    {
        // Add empty line
        $this->line('');

        // Show winning message
        $this->info('Player '. $this->getCurrentPlayer().' wins the game!');

        // Add empty line
        $this->line('');
    }

    /**
     * No winner message
     *
     * @return string
     */
    protected function showNoWinnerMessage(){
        // Add empty line
        $this->line('');

        // Show now winner
        $this->error('No winner for this round.');

        // Add empty line
        $this->line('');
    }

    /**
     * Change player
     *
     * @return void
     */
    protected function changePlayer()
    {
        $this->setCurrentPlayer($this->getCurrentPlayer() === 1 ? 2 : 1);
    }

    /**
     * Check for winner
     *
     * @return boolean
     */
    protected function winnerCheck($row, $column)
    {
        if ($this->checkRow($row, $column) || $this->checkColumn($row, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Check row for discs
     *
     * @return boolean
     */
    private function checkRow($row, $column)
    {
        // Get board
        $board = $this->getBoard();

        // Get cell player
        $player = $board[$row][$column];

        // Init counter
        $count = 0;

        // Check backward
        for ($i = $column; $i >= 0; $i--) {

            if ($board[$row][$i] !== $player){
                break;
            }

            $count++;
        }

        // Check forward
        for ($i = $column + 1; $i < $this->getColumns(); $i++) {

            if ($board[$row][$i] !== $player) {
                break;
            }

            $count++;
        }

        return $count >= 4 ? true : false;
    }

    /**
     * Check column for discs
     *
     * @return boolean
     */
    private function checkColumn($row, $column)
    {
        // Less than 4 pieces from bottom
        if ($row >= $this->getRows()-3) {
            return false;
        }

        // Get board
        $board = $this->getBoard();

        // Get cell player
        $player = $board[$row][$column];

        // Check forward
        for ($i = $row + 1; $i <= $row + 3; $i++) {

            if ($board[$i][$column] !== $player) {
                return false;
            }

        }

        return true;
    }

    /**
     * Check if maximum turns reached
     *
     * @return booleen
     */
    protected function maximumTurnsCheck()
    {
        if ($this->moves >= ($this->getRows() * $this->getColumns())) {
            return true;
        }

        return false;
    }
}
