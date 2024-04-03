<?
if (!function_exists('roundBetter')) {
    function roundBetter($number, $precision = 0, $mode = PHP_ROUND_HALF_UP, $direction = NULL) {    
        if (!isset($direction) || is_null($direction)) { 
            return round($number, $precision, $mode); 
        } 
        
        else { 
            $factor = pow(10, -1 * $precision); 
    
            return strtolower(substr($direction, 0, 1)) == 'd' 
                ? floor($number / $factor) * $factor 
                : ceil($number / $factor) * $factor; 
        } 
    } 
}
// roundBetterUp(1999, -3) => 2000 
// roundBetterUp(1001, -3) => 2000 
if (!function_exists('roundBetterUp')) {
    function roundBetterUp($number, $precision = 0, $mode = PHP_ROUND_HALF_UP) { 
        return roundBetter($number, $precision, $mode, 'up'); 
    }
}
if (!function_exists('roundBetterDown')) {
    function roundBetterDown($number, $precision = 0, $mode = PHP_ROUND_HALF_UP) { 
        return roundBetter($number, $precision, $mode, 'down'); 
    }
}

?>