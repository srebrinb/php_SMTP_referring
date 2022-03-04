<?php
require_once __DIR__.'/../vendor/autoload.php';
$configfile=file_get_contents("config.ini");
if($configfile!= null){
    $config=parse_ini_string($configfile, true) ;
}
$path = '../test/text2.eml';
$parser = new PhpMimeMailParser\Parser();
$parser->setText(file_get_contents($path));
$to = $parser->getAddresses('to');
$from = $parser->getAddresses('from');
$subject = $parser->getHeader('subject');
$text = $parser->getMessageBody('text');
$buffer = str_replace(array("\r\n","\r", "\n"), '#', $subject."\n".$text);
$len=$config["config"]["maxLen"];
$msg=urlencode(substr($buffer,0,$len));
$params=$config["config"]["params"];
$url=$config["config"]["url"];
$sys=@$config["sender"][$from[0]["address"]];


if (!$sys){
   // throw new Exception("not found sender:".$from[0]["address"]); 
   $sys=$config["config"]["defautReceiver"];
}
$receivers=$config["receiver"];
$msg_params=array();
foreach ($to as $t){
    if (@$receivers[$t["address"]]){
        $recv=$receivers[$t["address"]];
    }else{
        //throw new Exception("not found receiver:".$t["address"]); 
        $recv=$config["config"]["defautReceiver"];
    }
    $msg_params[]=str_replace(array("{msg}","{sys}","{recv}"),array($msg,$sys,$recv),$params);
}
foreach ($msg_params as $msg_param){
    sendMsg($url,$msg_param);
}
function sendMsg($u,$p){
            // create curl resource
            $url=$u.$p;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
            // $output contains the output string
            $output = curl_exec($ch);
            print_r($output);  
            $info = curl_getinfo($ch);
            print_r($info);  
            // close curl resource to free up system resources
            curl_close($ch); 
}