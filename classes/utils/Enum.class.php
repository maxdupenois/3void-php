<?php 
class Enum{
	public static function generate($clss, $values){
		$temp = preg_split("/\\s\\s*/", $clss);
		$base_class_name = array_shift($temp);
		$base_class_code = 'abstract class '.$clss.' {
							protected $value;
							public function value() { return $this->value; }
							public function __toString() { return "value: ".$this->value; }
							';
		$enum_classes = array();
		foreach($values as $v => $enum){
			 $base_class_code .= 'public static function '.$enum.'(){
							return '.$enum.'::instance();
					  }';
			 $val = (is_string($v)?'"'.addslashes($v).'"':$v); 		  
			 $enum_classes[] = '
			 					class '.$enum.' extends '.$base_class_name.'{
									private static $instance = null;
									protected $value = '.$val.';
									private function __construct() {}
									public static function instance() {
										if (self::$instance === null) { self::$instance = new self(); }
										return self::$instance;
									}
								};';
		}
		$base_class_code .= '}';
		//echo $base_class_code;
		//foreach($enum_classes as $e) echo $e;
		eval($base_class_code);
		foreach($enum_classes as $e) eval($e);
	}
}
?>