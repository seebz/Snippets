<?php


class Inflector {
	
	static public $plural_rules   = array();
	static public $singular_rules = array();
	
	
	// Copyright (c) 2005 Flinn Mueller (MIT License)
	static protected $_plural_rules   = array(
		'/^(ox)$/i'                => '\1\2en',     // ox
		'/([m|l])ouse$/i'          => '\1ice',      // mouse, louse
		'/(matr|vert|ind)ix|ex$/i' => '\1ices',     // matrix, vertex, index
		'/(x|ch|ss|sh)$/i'         => '\1es',       // search, switch, fix, box, process, address
		//'/([^aeiouy]|qu)ies$/'     => '\1y', -- seems to be a bug(?)
		'/([^aeiouy]|qu)y$/i'      => '\1ies',      // query, ability, agency
		'/(hive)$/i'               => '\1s',        // archive, hive
		'/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',    // half, safe, wife
		'/sis$/i'                  => 'ses',        // basis, diagnosis
		'/([ti])um$/i'             => '\1a',        // datum, medium
		'/(p)erson$/i'             => '\1eople',    // person, salesperson
		'/(m)an$/i'                => '\1en',       // man, woman, spokesman
		'/(c)hild$/i'              => '\1hildren',  // child
		'/(buffal|tomat)o$/i'      => '\1\2oes',    // buffalo, tomato
		'/(bu)s$/i'                => '\1\2ses',    // bus
		'/(alias|status)/i'        => '\1es',       // alias
		'`(octop|vir)us$`i'	       => '\1i',		// octopus, virus - virus has no defined plural (according to Latin/dictionary.com), but viri is better than viruses/viruss
		'/(ax|cri|test)is$/i'      => '\1es',       // axis, crisis
		'/s$/i'                    => 's',          // no change (compatibility)
		'/$/i'                     => 's',
	);
	// Copyright (c) 2005 Flinn Mueller (MIT License)
	static protected $_singular_rules = array(
		'/(matr)ices$/i'           => '\1ix',
		'/(vert|ind)ices$/i'       => '\1ex',
		'/^(ox)en/i'               => '\1',
		'/(alias)es$/i'            => '\1',
		'`([octop|vir])i$`i'       => '\1us',
		'/(cris|ax|test)es$/i'     => '\1is',
		'/(shoe)s$/i'              => '\1',
		'/(o)es$/i'                => '\1',
		'/(bus)es$/i'              => '\1',
		'/([m|l])ice$/i'           => '\1ouse',
		'/(x|ch|ss|sh)es$/i'       => '\1',
		'/(m)ovies$/i'             => '\1\2ovie',
		'/(s)eries$/i'             => '\1\2eries',
		'/([^aeiouy]|qu)ies$/i'    => '\1y',
		'/([lr])ves$/i'            => '\1f',
		'/(tive)s$/i'              => '\1',
		'/(hive)s$/i'              => '\1',
		'/([^f])ves$/i'            => '\1fe',
		'/(^analy)ses$/i'          => '\1sis',
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
		'/([ti])a$/i'              => '\1um',
		'/(p)eople$/i'             => '\1\2erson',
		'/(m)en$/i'                => '\1an',
		'/(s)tatuses$/i'           => '\1\2tatus',
		'/(c)hildren$/i'           => '\1\2hild',
		'/(n)ews$/i'               => '\1\2ews',
		'/s$/i'                    => '',
	);
	
	// Cache
	static protected $_plural_caches   = array();
	static protected $_singular_caches = array();
	
	
	
	/*
	 * camelize("active_record")  => "ActiveRecord"
	 * camelize("active_record", false)  =>  "activeRecord"
	 * camelize("active_record/errors")  =>  "ActiveRecord::Errors"
	 * camelize("active_record/errors", false)  =>  "activeRecord::Errors"
	 */
	static public function camelize($lower_case_and_underscored_word, $ucfirst=true) {
		$out = str_replace(array('_','/'), array(' ',"\t"), $lower_case_and_underscored_word);
		$out = ucwords($out);
		$out = str_replace(array(' ',"\t"), array('','::'), $out);
		if (!$ucfirst) {
			$out = preg_replace('`^([A-Z]{1})`e', "strtolower('\\1')", $out);
		}
		return $out;
	}
	
	
	
	/*
	 * classify("egg_and_hams")  =>  "EggAndHam"
	 * classify("posts")  =>  "Post"
	 */
	static public function classify($table_name) {
		return self::camelize(self::singularize($table_name));
	}
	
	
	
	/*
	 * constantize("Module")  =>  "Module"
	 * constantize("Class")  =>  "Class"
	 */
	// Pas compris l'utilité  -  http://api.rubyonrails.org/classes/Inflector.html
	static public function constantize($camel_cased_word) {}
	
	
	
