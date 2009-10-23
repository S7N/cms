<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Sprig database modeling system.
 *
 * @package    Sprig
 * @author     Woody Gilk
 * @copyright  (c) 2009 Woody Gilk
 * @license    MIT
 */
abstract class Sprig {

	// Model many-to-many relations
	protected static $_relations;

	/**
	 * Load an empty sprig model.
	 *
	 * @param   string  model name
	 * @param   array   values to pre-populate the model
	 * @return  Sprig
	 */
	public static function factory($name, array $values = NULL)
	{
		static $models;

		if ( ! isset($models[$name]))
		{
			$class = 'Model_'.$name;

			$models[$name] = new $class;
		}

		// Create a new instance of the model by clone
		$model = clone $models[$name];

		if ($values)
		{
			$model->values($values);
		}

		return $model;
	}

	/**
	 * @var  string  model name
	 */
	protected $_model;

	/**
	 * @var  string  database instance name
	 */
	protected $_db = 'default';

	/**
	 * @var  string  database table name
	 */
	protected $_table;

	/**
	 * @var  array  field list (name => object)
	 */
	protected $_fields = array();

	/**
	 * @var  mixed  primary key string or array (for composite keys)
	 */
	protected $_primary_key;

	/**
	 * @var  string  title key string (for select lists)
	 */
	protected $_title_key = 'name';

	/**
	 * @var  array  default sorting parameters
	 */
	protected $_sorting;

	// Initialization status
	protected $_init = FALSE;

	// Object loaded status
	protected $_loaded = FALSE;

	// Original object data
	protected $_original = array();

	/**
	 * Calls the init() method. Sprig constructors are only called once!
	 *
	 * @param   string   model name
	 * @return  void
	 */
	final protected function __construct()
	{
		$this->init();
	}

	/**
	 * Returns the model name.
	 *
	 * @return  string
	 */
	final public function __toString()
	{
		return $this->_model;
	}

	/**
	 * Clones each of the fields and empty the model.
	 *
	 * @return  void
	 */
	public function __clone()
	{
		// Object is being unloaded
		$this->_loaded = FALSE;

		foreach ($this->_fields as $name => $field)
		{
			$this->_fields[$name] = clone $field;
		}

		// Reset original data
		$this->_original = $this->as_array();
	}

	/**
	 * Get the value of a field.
	 *
	 * @throws  Sprig_Exception  field does not exist
	 * @param   string  field name
	 * @return  mixed
	 */
	public function __get($name)
	{
		if ( ! $this->_init)
		{
			$this->init();

			// Loaded status must be changed, this object is being created by
			// mysql_fetch_object() or similar
			$this->_loaded = TRUE;
		}

		if ( ! isset($this->_fields[$name]))
		{
			throw new Sprig_Exception(':name model does not have a field :field',
				array(':name' => get_class($this), ':field' => $name));
		}

		$field = $this->_fields[$name];

		if ( ! $this->loaded() AND ! $this->changed($name))
		{
			// Lazy loading, this field does not have a value yet
			$this->load();
		}

		if ($field instanceof Sprig_Field_HasMany)
		{
			$model = Sprig::factory($field->model);

			if ($ids = $field->get())
			{
				$query = DB::select()
					->where($model->pk(), 'IN', $ids);

				return $model->load($query, FALSE);
			}
			else
			{
				return new Database_Result_Cached(array(), '', get_class($model));
			}
		}

		return $field->get();
	}

