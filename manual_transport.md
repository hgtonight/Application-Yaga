Manual Transport
================

If you are looking to transfer your Yaga configuration from one forum
installation to the next, it is highly suggested you use the transport tools
included on the Yaga settings page. This will dump your configurations, database
records, and files into a single archive file that you can then upload to the
new installation.

If you were directed here by an error, I have _outlined_ what you should do to
get as complete a transport as possible. Keep in mind that transports are not
intended to maintain any earned badges, ranks, or reactions between installs.

## Export ##

1. Backup your database
2. Dump the GDN_Action, GDN_Badge, and GDN_Rank tables using your favorite mysql
   tool (e.g. phpMyAdmin)
3. Save the configurations starting with 'Yaga' in the `/conf/config.php` file
4. Download all files in the `/uploads/yaga` folder

## Import ##
1. Import the SQL dumps into your new database
2. Paste the saved configurations into the `/conf/config.php` file of your new
   install.
3. Upload your image files into `/uploads/yaga` of your new install
4. Profit!