<?php
/*Author: Juraj Korcek, VUT FIT 2017, IPP-SYN
*/
mb_internal_encoding("UTF-8");
mb_regex_encoding('UTF-8');

/**
 *
 * Printing help message
 *
 * @return void
 *
 */
function help_msg(){
  echo "\t--help help message will be printed on stdout (does not load an input)\n";
  echo "\t--format=filename determine fomat file. File can contain variable\n";
  echo "\t  number of formating entries. Format entry will consist of regex\n";
  echo "\t  which will determine highlited text and commands for formatting the text.\n";
  echo "\t--input=filename determine input file in UTF-8 encoding.\n";
  echo "\t--output=filename determine output file in UTF-8 encoding\n";
  echo "\t--br adds <br/> element at the end of the row (just after applying\n";
  echo "\t  formatting commands).\n";
  exit(0);
}

/**
 *
 * Printing error messages
 *
 * @param int  $errno Error code
 * @param string  $str String to be printed when error occured
 * @return void
 *
 */
function error_msg($errno,$str){    
  switch ($errno) {
    case "E_PARAMS":
      file_put_contents('php://stderr', "Wrong script parameters or unsupported combination\n");
      exit (1);
      break;
        
    case "E_FREAD":
      file_put_contents('php://stderr', "Error opening/reading file: ".$str."\n");
      exit(2);
      break;
            
    case "E_FWRITE":
      file_put_contents('php://stderr', "Error writing file: ".$str."\n");
      exit(3);
      break;
      
    case "E_FFORMAT":
      file_put_contents('php://stderr', "Invalid format file: ".$str."\n");
      exit(4);
      break;          
  }
}

/**
 *
 * Loads input file
 *
 * @param array $options array of arguments
 * @return string
 *
 */
function load_file($options) {
  if (array_key_exists("input",$options)) {
    $path = $options['input'];
  }else {
    $path = 'php://stdin';
  }  
  if (file_exists($path)) {
    if (is_readable($path)) {
      if (filesize($path) > 0){
        $file_content = file_get_contents($path);
        return $file_content;
      }
    } else {
      error_msg("E_FREAD","not readable "."\"".$path."\""." input file"); //detected not redable file
    }
  } elseif($path === 'php://stdin') {
    return $file_content = file_get_contents($path);
  } else {
      error_msg("E_FREAD","input file "."\"".$path."\""." does not exist"); //detected non-existing file
  }
}

/**
 *
 * Write to output file or stdout
 *
 * @param string  $file_content String to be saved to ouput file or stdout
 * @param array $options array of arguments
 * @return void
 *
 */