	/**
	 * Set the value of a field.
	 *
	 * @throws  Sprig_Exception  field does not exist
	 * @param   string  field name
	 * @param   mixed   new field value
	 * @return  mixed
	 */
	public function __set($name, $value)
	{
		if ( ! $this->_init)
		{
			$this->init();

			// Loaded status must be changed, this object is being created by
			// mysql_fetch_object() or similar
			$this->_loaded = TRUE;
		}

		if ( ! isset($this->_fields[$name]))
		{
			throw new Sprig_Exception(':name model does not have a field :field',
				array(':name' => get_class($this), ':field' => $name));
		}

		// Get the field object
		$field = $this->_fields[$name];

		if ($field instanceof Sprig_Field_HasOne)
		{
			if ( ! is_object($value))
			{
				// Load the a new model for this field
				$model = Sprig::factory($field->model);

				// Load the new object by pk
				$value = $model->values(array($model->pk() => $value))->load();
			}
		}
		elseif ($field instanceof Sprig_Field_Password)
		{
			if ($value)
			{
				// Hash the value immediately for security
				$value = call_user_func($field->hash_with, $value);
			}
		}
		elseif ($field instanceof Sprig_Field_HasMany)
		{
			if (is_object($value))
			{
				$model = Sprig::factory($field->model);
				$value = $value->as_array($model->pk(), $model->pk());
			}
			elseif (is_array($value))
			{
				$value = array_combine($value, $value);
			}
		}

		// Set the new field value
		$field->set($value);
	}

	/**
	 * Initialize the fields and add validation rules based on field properties.
	 *
	 * @return  void
	 */
	public function init()
	{
		if ($this->_init)
		{
			// Can only be called once
			return;
		}

		// Set up the fields
		$this->_init();

		if ( ! $this->_model)
		{
			// Set the model name based on the class name
			$this->_model = strtolower(substr(get_class($this), 6));
		}

		if ( ! $this->_table)
		{
			// Set the table name to the plural model name
			$this->_table = inflector::plural($this->_model);
		}

		foreach ($this->_fields as $name => $field)
		{
			if ($field->primary === TRUE)
			{
				if ( ! $this->_primary_key)
				{
					// This is the primary key
					$this->_primary_key = $name;
				}
				else
				{
					if (is_string($this->_primary_key))
					{
						// More than one primary key found, create a list of keys
						$this->_primary_key = array($this->_primary_key);
					}

					// Add this key to the list
					$this->_primary_key[] = $name;
				}
			}
		}

		foreach ($this->_fields as $name => $field)
		{
			if ( ! $field->column)
			{
				// Create the key based on the field name

				if ($field instanceof Sprig_Field_ForeignKey)
				{
					if ($field instanceof Sprig_Field_HasOne)
					{
						$field->column = $name.'_id';
					}
					else
					{
						$field->column = $this->_model;
					}
				}
				else
				{
					$field->column = $name;
				}
			}

			if ($field instanceof Sprig_Field_ManyToMany)
			{
				if ( ! $field->through)
				{
					// Get the model names for the relation pair
					$pair = array(strtolower($this->_model), strtolower($field->model));

					// Sort the model names alphabetically
					sort($pair);

					// Join the model names to get the relation name
					$pair = implode('_', $pair);

					if ( ! isset(Sprig::$_relations[$pair]))
					{
						// Must set the pair key before loading the related model
						// or we will fall into an infinite recursion loop
						Sprig::$_relations[$pair] = TRUE;

						$tables = array($this->table(), Sprig::factory($field->model)->table());

						// Sort the table names alphabetically
						sort($tables);

						// Join the table names to get the table name
						Sprig::$_relations[$pair] = implode('_', $tables);
					}

					// Assign by reference so that changes to the relationship
					// will carry over to all models
					$field->through =& Sprig::$_relations[$pair];
				}
			}

			if ( ! $field->label)
			{
				$field->label = Inflector::humanize($name);
			}

			if ($field->editable)
			{
				if ( ! $field->empty AND ! isset($field->rules['not_empty']))
				{
					// This field must not be empty
					$field->rules['not_empty'] = NULL;
				}

				if ($field->unique)
				{
					// Field must be a unique value
					$field->callbacks[] = array($this, '_unique_field');
				}

				if ($field->choices AND ! isset($field->rules['in_array']))
				{
					// Field must be one of the available choices
					$field->rules['in_array'] = array(array_keys($field->choices));
				}

				if ( ! empty($field->min_length))
				{
					$field->rules['min_length'] = array($field->min_length);
				}

				if ( ! empty($field->max_length))
				{
					$field->rules['max_length'] = array($field->max_length);
				}
			}
		}

		$this->_init = TRUE;
	}

