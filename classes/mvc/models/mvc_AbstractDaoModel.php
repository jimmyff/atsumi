<?php
// Abstract data access model
abstract class mvc_AbstractDaoModel extends mvc_AbstractModel {

    const DB_PKEY           = 'id';
    const DB_PKEY_AUTO_INC   = true;

    public $DB_STORED       = false;
    
	static public function write ($db, $o) {

        if (!($o instanceof self))
            throw new Exception (sf(
                'DAO Write: Expected object of class %s, got %s',
                get_class(),
                is_null($o)?'NULL':get_class($o)
            )
            );

        if (static::DB_PKEY_AUTO_INC)
    		$method = !is_null($o->get(static::DB_PKEY, false))?'update':'insert';
        else
            $method = $o->DB_STORED?'update':'insert';
                
		$args = array();

		if ($method == 'update') {
			$args[] = sf('%s = %s', static::DB_PKEY, $o->get(static::DB_PKEY));
		}

//		$vars = $this->getChanges();
		foreach($o->data as $key => $value) {
            if (is_null($value) && $key == self::DB_PKEY && self::DB_PKEY_AUTO_INC) continue;
			if (!$o->structure[$key]['write']) continue;
			$args[] = sf('%s = %%%s', $key, $o->structure[$key]['type']);
			$args[] = $value;
		}

		if (!count($args))
			throw new Exception ('Model has nothing to write...');

		array_unshift($args, static::DB_TABLE_NAME);

        
		$result = call_user_func_array(array($db, $method), $args);

        $o->DB_STORED = true;
        
		if (is_null($o->get(static::DB_PKEY, false)) && static::DB_PKEY_AUTO_INC) {
			$id = $db->selectOne('select lastval()');
			$o->set(static::DB_PKEY, $id->i_lastval, true);
		}

	}
	
	/*
	static public function writeInsert ($db, $o) {

		$args = array();
		foreach($o->data as $key => $value) {
			if (!$o->structure[$key]['write']) continue;
			$args[] = sf('%s = %%%s', $key, $o->structure[$key]['type']);
			$args[] = $value;
		}

		array_unshift($args, static::DB_TABLE_NAME);

		$result = call_user_func_array(array($db, 'insert'), $args);
	}
	*/

	/* generic */
	static protected function _load (
		$db,
		$where = null,
		$orderBy = null,
		$offset = null,
		$limit = null
	) {

		if (is_null($where)) $where = '';
		else $where = ' where '.$where;

		if (is_null($orderBy)) $orderBy = '';
		else $orderBy = ' order by '.$orderBy;

		if (is_null($offset)) $offset = '';
		else $offset = ' offset '.$offset;

		if (is_null($limit)) $limit = '';
		else $limit = ' limit '.$limit;

		$rows = $db->select('%l %l %l %l %l',
			static::DB_SELECT_QUERY, $where, $orderBy, $offset, $limit);

		$arrOut = array();

		if (!$rows) return $arrOut;

		foreach ($rows as $row) {
			$obj = new static();
            $obj->DB_STORED = true;
			$obj->populateFromSqlRow($row);
			$arrOut[] = $obj;
		}

		return $arrOut;

	}

	/* generic */
	function populateFromSqlRow ($r) {
		$rowData = $r->getData();

		// itterate through the db columns and populate the object
		foreach ($rowData as $k => $v) {

			// check we're expecting the currently column
			if (!array_key_exists($k, $this->structure))
				throw new Exception (sf('Unexpected row column "%s", add this to the models \'structure\' member variable.', $k));

			try {

				$this->data[$k] = caster_PostgreSqlToPhp::cast(sf('%%%s', $this->structure[$k]['type']), $v);

				// if casting error throw a useful exception
			} catch (Exception $e) {
				$spec = caster_PostgreSqlToPhp::getSpec();

				if (!array_key_exists($this->structure[$k]['type'], $spec))
					throw new caster_Exception (
						sf("Caster doesn't support format: %%%s",
							$this->structure[$k]['type']
						)
					);

				throw new caster_Exception (
					sf("%s: '%s' could not be cast as '%s' (%%%s)",
						$k,
						is_null($v)?'NULL':$v,
						$spec[$this->structure[$k]['type']],
						$this->structure[$k]['type']
					)
				);
			}
		}
	}


}
	
	

?>