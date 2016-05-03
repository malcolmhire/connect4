<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use MalcolmHire\Connect4\Connect4;  

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

    protected function setUpGame($config)
    {
        // Set rows
        $this->setRows(isset($config['rows']) ? $config['rows'] : null);

        // Set columns
        $this->setColumns(isset($config['columns']) ? $config['columns'] : null);
    }

    protected function initGame()
    {
        // Empty root array
        $board = [];
        
        for ($i = 0; $i < $this->getRows(); $i++) {
            // Empty row array
            $board[$i] = [];
            
            for ($x = 0; $x < $this->getColumns(); $x++){
                // Add column array
                $board[$i][$x] = null;
            }
        }
        
        // Set board 
        $this->setBoard($board);

        // Play Game
        $this->playGame($board);
    }

    protected function playGame()
    {
        // Check if max turns
        if ($this->maximumTurnsCheck()) {
            // No winner then
            $this->showWinnerMessage();
            
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
            $column = $this->choice('Pick a column to play', range(1, $this->getColumns()))-1;
        }
        
        $_current_board = $this->getBoard();
        
        for( $row = $this->getRows()-1; $row>=0; $row-- ){

            //If slot is currently empty
            if( $_current_board[$row][$column] === null ){
                
                //Set slot to current player
                $_current_board[$row][$column] = $this->getCurrentPlayer();
                
                // Update moves
                $this->setMoves();
                
                // Update the board
                $this->setBoard($_current_board);
                
                //Print current board
                $this->generateBoard();
                
                //Check for winner
                if ( $this->_checkForWinner( $row, $column ) ) {
                    
                    // If winner is found
                    $this->showWinnerMessage();
                    
                    exit;
                    
                } else {
                    //Change player
                    $this->_togglePlayer();
                    
                    //Drop the piece
                    $this->playTurn();
                }
                
                //exit once a piece is dropped for this move
                exit;
            } 
            
        }
        
        // Redo move again
        $this->playGame();
    }

    protected function generateBoard()
    {   
        // Add empty line
        $this->line('');

        // Display currnt player
        $this->line('Player '. $this->getCurrentPlayer() .': Move No. ' . $this->moves);

        // Add empty line
        $this->line('');

        // Print column headings
        for ($i = 0; $i < $this->getRows(); $i ++ ) {
            echo "\033[33m[".($i+1)."] \033[0m";
        }

        // Add empty line
        $this->line('');

        $_board_array = $this->getBoard();
      
        for ($i = 0; $i < 7; $i++ ){
            
            for ($j = 0; $j < 7; $j++ ){
                if ( $_board_array[$i][$j] === 1 ){
                    echo "\033[31m|O|\033[0m "; 
                } else if( $_board_array[$i][$j] === 2 ){
                    echo "\033[34m|O|\033[0m ";
                } else {
                    echo "\033[0m| |\033[0m ";
                }
            }
            
            // Add empty line
            $this->line('');
        }
    }

    public function setRows($rows = null)
    {   
        if (!$rows) {
            return;
        }

        $this->rows = $rows;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function setColumns($columns = null)
    {   
        if (!$columns) {
            return;
        }

        $this->columns = $columns;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    protected function setMoves()
    {   
        $this->moves = $this->moves++;
    }

    protected function getMoves()
    {
        return $this->moves;
    }

    protected function setCurrentPlayer($player = null)
    {   
        if (!$player) {
            // Randomise if no player set
            $player = rand(1,2);
        }

        return $this->current_player = $player;
    }

    public function getCurrentPlayer()
    {
        return $this->current_player;
    }

    /**
     * Gets the board
     * 
     * @return array
     */
    protected function getBoard()
    {
        return $this->board;    
    }
    
    /**
     * Sets the board 
     */
    protected function setBoard($board)
    {
        $this->board = $board;  
    }

    /**
     * Displays the message for the winner
     */
    protected function showWinnerMessage(){
        // Add empty line
        $this->line('');

        // Show winning message
        $this->info('Player '. $this->getCurrentPlayer().' wins the game!');

        // Add empty line
        $this->line('');
    }
    
    /**
     * Displays the message if there's no winner
     */
    protected function _showNoWinnerMessage(){
        // Add empty line
        $this->line('');

        // Show now winner
        $this->error('No winner for this round.');

        // Add empty line
        $this->line('');
    }
    
    /**
     * Switches the turn to the other player
     */
    protected function _togglePlayer(){
        
        $this->setCurrentPlayer($this->getCurrentPlayer()===1?2:1);
        
    }

    /**
     * Check for winner
     * 
     * @return boolean
     */
    protected function _checkForWinner($row, $col)
    {
        if ($this->horizontalCheck($row, $col) || $this->verticalCheck($row, $col)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check for horizontal pieces
     * 
     * @return boolean
     */
    private function horizontalCheck( $row, $col )
    {
        
        $_board_array = $this->getBoard();
        $_player = $_board_array[$row][$col];
        $_count = 0;
        
        //count towards the left of current piece
        for ( $i = $col; $i>=0; $i-- )
        {
            
            if( $_board_array[$row][$i] !== $_player ){
                
                break;
                
            }
            
            $_count++;
            
        }
        
        //count towards the right of current piece
        for ( $i = $col + 1; $i<$this->getColumns(); $i++ )
        {
                
            if( $_board_array[$row][$i] !== $_player ){
        
                break;
        
            }
                
            $_count++;
                
        }
        
        return $_count>=4 ? true : false;
        
    }
    
    /**
     * Check for vertical pieces
     * 
     * @return boolean
     */
    private function verticalCheck($row, $col)
    {
    
        //if current piece is less than 4 pieces from bottom, skip check
        if ( $row >= $this->getRows()-3 ) {
            
            return false;
            
        }
        
        $_board_array = $this->getBoard();
        $_player = $_board_array[$row][$col];
        
        for ( $i = $row + 1; $i <= $row + 3; $i++ ){
            
            if($_board_array[$i][$col] !== $_player){
                
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
        if ($this->getMoves() >= ($this->getRows() * $this->getColumns())) {
            return true;
        }

        return false;
    }
}