	/**
	 * Returns the primary key of the model, optionally with a table name.
	 *
	 * @param   string  table name, TRUE for the model table
	 * @return  string
	 */
	public function pk($table = FALSE)
	{
		if ($table)
		{
			if ($table === TRUE)
			{
				$table = $this->_table;
			}

			return $table.'.'.$this->_primary_key;
		}

		return $this->_primary_key;
	}

	/**
	 * Returns the foreign key of the model, optionally with a table name.
	 *
	 * @param   string  table name, TRUE for the model table
	 * @return  string
	 */
	public function fk($table = FALSE)
	{
		$key = $this->_model.'_'.$this->_primary_key;

		if ($table)
		{
			if ($table === TRUE)
			{
				$table = $this->_table;
			}

			return $table.'.'.$key;
		}

		return $key;
	}

	/**
	 * Returns the title key of the model, optionally with a table name.
	 *
	 * @param   string  table name, TRUE for the model table
	 * @return  string
	 */
	public function tk($table = FALSE)
	{
		if ($table)
		{
			if ($table === TRUE)
			{
				$table = $this->_table;
			}

			return $table.'.'.$this->_title_key;
		}

		return $this->_title_key;
	}

	/**
	 * Returns the database instance used for this model.
	 *
	 * @return  string
	 */
	public function db()
	{
		return $this->_db;
	}

	/**
	 * Returns the table name of the model.
	 *
	 * @return  string
	 */
	public function table()
	{
		return $this->_table;
	}

	/**
	 * Load all of the values in an associative array. Ignores all fields are
	 * not in the model.
	 *
	 * @param   array    field => value pairs
	 * @return  $this
	 */
	public function values(array $values)
	{
		// Remove all values which do not have a corresponding field
		$values = array_intersect_key($values, $this->_fields);

		foreach ($values as $field => $value)
		{
			// Set the field using __set()
			$this->$field = $value;
		}

		return $this;
	}

	/**
	 * Get the model data as an associative array.
	 *
	 * @return  array  field => value
	 */
	public function as_array($verbose = FALSE)
	{
		$data = array();
		foreach ($this->_fields as $name => $field)
		{
			if ($verbose === TRUE)
			{
				// Get the verbose value
				$value = $field->verbose();
			}
			elseif ($field instanceof Sprig_Field_ForeignKey)
			{
				// Use the non-object value for all foreign keys
				$value = $field->raw();
			}
			else
			{
				// Get the normal value
				$value = $field->get();
			}

			$data[$name] = $value;
		}

		return $data;
	}

	/**
	 * Get all of the records for this table as an associative array.
	 *
	 * @param   string  array key, defaults to the primary key
	 * @param   string  array value, defaults to the title key
	 * @return  array   key => value
	 */
	public function select_list($key = NULL, $value = NULL)
	{
		if ( ! $key)
		{
			$key = $this->pk();
		}

		if ( ! $value)
		{
			$value = $this->tk();
		}

		$query = DB::select($key, $value)
			->from($this->_table);

		if ($this->_sorting)
		{
			foreach ($this->_sorting as $field => $direction)
			{
				$query->order_by($field, $direction);
			}
		}

		return $query
			->execute($this->_db)
			->as_array($key, $value);
	}

