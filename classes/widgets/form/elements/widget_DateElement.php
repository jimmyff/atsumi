<?php

class widget_DateElement extends widget_AbstractElement {
	private $options = array();
    
    private $strings = array();

	public function __construct($args) {

		if(isset($args['yearRange'][0])) $this->yearRange[0] = $args['yearRange'][0];
		else $this->yearRange[0] = intval(date("Y")) - 100;

		if(isset($args['yearRange'][1])) $this->yearRange[1] = $args['yearRange'][1];
		else $this->yearRange[1] = idate("Y");


        $this->strings = array(
            'day'=>'Day',
            'month'=>'Month',
            'year'=>'Year'

        );
        
        if(isset($args['strings'])) $this->strings = array_merge($this->strings, $args['strings']);
        else ;
	}

	function renderElement() {
		$date = $this->getValue();
		$dayValue = isset($date['day']) ? $date['day'] : date("d");
		$monthValue = isset($date['month']) ? $date['month'] : date("m");
		$yearValue = isset($date['year']) ? $date['year'] : date("Y");


		$dayOptions = sfl("<option value='' >%s</option>", $this->strings['day']);
		for($i = 1; $i <= 31; $i++) {
			// TODO: Bug here days don't always return the correct number of days...

			$dayNumber = date("j", mktime(0, 0, 0, 1, $i, date("Y")));
			$dayValueLeading = date("d", mktime(0, 0, 0, 1, $i, date("Y")));

			if($dayValue == $i)
				$dayOptions .= sfl("<option value='%s' selected='selected'>%s</option>",$dayValueLeading,$dayNumber);
			else
				$dayOptions .= sfl("<option value='%s'>%s</option>", $dayValueLeading, $dayNumber);

		}
		$monthOptions = sfl("<option value='' >%s</option>", $this->strings['month']);
		for($i = 1; $i <= 12; $i++) {
			if(strval($monthValue) == strval($i))
				$monthOptions .= sfl("<option value='%s' selected='selected'>%s</option>", date("m", mktime(0, 0, 0, $i, 1, date("Y"))), date("F", mktime(0, 0, 0, $i, 1, date("Y"))));
			else
				$monthOptions .= sfl("<option value='%s'>%s</option>", date("m", mktime(0, 0, 0, $i, 1, date("Y"))), date("F", mktime(0, 0, 0, $i, 1, date("Y"))));

		}

		$yearOptions = sfl("<option value='' >%s</option>", $this->strings['year']);
		for($i = $this->yearRange[1]; $i >=  $this->yearRange[0]; $i--) {
			if(strval($yearValue) == strval($i))
				$yearOptions .= sfl("<option value='%s' selected='selected'>%s</option>", $i, $i);
			else
				$yearOptions .= sfl("<option value='%s'>%s</option>", $i, $i);
		}



		$out =	sfl("<select name='%s[day]' %s id='form_%s_day' class='inputDate inputDateDay'>%s</select>",
						$this->getName(),
						($this->tabindex) ? sf('tabindex="%s"', $this->tabindex) : '',
						$this->getName(),
						$dayOptions
					);

		$out .=	sfl("<select name='%s[month]' %s id='form_%s_month' class='inputDate inputDateMonth'>%s</select>",
						$this->getName(),
						($this->tabindex) ? sf('tabindex="%s"', $this->tabindex) : '',
						$this->getName(),
						$monthOptions
					);

		$out .=	sfl("<select name='%s[year]' %s id='form_%s_year' class='inputDate inputDateYear'>%s</select>",
						$this->getName(),
						($this->tabindex) ? sf('tabindex="%s"', $this->tabindex) : '',
						$this->getName(),
						$yearOptions
					);
		return $out;

	}
}

?>