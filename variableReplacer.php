<?php
/*************************
 *  RPG MAKER Variable Replacer
 *  
 *  This script replaces all occurrences of uses of a variable number in a game and replaces it with another number
 *  This is helpful when you need to move some variables to keep your variables list organized.
 *
 *	How to use:
 *	If you use the game within a webserver, just drop the php file in the root directory of the game
 *	If not, you can either install php and use it from command line
 *
 *  ALWAYS BACKUP YOUR FILES BEFORE USING THIS SCRIPT
 *  Consider possible permissions issues as RPGMAKER usually saves files as 664 (Linux)
 *
 *  From webserver
 *  Navigate to this php file and use the following GET parameters
 *
 *  variableReplacer.php?variable=250&to=251       To move variable 250 to 251
 *  variableReplacer.php?switch=250&to=251         To move switch 250 to 251
 *
 *
 *  From command line
 *  php -f variableReplacer.php variable 250 251   To move variable 250 to 251
 *  php -f variableReplacer.php switch 250 251     To move switch 250 to 251
 * 
 *  
 *  Should work with RPG Maker MZ and MV
 *
 * What this does
 *  - This replaces all variables/switches occurrences in RPGMAKER commands, in maps or common events
 *  - This replaces all variables/switches used in the \v[] \s[] format
 *  - This replaces all variables/switches used in $gameVariables and $gameSwitches javascript files, in case you wrote internal plugins for your game
 *  - Some plugins accept <<s[]>> and <<v[]>> formats, those are also replaced
 *  - Replace the name of the variable/switch in the system
 *
 * What this does not
 * - Replace variables or switches used within "ranges" in the game. AKA: When you modify a lot of variables or switches in one command
 * - Replace variables or switches used in command plugin parameters
 *  
 */

$path = ""; //You can modify this to change the path if this file is not in the game root

//List of commands and possible variables uses in rpg maker code

$variablesCommands='
(\$gameVariables\.value\()XXX(\))
(\$gameVariables\.setValue\()XXX(,)
(\\\\[Vv]\[)XXX(\])
(<<v\[)XXX(\])
({"code":103,"indent":[-"0-9a-zA-Z]*,"parameters":\[)XXX(,)
({"code":104,"indent":[-"0-9a-zA-Z]*,"parameters":\[)XXX(,)
({"code":122,"indent":[-"0-9a-zA-Z]*,"parameters":\[)XXX(,)
({"code":122,"indent":[-"0-9a-zA-Z]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(,)
({"code":111,"indent":[-"0-9a-zA-Z]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(,)
({"code":125,"indent":[-"0-9a-zA-Z]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX(\])
({"code":126,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX(\])
({"code":127,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX(,)
({"code":128,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX(,)
({"code":311,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(,)
({"code":311,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX(,)
({"code":312,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(,)
({"code":312,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX(\])
({"code":326,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(,)
({"code":326,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX(\])
({"code":313,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(,)
({"code":314,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(\])
({"code":315,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(,)
({"code":315,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX(,)
({"code":316,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(,)
({"code":316,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX(,)
({"code":317,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX(,)
({"code":317,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":318,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":201,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":201,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":201,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":202,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":202,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":202,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":203,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":203,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":231,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":231,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":232,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":232,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":301,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":285,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":285,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":331,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":332,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":342,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,[-"0-9a-zA-Z]*,)XXX([,\]])
';

//List of commands and possible switches uses in rpg maker code

$switchesCommands=
'
(\$gameSwitches\.value\()XXX(\))
(\$gameSwitches\.setValue\()XXX(,)
(\\\\[Ss]\[)XXX(\])
(<<s\[)XXX(\])
({"code":111,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":121,"indent":[0-9]*,"parameters":\[[-"0-9a-zA-Z]*,)XXX([,\]])
({"code":121,"indent":[0-9]*,"parameters":\[)XXX([,\]])
({"code":27,"parameters":\[)XXX([,\]])
({"code":28,"parameters":\[)XXX([,\]])
';


//Show only errors and warnings
error_reporting(error_reporting() & ~E_NOTICE);


//Getting parameters either from argv or get
if ($_GET["to"]){
	if ($_GET["variable"]) { $argVariable=$_GET["variable"]; }
	if ($_GET["switch"]) { $argSwitch=$_GET["switch"]; }
	$argTo=$_GET["to"];
}elseif($argv[0]){
	if ($argv[1]=="variable") { $argVariable=$argv[2]; }
	if ($argv[1]=="switch")   { $argSwitch=$argv[2]; }
	$argTo=$argv[3];
}else{
	echo "Unable to get arguments either from GET or from argv\n";
	exit;
}

//Error detection
if ((!$argVariable)&&(!$argSwitch)){
	echo "No 'variable' or 'switch' arguments were passed. Read this file comments for documentation\n";
	exit;
}

if (!$argTo){
	echo "No 'to' arguments were passed. Read this file comments for documentation\n";
	exit;
}


//Replace XXX by variable number, then explode by line break and then remove empty items
if($argVariable){
	$what = "variable";
	$systemName = "variables";
	$from = $argVariable;
	$patterns = array_values(array_filter(explode("\n",str_replace("XXX", $argVariable, $variablesCommands))));
}elseif($argSwitch){
	$what = "switch";
	$systemName = "switches";
	$from = $argSwitch;
	$patterns = array_values(array_filter(explode("\n",str_replace("XXX", $argSwitch, $switchesCommands))));
}

//Adding the slashes to regexs. 
foreach ($patterns as $key => $value) {
	$patterns[$key]="/".$value."/";
}

/*
//If you want to debug
var_dump($patterns);
var_dump($dataFiles);
var_dump($pluginFiles);
*/


//Files to search
$dataFiles = glob($path."data/*.json");
$pluginFiles = glob($path."js/plugins/*.js");


$replacement = '${1}'.$argTo.'${2}';
$count = 0;
$totalCount = 0;


echo "Replacing ".$what." number ".$from." to ".$argTo."\n";

//For each file and for each pattern, replace
foreach ($dataFiles as $key => $value) {
	$fileContents = file_get_contents($value);
	foreach ($patterns as $pkey => $pvalue) {
		$count = 0;
		$fileContents = preg_replace($pvalue, $replacement, $fileContents,-1,$count);
		$totalCount=$totalCount+$count;
	}
	file_put_contents($value,$fileContents);
}

foreach ($pluginFiles as $key => $value) {
	$fileContents = file_get_contents($value);
	foreach ($patterns as $pkey => $pvalue) {
		$count = 0;
		$fileContents = preg_replace($pvalue, $replacement, $fileContents,-1,$count);
		$totalCount=$totalCount+$count;
	}
	file_put_contents($value,$fileContents);
}

//Replace variable or switch name
$fileContents = file_get_contents($path."data/System.json");
$systemData = json_decode($fileContents,true);

$systemData[$systemName][$argTo]=$systemData[$systemName][$from];
$systemData[$systemName][$from]="";

$fileContents = json_encode($systemData,JSON_UNESCAPED_UNICODE);
file_put_contents($path."data/System.json",$fileContents);

echo "Done: ".$totalCount." occurrences replaced\n";

?>