	/**
	 * Return the related object of a ForeignKey field.
	 *
	 * @param   string   field name
	 * @return  object   Sprig instance for HasOne fields, Database_Result iterator for HasMany
	 */
	public function related($name)
	{
		$field = $this->_fields[$name];

		if ( ! $field instanceof Sprig_Field_HasMany)
		{
			throw new Sprig_Exception(':name model does not have a relation :field',
				array(':name' => get_class($this), ':field' => $name));
		}

		if ( ! $field->get())
		{
			// Load the related model
			$model = Sprig::factory($field->model);

			if ($field instanceof Sprig_Field_ManyToMany)
			{
				$query = DB::select(array($model->fk(), $model->pk()))
					->from($field->through);
			}
			else
			{
				$query = DB::select($model->pk())
					->from($model->table());
			}

			$result = $query
				->where($this->fk(), '=', $this->{$this->_primary_key})
				->execute($this->_db)
				->as_array($model->pk(), $model->pk());

			// Change the field value to the result value
			$field->set($result);
		}

		return $field->get();
	}

	/**
	 * Object data loaded status.
	 *
	 * @return  boolean
	 */
	public function loaded()
	{
		return $this->_loaded;
	}

	/**
	 * Get all of the changed fields as an associative array.
	 *
	 * @return  array  field => value
	 */
	public function changed($field = NULL)
	{
		if ($field === NULL)
		{
			$changed = $this->as_array();

			foreach ($changed as $field => $value)
			{
				if ($value === $this->_original[$field])
				{
					// Original and current value are the same
					unset($changed[$field]);
				}
			}

			return $changed;
		}
		else
		{
			return $this->_fields[$field]->raw() !== $this->_original[$field];
		}
	}

	/**
	 * Get a single field object.
	 *
	 * @return  Sprig_Field
	 */
	public function field($name)
	{
		return $this->_fields[$name];
	}

	/**
	 * Get all fields as an associative array.
	 *
	 * @return  array  name => object
	 */
	public function fields()
	{
		return $this->_fields;
	}

	/**
	 * Return a single field input.
	 *
	 * @param   string  field name
	 * @param   array   input attributes
	 * @return  string
	 */
	public function input($field, array $attr = NULL)
	{
		return $this->_fields[$field]->input($field, $attr);
	}

	/**
	 * Get all fields as an array of inputs.
	 *
	 * @param   boolean  use the input label as the array key
	 * @return  array    label => input
	 */
	public function inputs($labels = TRUE)
	{
		$inputs = array();

		foreach ($this->_fields as $name => $field)
		{
			if ($field->editable)
			{
				if ($labels === TRUE)
				{
					$key = form::label($name, $field->label);
				}
				else
				{
					$key = $name;
				}

				$inputs[$key] = $field->input($name);
			}
		}

		return $inputs;
	}

	/**
	 * Return a single field label.
	 *
	 * @param   string  field name
	 * @return  string
	 */
	public function label($field)
	{
		return $this->_fields[$field]->label;
	}

	/**
	 * Return a single field value in verbose form.
	 *
	 * @param   string  field name
	 * @return  string
	 */
	public function verbose($field)
	{
		return $this->_fields[$field]->verbose();
	}

	/**
	 * Load a single record using the current data.
	 *
	 * @return  $this
	 */
	public function load(Database_Query $query = NULL, $limit = 1)
	{
		// Load changed values as search parameters
		$changed = $this->changed();

		if ($query === NULL AND $limit === 1 AND empty($changed))
		{
			// No query needs to be executed
			return $this;
		}

		if ($query === NULL)
		{
			$query = DB::select();
		}

		$query->from($this->_table);

		foreach ($this->_fields as $name => $field)
		{
			if ($field instanceof Sprig_Field_HasMany OR ! $field->in_db)
			{
				// Multiple relations cannot be loaded this way
				continue;
			}

			if ($name === $field->column)
			{
				$query->select($name);
			}
			else
			{
				$query->select(array($field->column, $name));
			}

			if (array_key_exists($name, $changed))
			{
				$query->where($field->column, '=', $field->raw());
			}
		}

		if ($limit)
		{
			$query->limit($limit);
		}

		if ($limit === 1)
		{
			$result = $query
				->execute($this->_db);

			if (count($result))
			{
				// Load the result
				$this->values($result->current(), TRUE);

				// Object data has been loaded
				$this->_loaded = TRUE;

				foreach ($this->_fields as $name => $field)
				{
					if ($field instanceof Sprig_Field_HasMany)
					{
						// Load all relations
						$this->related($name);
					}
				}

				// Load the original data for this record
				$this->_original = $this->as_array();
			}

			return $this;
		}
		else
		{
			if ($this->_sorting)
			{
				foreach ($this->_sorting as $field => $direction)
				{
					$query->order_by($field, $direction);
				}
			}

			return $query
				->as_object(get_class($this))
				->execute($this->_db);
		}
	}


