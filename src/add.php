<?php
function build_ini_string(array $a) {
    $out = '';
    $sectionless = '';
    foreach($a as $rootkey => $rootvalue){
        if(is_array($rootvalue)){
            // find out if the root-level item is an indexed or associative array
            $indexed_root = array_keys($rootvalue) == range(0, count($rootvalue) - 1);
            // associative arrays at the root level have a section heading
            if(!$indexed_root) $out .= PHP_EOL."[$rootkey]".PHP_EOL;
            // loop through items under a section heading
            foreach($rootvalue as $key => $value){
                if(is_array($value)){
                    // indexed arrays under a section heading will have their key omitted
                    $indexed_item = array_keys($value) == range(0, count($value) - 1);
                    foreach($value as $subkey=>$subvalue){
                        // omit subkey for indexed arrays
                        if($indexed_item) $subkey = "";
                        // add this line under the section heading
                        $out .= "{$key}[$subkey] = $subvalue" . PHP_EOL;
                    }
                }else{
                    if($indexed_root){
                        // root level indexed array becomes sectionless
                        $sectionless .= "{$rootkey}[] = $value" . PHP_EOL;
                    }else{
                        // plain values within root level sections
                        $out .= "$key = $value" . PHP_EOL;
                    }
                }
            }

        }else{
            // root level sectionless values
            $sectionless .= "$rootkey = $rootvalue" . PHP_EOL;
        }
    }
    return $sectionless.$out;
}

if ($argc<4){
    echo "add.php [sender|receiver] test@test.com [snderID|0888888888]";
    exit;
}
print_r($argv);
$s=$argv[1];
/*
[receiver]
test@test = 08888888

[sender]
send@test.com = senderID
*/
$configfile=file_get_contents("config.ini");
$config=array(
    'sender' => array(
    ),
    'receiver' => array(
    )
);
if($configfile!= null){
    $config=parse_ini_string($configfile, true) ;
}
$config[$s][$argv[2]]=$argv[3];
$ini=build_ini_string($config);
file_put_contents("config.ini",$ini);