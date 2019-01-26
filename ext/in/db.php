<?php
class dbAppAlex
{
	private $dbh;
	private $queryString;
	private $queryVars;
	
	function __construct()
	{
		include_once($_SERVER['DOCUMENT_ROOT'] . '/app/configuration.php');

		if(defined('DBNAME') && DBNAME != '')
		{
			$this->dbh = new PDO(DBENGINE . ":dbname=" . DBNAME . ";host=" . DBHOST . ";port=" . DBPORT, DBUSER, DBPASS);
			$this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		}
		$this->queryString = '';
		$this->queryVars = array();
	}
	
	function executeQuery($relation = '')
	{
		$countQuestionMark = count_chars($this->queryString);
		
		if(isset($countQuestionMark[63]) && $countQuestionMark[63] == count($this->queryVars)) //63 is byte-value for ? 
		{
			/***** this function uses the question mark preparation for query and an array for values
			$sth = $this->dbh->prepare('SELECT name, colour, calories FROM fruit WHERE calories < ? AND colour = ?');
			$sth->execute(array(150, 'red'));
			*/
			$sth = $this->dbh->prepare($this->queryString);
			$sth->execute($this->queryVars);
			if(!$sth)
			{
				error_log($this->dbh->errorCode() . ' :: ' . serialize($this->dbh->errorInfo()));
				return null;
			}

			if( strtoupper( substr($this->queryString,0 ,6) ) == 'SELECT')
			{
				return $sth->fetchAll(PDO::FETCH_ASSOC);
			}
			elseif( strtoupper( substr($this->queryString,0 ,6) ) == 'INSERT')
			{
				return $this->dbh->lastInsertId($relation);
			}
			else
			{
				return $sth->rowCount();
			}
		}
	}
	
	function setQueryString($queryString){$this->queryString = $queryString;}
	function setQueryVars($queryVars)
	{
		if(is_array($queryVars))
		{
			$this->queryVars = $queryVars;
		}
	}
}
?>