	/*
	 * dasherize("puni_puni")  =>  "puni-puni"
	 */
	static public function dasherize($underscored_word) {
		return str_replace('_', '-', $underscored_word);
	}
	
	
	
	/*
	 * demodulize("ActiveRecord\CoreExtensions\String\Inflections")  =>  "Inflections"
	 * demodulize("Inflections")  =>  "Inflections"
	 */
	static public function demodulize($class_name_in_module) {
		$str = explode('\\', $class_name_in_module);
		return array_pop($str);
	}
	
	
	
	/*
	 * foreign_key("Message")  =>  "message_id"
	 * foreign_key("Message", false)  =>  "messageid"
	 * foreign_key("Admin\Post")  =>  "post_id"
	 */
	static public function foreign_key($class_name, $separate_class_name_and_id_with_underscore=true) {
		$out  = self::underscore(self::demodulize($class_name));
		$out .= $separate_class_name_and_id_with_underscore ? '_id' : 'id';
		return $out;
	}
	
	
	
	/*
	 * humanize("employee_salary")  =>  ("Employee salary")
	 * humanize("author_id")  =>  ("Author")
	 */
	static public function humanize($string) {
		$out = str_replace('_', ' ', str_replace(array('_ids', '_id'), '', $string));
		return ucfirst(trim($out));
	}
	
	
	
	/*
	 * ordinalize(1)  =>  "1st"
	 * ordinalize(2)  =>  "2nd"
	 * ordinalize(1002)  =>  "1002nd"
	 * ordinalize(1003)  =>  "1003rd"
	 */
	static public function ordinalize($number) {
		if ($number%100<14 && $number%100>10) {
			return $number.'th';
		} else {
			switch ($number%10) {
				case 1: return $number.'st';
				case 2: return $number.'nd';
				case 3: return $number.'rd';
			}
			return $number.'th';
		}
	}
	
	
	
	/*
	 * pluralize("post")  =>  "posts"
	 * pluralize("octopus")  =>  "octopi"
	 * pluralize("sheep")  =>  "sheep"
	 * pluralize("words")  =>  "words"
	 * pluralize("the blue mailman")  =>  "the blue mailmen"
	 * pluralize("CamelOctopus")  =>  "CamelOctopi"
	 */
	static public function pluralize($word) {
		$result = (string)$word;
		
		if (isset(self::$_plural_caches[$word])) {
			return self::$_plural_caches[$word];
		}
		
		$rules = array_merge(self::$plural_rules, self::$_plural_rules);
		foreach($rules as $rule=>$replacement) {
			if (preg_match($rule, $result)) {
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}
		
		self::$_plural_caches[$word] = $result;
		
		return $result;
	}
	
	
	
	/*
	 * singularize("posts")  =>  "post"
	 * singularize("octopi")  =>  "octopus"
	 * singularize("sheep")  =>  "sheep"
	 * singularize("word")  =>  "word"
	 * singularize("the blue mailmen")  =>  "the blue mailman"
	 */
	static public function singularize($word) {
		$result = (string)$word;
		
		if (isset(self::$_singular_caches[$word])) {
			return self::$_singular_caches[$word];
		}
		
		$rules = array_merge(self::$singular_rules, self::$_singular_rules);
		foreach($rules as $rule=>$replacement) {
			if (preg_match($rule, $result)) {
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}
		
		self::$_singular_caches[$word] = $result;
		
		return $result;
	}
	
	
	
	/*
	 * tableize("RawScaledScorer")  =>  "raw_scaled_scorers"
	 * tableize("egg_and_ham")  =>  "egg_and_hams"
	 * tableize("fancyCategory")  =>  "fancy_categories"
	 */
	static public function tableize($class_name) {
		return self::pluralize(self::underscore($class_name));
	}
	
	
	
	/*
	 * titleize("man from the boondocks")  =>  "Man From The Boondocks"
	 * titleize("x-men: the last stand")  =>  "X Men: The Last Stand"
	 */
	static public function titleize($word) {
		$out = str_replace('-', ' ', $word);
		$out = self::humanize(self::underscore($out));
		$out = ucwords($out);
		return $out;
	}
	
	
	
	/*
	 * underscore("ActiveRecord")  =>  "active_record"
	 * underscore("ActiveRecord::Errors")  =>  "active_record/errors"
	 */
	static public function underscore($string) {
		$out = preg_replace('`([A-Z]{1})`', '_\\1', $string);
		$out = strtolower(trim($out, '_'));
		$out = preg_replace('`::_?`', '/', $out);
		return $out;
	}
	
}

?>