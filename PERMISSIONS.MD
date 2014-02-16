To add permissions in Yaga, open up /settings/about.php and under the last permission in "'RegisterPermissions'  => array(",
basically, just add the permission like this without quotations: "Yaga.Permission.Example, // What does this do? Fill in the question". 

You may notice that upon overwriting the old about file with the new about file that you just modified, it will not add the permission immediately. 
To see the newly added permissions, you must diasble and enable the application. Upon doing this, everything for Yaga will begin with Yaga. You will
need to follow the instructions in README.MD