	/**
	 * Create a new record using the current data.
	 *
	 * @uses    Sprig::check()
	 * @return  $this
	 */
	public function create()
	{
		foreach ($this->_fields as $name => $field)
		{
			if ($field instanceof Sprig_Field_Timestamp AND $field->auto_now_create)
			{
				// Set the value to the current timestamp
				$this->$name = time();
			}
		}

		// Check the all current data
		$data = $this->check($this->as_array());

		$values = $relations = array();
		foreach ($data as $field => $value)
		{
			$field = $this->_fields[$field];

			if ($field instanceof Sprig_Field_Auto OR ! $field->in_db )
			{
				// Skip all auto-increment fields or where in_db is false
				continue;
			}

			if ($field instanceof Sprig_Field_HasMany)
			{
				$relations[$name] = $value;
			}
			else
			{
				// Change the field name to the column name
				$values[$field->column] = $value;
			}
		}

		list($id) = DB::insert($this->_table, array_keys($values))
			->values($values)
			->execute($this->_db);

		if (is_array($this->_primary_key))
		{
			foreach ($this->_primary_key as $field)
			{
				if ($this->_fields[$field] instanceof Sprig_Field_Auto)
				{
					// Set the auto-increment primary key to the insert id
					$this->_fields[$field]->set($id);

					// There can only be 1 auto-increment column per model
					break;
				}
			}
		}
		else
		{
			if ($this->_fields[$this->_primary_key] instanceof Sprig_Field_Auto)
			{
				$this->_fields[$this->_primary_key]->set($id);
			}
		}

		if ($relations)
		{
			foreach ($relations as $name => $value)
			{
				$field = $this->_fields[$name];

				$model = Sprig::factory($field->model);

				foreach ($value as $id)
				{
					DB::insert($field->through, array($this->fk(), $model->fk()))
						->values(array($this->{$this->_primary_key}, $id))
						->execute($this->_db);
				}
			}
		}

		// Load the original data for this record
		$this->_original = $this->as_array();

		// Object is now loaded
		$this->_loaded = TRUE;

		return $this;
	}

