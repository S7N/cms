<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database-based session class.
 *
 * Sample schema:
 *
 *     CREATE TABLE  `sessions` (
 *         `session_id` VARCHAR( 24 ) NOT NULL,
 *         `last_active` INT UNSIGNED NOT NULL,
 *         `contents` TEXT NOT NULL,
 *         PRIMARY KEY ( `session_id` ),
 *         INDEX ( `last_active` )
 *     ) ENGINE = MYISAM ;
 *
 * @package    Session
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Session_Database extends Session {

	// Database instance
	protected $_db;

	// Database table name
	protected $_table = 'sessions';

	// The current session id
	protected $_session_id;

	// The old session id
	protected $_update_id;

	// Update the session?
	protected $_update = FALSE;

	public function __construct(array $config = NULL)
	{
		if ( ! isset($config['group']))
		{
			// Use the default group
			$config['group'] = 'default';
		}

		// Load the database
		$this->_db = Database::instance($config['group']);

		if (isset($config['table']))
		{
			// Set the table name
			$this->_table = (string) $config['table'];
		}

		parent::__construct($config);
	}

	public function _read($id = NULL)
	{
		if ($id OR $id = Cookie::get($this->_name))
		{
			$result = DB::query(Database::SELECT, "SELECT contents FROM {$this->_table} WHERE session_id = :id LIMIT 1")
				->param(':id', $id)
				->execute($this->_db);

			if ($result->count())
			{
				// Set the current session id
				$this->_session_id = $this->_update_id = $id;

				// Return the contents
				return $result->get('contents');
			}
		}

		// Create a new session id
		$this->_regenerate();

		return NULL;
	}

	protected function _regenerate()
	{
		// Create the query to find an ID
		$query = DB::query(Database::SELECT, "SELECT session_id FROM {$this->_table} WHERE session_id = :id LIMIT 1")
			->bind(':id', $id);

		do
		{
			// Create a new session id
			$id = uniqid(NULL, TRUE);

			// Get the the id from the database
			$result = $query->execute($this->_db);
		}
		while ($result->count() > 0);

		return $this->_session_id = $id;
	}

	protected function _write()
	{
		if ($this->_update_id === NULL)
		{
			// Insert a new row
			$query = DB::query(Database::INSERT,
				"INSERT INTO {$this->_table} (session_id, last_active, contents) VALUES (:new_id, :active, :contents)");
		}
		elseif ($this->_update_id === $this->_session_id)
		{
			// Update just the activity and contents
			$query = DB::query(Database::UPDATE,
				"UPDATE {$this->_table} SET last_active = :active, contents = :contents WHERE session_id = :old_id");
		}
		else
		{
			// Update all fields
			$query = DB::query(Database::UPDATE,
				"UPDATE {$this->_table} SET session_id = :new_id, last_active = :active, contents = :contents WHERE session_id = :old_id");
		}

		$query
			->param(':new_id',   $this->_session_id)
			->param(':old_id',   $this->_update_id)
			->param(':active',   $this->_data['last_active'])
			->param(':contents', $this->__toString());

		try
		{
			// Execute the query
			$query->execute($this->_db);
		}
		catch (Exception $e)
		{
			// Ignore all errors when a write fails
			return FALSE;
		}

		// The update and the session id are now the same
		$this->_update_id = $this->_session_id;

		// Update the cookie with the new session id
		Cookie::set($this->_name, $this->_session_id, $this->_lifetime);

		return TRUE;
	}

	protected function _destroy()
	{
		if ($this->_update_id === NULL)
		{
			// Session has not been created yet
			return TRUE;
		}

		// Delete the current session
		$query = DB::query(Database::DELETE, "DELETE FROM {$this->_table} WHERE session_id = :id")
			->param(':id', $this->_update_id);

		try
		{
			// Execute the query
			$query->execute($this->_db);

			// Delete the cookie
			Cookie::delete($this->_name);
		}
		catch (Exception $e)
		{
			// An error occurred, the session has not been deleted
			return FALSE;
		}

		return TRUE;
	}

} // End Session_Database
