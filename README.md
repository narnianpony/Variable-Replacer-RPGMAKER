# Variable-Replacer-RPGMAKER
This script replaces all occurrences of uses of a variable number in a game and replaces it with another number. This is helpful when you need to move some variables to keep your variables list organized.
 
## How to use:
If you use the game within a webserver, just drop the php file in the root directory of the game.

If not, you can either install php and use it from command line or use a portable version of php
 
**ALWAYS BACKUP YOUR FILES BEFORE USING THIS SCRIPT**

Consider possible permissions issues as RPGMAKER usually saves files as 664 (Linux)
 
From webserver

Navigate to this php file and use the following GET parameters

`variableReplacer.php?variable=250&to=251`       To move variable 250 to 251

`variableReplacer.php?switch=250&to=251`         To move switch 250 to 251
 
 
From command line

`php -f variableReplacer.php variable 250 251`   To move variable 250 to 251

`php -f variableReplacer.php switch 250 251`     To move switch 250 to 251
 
  
**Should work with RPG Maker MZ and MV**
 
### What this does
  - This replaces all variables/switches occurrences in RPGMAKER commands, in maps or common events
  - This replaces all variables/switches used in the \v[] \s[] format
  - This replaces all variables/switches used in javascript files with the functions $gameVariables and $gameSwitches, in case you wrote internal plugins for your game
  - Some plugins accept <<s[]>> and <<v[]>> formats, those are also replaced
  - Replace the name of the variable/switch in the system
 
 ### What this does not
 - Replace variables or switches used within "ranges" in the game, when the variable or switch is not the first one or the last one. AKA: When you modify a lot of variables or switches in one command
 - Replace variables or switches used in command plugin parameters
 
 There is also a testMap.json with an event with all the commands that uses variables and switches to test the code.