	/**
	 * Update the current record using the current data.
	 *
	 * @uses    Sprig::check()
	 * @return  $this
	 */
	public function update()
	{
		if ($this->changed())
		{
			foreach ($this->_fields as $name => $field)
			{
				if ($field instanceof Sprig_Field_Timestamp AND $field->auto_now_update)
				{
					// Set the value to the current timestamp
					$this->$name = time();
				}
			}

			// Check the updated data
			$data = $this->check($this->changed());

			$values = $relations = array();
			foreach ($data as $name => $value)
			{
				$field = $this->_fields[$name];

				if ( ! $field->in_db)
				{
					// Skip all fields that are not in the database
					continue;
				}

				if ($field instanceof Sprig_Field_HasMany)
				{
					$relations[$name] = $value;
				}
				else
				{
					// Change the field name to the column name
					$values[$field->column] = $value;
				}
			}

			if ($values)
			{
				$query = DB::update($this->_table)
					->set($values);

				if (is_array($this->_primary_key))
				{
					foreach($this->_primary_key as $field)
					{
						$query->where($this->_fields[$field]->column, '=', $this->_original[$field]);
					}
				}
				else
				{
					$query->where($this->_fields[$this->_primary_key]->column, '=', $this->_original[$this->_primary_key]);
				}

				$query->execute($this->_db);
			}

			if ($relations)
			{
				foreach ($relations as $name => $value)
				{
					$field = $this->_fields[$name];

					$model = Sprig::factory($field->model);

					// Find old relationships that must be deleted
					if ($old = array_diff($this->_original[$name], $value))
					{
						DB::delete($field->through)
							->where($this->fk(), '=', $this->{$this->_primary_key})
							->where($model->fk(), 'IN', $old)
							->execute($this->_db);
					}

					// Find new relationships that must be inserted
					if ($new = array_diff($value, $this->_original[$name]))
					{
						foreach ($new as $id)
						{
							DB::insert($field->through, array($this->fk(), $model->fk()))
								->values(array($this->{$this->_primary_key}, $id))
								->execute($this->_db);
						}
					}
				}
			}

			// Reset the original data for this record
			$this->_original = $this->as_array();
		}

		return $this;
	}

	/**
	 * Delete the current record:
	 *
	 * - If the record is loaded, it will be deleted using primary key(s).
	 * - If the record is not loaded, it will be deleted using all changed fields.
	 * - If no data has been changed, the delete will be ignored.
	 *
	 * @return  $this
	 */
	public function delete()
	{
		if ($this->loaded())
		{
			$query = DB::delete($this->_table);

			if (is_array($this->_primary_key))
			{
				foreach($this->_primary_key as $field)
				{
					$query->where($this->_fields[$field]->column, '=', $this->_original[$field]);
				}
			}
			else
			{
				$query->where($this->_fields[$this->_primary_key]->column, '=', $this->_original[$this->_primary_key]);
			}
		}
		elseif ($changed = $this->changed())
		{
			$query = DB::delete($this->_table);

			foreach ($changed as $field => $value)
			{
				$query->where($this->_fields[$field]->column, '=', $value);
			}
		}

		if (isset($query) AND $query->execute($this->_db))
		{
			return clone $this;
		}

		return $this;
	}

	/**
	 * Check the given data is valid. Only values that have editable fields
	 * will be included and checked.
	 *
	 * @throws  Validate_Exception  when an error is found
	 * @param   array  data to check, field => value
	 * @return  array  filtered data
	 */
	public function check(array $data = NULL)
	{
		if ($data === NULL)
		{
			// Use the current data set
			$data = $this->changed();
		}

		$data = Validate::factory($data);

		foreach ($this->_fields as $name => $field)
		{
			if ( ! $data->offsetExists($name))
			{
				// Do not add any rules for this field
				continue;
			}

			$data->label($name, $field->label);

			if ($field->filters)
			{
				$data->filters($name, $field->filters);
			}

			if ($field->rules)
			{
				$data->rules($name, $field->rules);
			}

			if ($field->callbacks)
			{
				$data->callbacks($name, $field->callbacks);
			}
		}

		if ( ! $data->check())
		{
			throw new Validate_Exception($data);
		}

		return $data->as_array();
	}

	/**
	 * Callback for validating unique fields.
	 *
	 * @param   object  Validate array
	 * @param   string  field name
	 * @return  void
	 */
	public function _unique_field(Validate $array, $field)
	{
		if ($array[$field])
		{
			$query = DB::select($this->_fields[$this->_primary_key]->column)
				->from($this->_table)
				->where($this->_fields[$field]->column, '=', $array[$field])
				->execute($this->_db);

			if (count($query))
			{
				$array->error($field, 'unique');
			}
		}
	}

	/**
	 * Initialize the fields. This method will only be called once
	 * by Sprig::init(). All models must define this method!
	 *
	 * @return  void
	 */
	abstract protected function _init();

} // End Sprig