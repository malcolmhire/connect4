<?php

namespace MalcolmHire\Connect4\Console;

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Connect4 extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
    protected $signature = 'command:name';

	/**
	 * The console command description.
	  *
	* @var string
	 */
    protected $description = '';


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
	protected $grid = [];

    /**
	 * Track moves executed by both players.
	 * 
	 * @var int
	 */
	protected $_moves = 0;

	
	public function __construct($config)
	{
		// Setup game
		$this->setUpGame($config);
	}

	/**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
    	// Set player
		$this->setCurrentPlayer();

		if ($this->confirm('Do you wish to continue? [y|N]')) {
			// Initialize board
			$this->initBoard();
		}
    }

	protected function initBoard()
	{
		// Empty root array
		$board = [];
		
		for ($i = 0; $i < $this->getRows(); $i++) {
			// Empty column array
			$board[$i] = [];
			
			for ($x = 0; $x < $this->getColumns(); $x++){
				// Add empty array
				$board[$i][$x] = 0;
			}
		}
		
		// Set grid 
		$this->setBoard($board);

		// Play Turn
		$this->playTurn($board);
	} 

	protected function setUpGame($config)
	{
		// Set rows
		$this->setRows(isset($config['rows']) ? $config['rows'] : null);

		// Set columns
		$this->setColumns(isset($config['columns']) ? $config['columns'] : null);
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
	 * Creates a 'move' for each player by randomly choosing a column to drop a piece into.
	 */
	protected function playTurn(){
		
		//Check if total moves reached. (Recursive baseline)
		if ( $this->_moves >= ( $this->getRows() * $this->getColumns() )) {
			
			//No winner then =(
			$this->_showNoWinnerMessage();
			
			exit;
		}
		
		//Random column chosen for placing chips
		$_target_col = rand(0, $this->getColumns()-1);
		$_current_board = $this->getBoard();
		
		for( $row = $this->getRows()-1; $row>=0; $row-- ){

			//If slot is currently empty
			if( $_current_board[$row][$_target_col] === 0 ){
				
				//Set slot to current player
				$_current_board[$row][$_target_col] = $this->getCurrentPlayer();
				
				//Update the no. of moves, might wana setter/getter this
				$this->_moves++;
				
				//Update the board
				$this->setBoard($_current_board);
				
				//Print current board
				$this->generateBoard();
				
				//Check for winner
				if ( $this->_checkForWinner( $row, $_target_col ) ) {
					
					//If winner is found
					$this->_showWinnerMessage();
					
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
		
		//If it comes to here, it means no slots are empty (column is full). Redo move again
		$this->playTurn();
	}

	protected function generateBoard()
	{	
		// Add return
		echo PHP_EOL;

		echo 'Player '. $this->getCurrentPlayer() .': Move No. ' . $this->_moves;

		// Add return
		echo PHP_EOL;

		$_board_array = $this->getBoard();

		// Init command colour
		//$command_color = new Color();
		
		// Add return
		echo PHP_EOL;

		// Print column Numbers
		for ($i = 0; $i < $this->getRows(); $i ++ ) {
			//echo "\033[34m ".$i+1." \033[0m";
			echo "\033[33m[".($i+1)."] \033[0m";
		}

		// Add return
		echo PHP_EOL;
				
		for ($i = 0; $i < $this->getRows(); $i++ ){
			
			for ($j = 0; $j < $this->getColumns() ; $j++ ){
				if ( $_board_array[$i][$j] === 1 ){
					echo "\033[31m ".$_board_array[$i][$j]." \033[0m ";
					
				} else if( $_board_array[$i][$j] === 2 ){
					echo "\033[34m ".$_board_array[$i][$j]." \033[0m ";
				} else {
					echo "\033[0m ".$_board_array[$i][$j]." \033[0m ";
				}
			}
			
			// Add return
			echo PHP_EOL;
		}

		//$this->command->ask('What is your name?');

	}
	
	/**
	 * Displays the message for the winner
	 */
	protected function _showWinnerMessage(){
		echo PHP_EOL . "\033[31mPlayer " . $this->getCurrentPlayer() ." wins the game! \033[0m " . PHP_EOL;
	}
	
	/**
	 * Displays the message if there's no winner
	 */
	protected function _showNoWinnerMessage(){
		echo PHP_EOL . "\033[31mNo winner for this round. \033[0m " . PHP_EOL;
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
	protected function _checkForWinner( $row, $col ){
		
		if($this->_horizontalCheck($row, $col) 
				|| $this->_verticalCheck($row, $col) 
		){
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Check for horizontal pieces
	 * 
	 * @return boolean
	 */
	private function _horizontalCheck( $row, $col ){
		
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
	private function _verticalCheck($row, $col){
	
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
}
