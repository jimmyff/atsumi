<?php
/**
 * File defines all functionaility of the caster_PostgreSQL class.
 * @package		Atsumi.Framework
 * @copyright	Copyright(C) 2008, James A. Forrester-Fellowes. All rights reserved.
 * @license		GNU/GPL, see license.txt
 * The Atsumi Framework is open-source software. This version may have been modified pursuant to
 * the GNU General Public License, and as distributed it includes or is derivative of works
 * licensed under the GNU General Public License or other free or open source software licenses.
 * See copyright.txt for copyright notices and details.
 */

/**
 * Caster class instance specific for PostgreSQL databases.
 * @package		Atsumi.Framework
 * @subpackage	Caster
 * @since		1.0
 */
class caster_PostgreSql extends caster_Abstract {
	/* CONSTANTS */
	/* PROPERTIES */

	/**
	 * Character to function casting spec
	 * @var array
	 */
	protected $spec = array(
		'@' => 'tableName',
		'a' => 'sqlArray',
		'A' => 'sqlArrayOrNull',
		'b' => 'boolean',
		'c' => 'character',
		'C' => 'characterVarying',
		'd' => 'date',
		'e'	=> 'bigInteger',
		'E'	=> 'bigIntegerOrNull',
		'f' => 'float',
		'g' => 'geometry',
		'i' => 'integer',
		'I' => 'integerOrNull',
		'l' => 'literal',
		'j'	=> 'json',
		'J'	=> 'jsonOrNull',
		'n' => 'numeric',
		'N' => 'numericOrNull',
		'q' => 'fullTextQuery',
		's' => 'text',
		'S' => 'textOrNull',
		't' => 'timestampWithTimezone',
		'v' => 'fullTextVector',
		'x' => 'binary',
		'z' => 'interval',
		'Z' => 'intervalOrNull'
	);

	/* CONSTRUCTOR & DESTRUCTOR */
	/* GET METHODS */
	/* SET METHODS */
	/* MAGIC METHODS */
	/* METHODS */

	/**
	 * Casts a string in a PostgreSQL format
	 * NOTE: Annoyance due to PHP scope issue
	 * @param string $string The string to cast
	 * @param mixed $args The args to be parsed into the string
	 * @param mixed $_ Repeated last arg as needed
	 * @return string The casted string
	 */
	static function cast($string, $args = null, $_ = null) {
		$parser =  new self();

		/* 'func_get_args' cannot be called as function arg pre PHP5 */
		$func_args = func_get_args();
		return (string)$parser->castString($func_args);
	}

	/**
	 * Casts a variable into a PostgreSQL table name
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function tableName($in) {
		return sf('"%s"', $in);
	}

	/**
	 * Casts a variable into a PostgreSQL text
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function text($in) {
		if(!is_string($in)) throw new caster_StrictTypeException('Expected String');
		return sf("'%s'::TEXT", pg_escape_string($in));
	}

	/**
	 * Casts a variable into a PostgreSQL text thats accepts NULL values
	 * @param string $in String to be casted or null
	 * @return string Casted string
	 */
	static function textOrNull($in) {
		if (!is_string($in) && !is_null($in)) throw new caster_StrictTypeException('Expected String or Null');

		if (is_string($in) && strlen($in)) return self::text($in);
		elseif (is_null($in)) return 'NULL';
	}

	/**
	 * Casts a variable into a PostgreSQL array
	 * @param array $in Array to be casted
	 * @return string Casted string
	 */
	static function sqlArray($in) {
		$sqlArr = "";
		foreach($in as $item) {
			$sqlArr .= ($sqlArr == "") ? "" : ", ";
			$sqlArr .= is_int($item) ? $item : "'".pg_escape_string($item)."'";
		}

		return "ARRAY[".$sqlArr."]";
	}
	static function sqlArrayOrNull($in) {
		if (is_null($in)) return 'NULL';
		return self::sqlArray($in);
	}

	/**
	 * Casts a variable into a PostgreSQL character
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function character($in) {
		return sf("'%s'::CHARACTER", pg_escape_string($in));
	}

	/**
	 * Casts a variable into a PostgreSQL character varying
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function characterVarying($in) {
		return sf("'%s'::CHARACTER VARYING", pg_escape_string($in));
	}

	/**
	 * Casts a variable into a PostgreSQL float
	 * @param float $in Float to be casted
	 * @return string Casted string
	 */
	static function float($in) {
		return sf("'%s'::DOUBLE PRECISION", $in);
	}