function write_file($file_content,$options) {
  if (array_key_exists("br",$options)) {  //adding <br /> element if it is present
    $file_content = mb_ereg_replace("\n", "<br />\n", $file_content);
  }
  
  if (array_key_exists("output",$options)) {  //if no output file specied, stdout is output
    $path = $options['output'];
  } else {
    $path = 'php://stdout';
  }  
  
  if ((file_exists($path)) or ($path === 'php://stdout')) {
    if ((is_writable($path)) or ($path === 'php://stdout')) {
      file_put_contents($path, $file_content);
    } else {
      error_msg("E_FWRITE","not writable "."\"".$path."\""." output file");
   }
  } elseif (@file_put_contents($path, $file_content) !== FALSE) {
  } else {
    error_msg("E_FWRITE","output directory "."\"".$path."\""." does not exist");
  }
 }
 
 /**
 *
 * Splitting format file to an array
 *
 * @param array  $options array of arguments
 * @param string  $input_file string which holds input file
 * @return array
 *
 */
 function parse_format_file($options,$input_file){
 	$path = $options['format'];
   
   if (file_exists($path)) {  //if no format file output file is input file
     if (is_readable($path)) {
       if (filesize($path) > 0) {
       $file_ptr = fopen($path, "r");
       } else {
        write_file($input_file,$options);
        exit(0);
       }       
     } else {
       write_file($input_file,$options);
       exit(0);
     }
   } else {
     write_file($input_file,$options);
     exit(0);
   }
   
   while (!feof($file_ptr)) {
     $line = fgets($file_ptr);  //reads format file line by line
     if (($line === "\n") or ((ord($line) === 0))) {
       continue;
     } 
     if ((mb_ereg_match("^[^\t]+\t+(italic|bold|teletype|underline|color:[A-Fa-f0-9]{6}|size:[1-7])(,[ \t]*(italic|bold|teletype|underline|color:[A-Fa-f0-9]{6}|size:[1-7]))*\n?$",$line) === false) ) {
       error_msg("E_FFORMAT", "format line error ".$line);  //regex which validate format line
     }       
     $line = mb_ereg_replace("\n","",$line);  //eliminating ending newline
     $format_file = mb_split("\t+",$line,2);  //spliting elements to array by tabulator
     if (array_key_exists('1',$format_file)) {
       $format_file[1]= mb_split(",[ \t]*",$format_file[1]);  //spliting formating commands to array
     }
     $format_array []= $format_file;
   }
   
   if (empty($format_array)) {
     write_file($input_file,$options);
     exit(0);
   } elseif (isset($format_array[count($format_array)-1][0]) and $format_array[count($format_array)-1][0] === "") {
     array_pop($format_array);  //poping empty array which can cause issues
   } 
   fclose($file_ptr);
   return $format_array;
 }
 /**
 *
 * Converting input regexes to perl-like regex
 *
 * @param string  $input_regex input regex from format file
 * @return string
 *
 */
 function regex_convert($input_regex) {
   $regex = "";
   $neg = "";
   $quant = array("*","+");
   $re_specials = array("\\","$","{","}","[","]","^","?","-","=","<",">",":");
   $l_brack_forb = array("+","*","|");
   $pipe_forb = array(")","|","+","*");
   $c_old = "";
   $c1 = "";
   $c1_valid = 0;
   $str_len = mb_strlen($input_regex);
   $reg_array = array_fill(0,$str_len,NULL);
   for ($i = 0; $i < $str_len; $i++){
     $c = mb_substr($input_regex,$i,1);
     if ($c === "%") {
       $i++;
       $c1=mb_substr($input_regex,$i,1);  //load one more char, to determine special regex
 
       if ($c1 === "s") {
         $reg_array[$i] = "[". $neg ." \\t\\n\\r\\f\\v]"; //special chars needs to be escaped
       } elseif ($c1 === "a") {
         if ($neg === "") {
           $reg_array[$i] = ".";
         } else {
           $reg_array[$i] = "[^\s\S]";  //regex, that does not match anything
         }
       } elseif ($c1 === "d") {
         $reg_array[$i] = "[" . $neg . "0-9]";
       } else if ($c1 === "l") {
         $reg_array[$i] = "[" . $neg . "a-z]";
       } elseif ($c1 === "L") {
         $reg_array[$i] = "[" . $neg . "A-Z]";
       } elseif ($c1 === "w") {
         $reg_array[$i] = "[" . $neg . "a-zA-Z]";
       } elseif ($c1 === "W") {
         $reg_array[$i] = "[" . $neg . "a-zA-Z0-9]";
       } elseif ($c1 === "t") {
         $reg_array[$i] = "[" . $neg . "\\t]";
       } elseif ($c1 === "n") {
         $reg_array[$i] = "[" . $neg . "\\n]";
       } elseif (in_array($c1,array(".","|","!","*","+","(",")","%"))) {
         $reg_array[$i] = "[" . $neg . "\\" . $c1 . "]";  //perl-like regex which needs to be escaped
       } else {
         error_msg("E_FFORMAT","invalid regex escape sequence "."\"".$input_regex."\"");
       }
       $neg = "";
       
     } elseif ($c === "!") {
       if ($neg === "^" or $i == ($str_len -1)) {
         error_msg("E_FFORMAT","invalid regex, double negation "."\"".$input_regex."\""); //if negation flag was set, print error
       } else $neg = "^"; // set negation flag
     } elseif (in_array($c,$re_specials)) { //negation of perl-like special symbols 
       $reg_array[$i] = "[" . $neg . "\\" . $c . "]";
       $neg = "";
     } elseif ($c === ".") {  //handling with concatenate symbol in input regex
       if ($i == 0 or $i == ($str_len - 1) or $c_old === "." or $c_old === "!" or $c_old === "|" or $c_old === "("){
         error_msg("E_FFORMAT","invalid regex, bad concatenate "."\"".$input_regex."\"");
       }
     } else {
       if ($neg === "^") {
         if ($c === "/") {
           $reg_array[$i] = "[" . $neg . "\\" .$c . "]";  //escaping slashes
         } elseif (in_array($c,$l_brack_forb)) {
           error_msg("E_FFORMAT","invalid regex negation "."\"".$input_regex."\"");
         } elseif (ord($c) >= 32) { //determine if symbol si printable
           $reg_array[$i] = "[" . $neg . $c . "]";         
         } else error_msg("E_FFORMAT","invalid regex, ASCII<32 "."\"".$input_regex."\"");
         $neg = "";
       } elseif ($c === ")" and ($c_old === "(" or $c_old === ".")) { //handling with empty parenthesis
         error_msg("E_FFORMAT","invalid regex, bracket error "."\"".$input_regex."\"");
       } elseif ((in_array($c,$quant) and $i==0) or (in_array($c_old,$quant) and in_array($c,$quant)) or (in_array($c,$quant) and $c_old === ".") or (in_array($c,$quant) and $c_old === "!")) { //quantifiers special cases
         error_msg("E_FFORMAT","invalid regex, bad quantifiers "."\"".$input_regex."\"");

       } elseif (($c === "|" and $i == 0) or ($c === "|" and $i == ($str_len -1)) or ($c === "|" and $c_old === "|") or ($c === "|" and $c_old === ".")) {
         error_msg("E_FFORMAT","invalid regex, pipe error "."\"".$input_regex."\"");
       } else {
         if ($c === "/") {
           $reg_array[$i] = "\\" . $c;
         } elseif (ord($c) >= 32) {
           $reg_array[$i] = $c;
         } else {
           error_msg("E_FFORMAT","invalid regex, ASCII<32 "."\"".$input_regex."\"");
         }
       }
     }
     $c_old = $c;
   }

   for ($i = 1;$i<count($reg_array);$i++){
     if (($reg_array[$i-1] === "(") and (in_array($reg_array[$i],$l_brack_forb))){
       error_msg("E_FFORMAT","invalid regex, bracket error "."\"".$input_regex."\"");
     } elseif (($reg_array[$i-1] === "|") and (in_array($reg_array[$i],$pipe_forb))) {
       error_msg("E_FFORMAT","invalid regex, pipe error "."\"".$input_regex."\"");
     } elseif(($reg_array[$i-1] === "+") and (($reg_array[$i] === "+") or ($reg_array[$i] === "*"))) {  //implementation of NQS extension
       $reg_array[$i-1] = "";
     } elseif (($reg_array[$i-1] === "*") and (($reg_array[$i] === "+") or ($reg_array[$i] === "*"))) { //implementation of NQS extension
       $reg_array[$i] = "*";
       $reg_array[$i-1] = "";
     }
   }
   
   $regex = implode("",$reg_array); //sticking array of string together
   
   if (@preg_match("/$regex/",NULL) === false) { //testing if coverted regex is perl-like and valid
     error_msg("E_FFORMAT","invalid regex, cannot be translated "."\"".$input_regex."\"");
   } 
   return $regex;
}
 
 /**
 *
 * Generating html tags
 *
 * @param array  $format_array array of parsed tags
 * @param int  $type determines opening (0) or closing (1) tag
 * @return string
 *
 */
 function generate_tags($format_array,$type) {
   $tag = "";
   $tag_concat = "";
   static $cnt =0;
   foreach ($format_array as $input_tags) {
     $tag = "<";
         if ($type === 1) { //adds "/" only if is closing tag
           $tag .= "/";
         }
         if ($input_tags === "bold") {
           $tag .= "b>"; 
         } elseif ($input_tags === "italic") {
           $tag .= "i>";
         } elseif ($input_tags === "underline") {
           $tag .= "u>";
         } elseif ($input_tags === "teletype") {
           $tag .= "tt>";
         } elseif (@preg_match('/size:[1-7]/',$input_tags)) { //testing valid format of format command
            if ($type === 0) {
              $tag .= "font size=" . substr($input_tags,-1) . ">";
            } else {
              $tag .= "font>";
            }
         } elseif (@preg_match('/color:[A-Fa-f0-9]{6}/',$input_tags)) { //testing valid format of format command
            if ($type === 0) {
              $tag .= "font color=#" . substr($input_tags,-6) . ">";
            } else {
              $tag .= "font>";
            }
         } else {
           error_msg("E_FFORMAT","invalid format tag "."\"".$input_tags."\"");
         }
         
         if ($type === 0) {
           $tag_concat = $tag_concat . $tag;  //concatenate tag with existing opening tag
         } else $tag_concat = $tag . $tag_concat; //concatenate tag with existing closing tag
   }
   return $tag_concat;
 }
 
 //creating tag array of formatting commands
 //appending formatting commands to output file
  /**
 *
 * Creating tag array of formatting tags and inserting it in output file
 *
 * @param array  $format_array array of parsed tags and regexes
 * @param string $input_file input file content
 * @return string
 *
 */
 function tag_array($format_array,$input_file) {
   $index_begin = 0;
   $index_end = 0;
   $regex_offset = 0;
   $matched_str_len = 0;
   $offset = 0; //initial offset for preg_math
   $tag_array = array_fill(0,mb_strlen($input_file)+1,NULL);  //create array with appropriate length
   foreach ($format_array as $key){ //take every key from format_array
     $regex = regex_convert($key[0]); //regex string is in key[0]
     generate_tags($key[1], 0); //validation of formatting commands
     while ((preg_match('/'.$regex.'/us',$input_file,$matched,PREG_OFFSET_CAPTURE,$regex_offset))) {  //matching regex in input file, saves matched string
       if ($matched[0][0] !== ""){
         $offset = mb_strlen(substr($input_file,0,$matched[0][1])); //calculating offset from beggining
         $matched_str_len = mb_strlen($matched[0][0]);         
         $tag_array[$offset] = $tag_array[$offset] . generate_tags($key[1], 0); //appending opening tag to array
         $tag_array[$offset+$matched_str_len] = generate_tags($key[1], 1) . $tag_array[$offset+$matched_str_len]; //appending closing tag to array
         $offset = $matched[0][1];
       } else $matched_str_len++; //if matched string is empty increment counter for not locking up
       $regex_offset = $offset+$matched_str_len;  //calculate next offset for preg_match
     }
     $regex_offset = 0; // zeroing offset for new formating line
   }
   $output_file = "";
   for ($i=0;$i<mb_strlen($input_file);$i++) {
     $output_file .= $tag_array[$i] . mb_substr($input_file,$i,1);  //concatenating tags from $tag_array with input file 
   }
   $output_file .= $tag_array[mb_strlen($input_file)]; //concatenate last tag
   return $output_file;
 }

$longopts = [
"help",
"format:",
"input:", //moze byt problem (bude brat aj argument --input bez rovnitka) mozno iba :
"output:",
"br",
];

$options = getopt(NULL,$longopts);
$output_content = "";

if (array_key_exists("help",$options)){
  if (count($argv) == 2){
        help_msg();
    }
  error_msg("E_PARAMS","if --help is passed, no more arguments are allowed");
}else {
  if (($argc-1) != count($options)) {
    error_msg("E_PARAMS","doubled parameters were passed");
  }
  if ($argc === 1) {
    exit(0);
  }
  $input_file = load_file($options);
  
  if (array_key_exists("format",$options)) {
    $format_file = parse_format_file($options,$input_file);
    $output_content = tag_array($format_file,$input_file);
  }else {
    $output_content = $input_file;
  }
write_file($output_content,$options);
}
?>