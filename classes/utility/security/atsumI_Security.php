<?php

class atsumi_Security {


    // using the older inferior methods for random number generation
    static public function numericCodeLegacy ($ref, $salt, $length) {

        // seed random number generator
        srand(crc32(sf('%s:%s',$ref,$salt)));

        // generate the number
        $code = rand(pow(10,($length-1)), pow(10,$length)-1);
        return $code;
    }


    // returns a number based on a string at a certain length
    // useful for verification codes etc
    static public function numericCode ($ref, $salt, $length) {

        // seed random number generator
        mt_srand(crc32(sf('%s:%s',$ref,$salt)));

        // generate the number
        $code = mt_rand(pow(10,($length-1)), pow(10,$length)-1);
        return $code;
    }


    // returns a alphanumeric string based on a string at a certain length
    static public function alphaNumericCode (
        $ref, $salt, $length, $capitals = true,
        $numbers = true, $symbols = false) {

        $characters = 'abcdefghijklmnopqrstuvwxyz';
        if ($capitals) $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($numbers) $characters .= '0123456789';
        if ($symbols) $characters .= '!$%^&*+-_=@#.,><][}{';
        $len = strlen($characters);

        // seed random number generator
        mt_srand(crc32(sf('%s:%s',$ref,$salt)));

        // generate the string
        $code ='';
        for ($i = 0; $i < $length; $i++)
            $code .= $characters[mt_rand(0,$len-1)];

        return $code;
    }
	
}




?>