	/**
	 * Casts a variable into a PostgreSQL boolean
	 * @param bool $in Bool to be casted
	 * @return string Casted string
	 */
	static function boolean($in) {
		return sf("'%s'::BOOLEAN", $in?'t':'f');
	}

	/**
	 * Casts a variable into a PostgreSQL geometry
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function geometry($in) {
		if(!is_string($in)) throw new caster_StrictTypeException('Geom Expected String');
		return sf("'%s'::GEOMETRY", pg_escape_string($in));
	}

	/**
	 * Casts a variable into a PostgreSQL integer
	 * @param int $in Int to be casted
	 * @return string Casted string
	 */
	static function integer($in) {
		return sf("%s::INTEGER", intval($in));
	}
	/**
	 * Casts a variable into a PostgreSQL integer or Null
	 * @param int $in Int to be casted
	 * @return string Casted string
	 */
	static function integerOrNull($in) {
		if (is_null($in)) return 'NULL';
		return sf("%s::INTEGER", intval($in));
	}

	/**
	 * Casts a variable into a PostgreSQL bigint
	 * @param int $in Int to be casted
	 * @return string Casted string
	 */
	static function bigInteger($in) {
		return sf("%s::BIGINT", intval($in));
	}
	/**
	 * Casts a variable into a PostgreSQL big int or Null
	 * @param int $in Int to be casted
	 * @return string Casted string
	 */
	static function bigIntegerOrNull($in) {
		if (is_null($in)) return 'NULL';
		return sf("%s::BIGINT", intval($in));
	}

	/**
	 * Casts a variable into a PostgreSQL numeric
	 * @param mixed $in Mixed to be casted
	 * @return string Casted string
	 */
	static function numeric($in) {
		return sf("%s::NUMERIC", $in);
	}
	/**
	 * Casts a variable into a PostgreSQL numeric or Null
	 * @param int $in Mixed to be casted
	 * @return string Casted string
	 */
	static function numericOrNull($in) {
		if (is_null($in)) return 'NULL';
		return sf("%s::NUMERIC", $in);
	}

	/**
	 * Casts a variable into a PostgreSQL json
	 * @param mixed $in Mixed to be casted
	 * @return string Casted string
	 */
	static function json($in) {
		return sf("%s::JSON", json_encode($in));
	}

	/**
	 * Casts a variable into a PostgreSQL json or Null
	 * @param int $in Mixed to be casted
	 * @return string Casted string
	 */
	static function jsonOrNull($in) {
		if (is_null($in)) return 'NULL';
		return self::json($in);
	}

	/**
	 * Casts a variable into a PostgreSQL binary
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function binary($in) {
		return sf("'%s'::BYTEA", pg_escape_bytea($in));
	}

	/**
	 * Casts a variable into a PostgreSQL timestamp with timezone
	 * @param int $in Int to be casted
	 * @return string Casted string
	 */
	static function timestampWithTimezone($in) {
		return sf("'%s'::TIMESTAMP WITH TIME ZONE", pg_escape_string(gmdate('Y-m-d H:i:s+00', $in)));
	}

	/**
	 * Casts a variable into a PostgreSQL date
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function date($in) {
		return sf("'%s'::DATE", $in);
	}

	/**
	 * Casts a variable into a PostgreSQL date
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function interval($in) {
		return sf("'%s'::INTERVAL", $in);
	}
	static function intervalOrNull($in) {
		if (is_null($in)) return 'NULL';
		return sf("'%s'::INTERVAL", $in);
	}

	/**
	 * Performs no casting on the variable, leaving it as it is
	 * @param mixed $in mixed to be casted
	 * @return string Unaffected string
	 */
	static function literal($in) {
		return $in;
	}

	/**
	 * Casts a variable into a PostgreSQL full text query
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function fullTextQuery($in) {
		return sf("'%s'::TSQUERY", $in);
	}

	/**
	 * Casts a variable into a PostgreSQL full text vector
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function fullTextVector($in) {
		return sf("'%s'::TSVECTOR", $in);
	}

	/* DEPRECATED METHODS */
}
?>