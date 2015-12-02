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
class caster_PostgreSqlToPhp extends caster_Abstract {
	/* CONSTANTS */
	/* PROPERTIES */

	/**
	 * Character to function casting spec
	 * @var array
	 */
	protected $spec = array(
		'A' => 'sqlArrayOrNull',
		'a' => 'sqlArray',
		'b' => 'boolean',
		'B' => 'booleanOrNull',
		'd' => 'date',
		'D' => 'dateOrNull',
		'e' => 'integer',
		'E' => 'integerOrNull',
		'g' => 'geometry',
		'G' => 'geometryOrNull',
		'i' => 'integer',
		'I' => 'integerOrNull',
		'j' => 'json',
		'J' => 'jsonOrNull',
		'n' => 'numeric',
		'N' => 'numericOrNull',
		'f' => 'float',
		'F' => 'floatOrNull',
		's' => 'text',
		'S' => 'textOrNull',
		't' => 'timestampWithTimezone',
		'T' => 'timestampWithTimezoneOrNull',
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
		return $parser->castObject($func_args);
	}


	/**
	 * Casts a variable into a PostgreSQL text
	 * @param string $in String to be casted
	 * @return string Casted string
	 */
	static function text($in) {
		if(!is_string($in)) throw new caster_StrictTypeException('Expected String, received: '.$in.' ('.gettype($in).')');
		return sf("%s", $in);
	}

	/**
	 * Casts a variable into a PostgreSQL text thats accepts NULL values
	 * @param string $in String to be casted or null
	 * @return string Casted string
	 */
	static function textOrNull($in) {
		if (!is_string($in) && !is_null($in)) throw new caster_StrictTypeException('Expected String or Null, received: '.$in.' ('.gettype($in).')');

		if (is_string($in) && strlen($in)) return self::text($in);
		elseif (is_null($in)) return null;
	}

	/**
	 * Casts a variable into a PostgreSQL array
	 * @param array $in Array to be casted
	 * @return string Casted string
	 */
	static function sqlArray($in) {

		if (!is_string($in)) throw new caster_StrictTypeException('Expected String, received: '.$in.' ('.gettype($in).')');
		if (is_null($in)) return array();

		$in = str_replace(array("{","}"),"", $in);

		$arrayOut = array();
		$processing= $in;

		while(strlen($processing) > 0) {

			if (substr($processing, 0, 1) == ',') {
				$processing = substr($processing, 1);
			}

			preg_match('/^\"([^"]*)\"/', $processing, $match);
			if (count($match)) {

				if (strlen($processing) === strlen($match[0]))
						$processing = '';
				else	$processing = substr($processing, strlen($match[0]));

				$arrayOut[] = $match[1];
				continue;
			}

			preg_match('/^([^,]*)/', $processing, $match);
			if (count($match)) {

				if (strlen($processing) === strlen($match[0]))
						$processing = '';
				else	$processing = substr($processing, strlen($match[0]));

				$arrayOut[] = $match[1];
				continue;
			}
		}
		// int
	/*	if ($type == "integer") {
			$newArr = array();
			foreach ($arr as $val)
			$newArr[] = intval($val);
			$arr = $newArr;
		} */
		return $arrayOut;
	}

	static function sqlArrayOrNull($in) {
		if (!is_string($in) && !is_null($in)) throw new caster_StrictTypeException('Expected String or Null, received: '.$in.' ('.gettype($in).')');

		if (is_string($in)) return self::sqlArray($in);
		elseif (is_null($in)) return null;
	}

	/**
	 * Casts a variable into a PostgreSQL boolean
	 * @param bool $in Bool to be casted
	 * @return string Casted string
	 */
	static function boolean($in) {

		if (is_string($in) && $in == 'f') $in = false;
		if (is_string($in) && $in == 't') $in = true;
		if (!is_bool($in)) throw new caster_StrictTypeException('Expected Boolean, received: '.$in.' ('.gettype($in).')');
		return $in;
	}
	static function booleanOrNull($in) {
		if (is_null($in)) return null;
		return self::boolean($in);
	}

	static function interval($in) {
		return atsumi_Interval::fromPostgresql(strval($in));
	}
	static function intervalOrNull($in) {
		if (is_null($in)) return null;
		return self::interval($in);
	}

	/**
	 * Casts a variable into a PostgreSQL integer
	 * @param int $in Int to be casted
	 * @return string Casted string
	 */
	static function integer($in) {
		if (is_string($in)) $in = intval($in);
		if (!is_int($in)) throw new caster_StrictTypeException('Expected Integer, received: '.$in.' ('.gettype($in).')');
		return intval($in);
	}
	static function integerOrNull($in) {
		if (is_null($in)) return null;
		if (!is_int(intval($in))) throw new caster_StrictTypeException('Expected Integer or Null, received: '.$in.' ('.gettype($in).')');
		return intval($in);
	}

	static function numeric($in) {
		if (!is_numeric($in)) throw new caster_StrictTypeException('Expected Numeric, received: '.$in.' ('.gettype($in).')');
		return $in;
	}
	static function numericOrNull($in) {
		if (is_null($in)) return null;
		if (!is_numeric($in)) throw new caster_StrictTypeException('Expected Numeric, received: '.$in.' ('.gettype($in).')');
		return $in;
	}

	static function json($in) {
		return json_decode($in);
	}

	static function jsonOrNull($in) {
		if (is_null($in)) return null;
		else return self::json($in);
	}
	

	static function float($in) {
		if (!is_numeric($in)) throw new caster_StrictTypeException('Expected Float, received: '.$in.' ('.gettype($in).')');
		setType($in, 'float');
		return $in;
	}
	static function floatOrNull($in) {
		if (is_null($in)) return null;
		if (!is_numeric($in)) throw new caster_StrictTypeException('Expected Float or Null, received: '.$in.' ('.gettype($in).')');
		setType($in, 'float');
		return $in;
	}

	static function date($in) {
		return atsumi_Date::fromYmd($in);
	}
	static function dateOrNull($in) {
		if (is_null($in)) return null;
		return atsumi_Date::fromYmd($in);
	}

	static function timestampWithTimezone($in) {
		return new atsumi_DateTime(strtotime($in));
	}
	static function timestampWithTimezoneOrNull($in) {
		if (is_null($in)) return null;
		return self::timestampWithTimezone($in);
	}

	static function geometry($in) {
		return self::text($in);
	}
	static function geometryOrNull($in) {
		return self::textOrNull($in);
	}

	/* DEPRECATED METHODS */
}
?>