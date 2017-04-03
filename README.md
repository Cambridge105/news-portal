## About ##
This presents an IRN-like portal for local news we have sourced ourselves and other clips we have rights to use. Stories do not have to have audio - they can be standalone scripts.

### Architecture ###
The system exists in two parts, the server is an internet-accessible webserver, allowing people not in the studio to upload and access content. The database backend is also on the webserver. The smaller client component is hosted inside the studio LAN and is used for importing audio clips to Rivendell.
A small PHP script in the client component regularly checks the webserver for new audio content. If some is found, the script downloads the audio and imports it to Rivendell. It then calls back to the webserver so that the webserver can write the Rivendell cart number for the clip into the database.

## Deployment Notes ##
### Server requirements ###

 - PHP (it was written in PHP 7.1.1 but anything PHP 5.2 or later should be OK (PHP 5.2 is required for JSON functions)
 - MySQL database
 - The 'upload_max_filesize' constant in *php.ini* must be set to something sensible for audio clips (the default 2MB probably isn't enough), and 'file_uploads' must be set on, obviously.

### Deploying to the server ###
1. Make all files inside the *server/* directory served by the web server
2. Create a database in mysql called *localnews*
3. Run *stories_table.sql* to create the stories table where data will be kept
4. Create a PHP file in the root of the served directory called *db_credentials.php* and copy the following contents, editing as applicable:

 `<?php
$db_user="<USERNAME>";
$db_pass="<PASSWORD>";
$db_host="<SERVERNAME>";
?>`

5.  Ensure the *uploads/* subdirectory is writable by the webserver user
6. Edit the path to the webserver (using an addressable domain name) at the following line:
	- Line 78 of *news-portal.js*

### Client requirements ###
- PHP  (it was written in PHP 7.1.1 but anything PHP 5.2 or later should be OK (PHP 5.2 is required for JSON functions)
- Rivendell import (rdimport) available at a prompt
- Must be able to access the webserver

### Deploying to the client ###
1. Edit the address of the webserver at the following line:
	- Line 3 of *studio_importer.php*
2. Create a directory on the client which the user running the PHP script will have write access to
3. Edit the path (Note: this must be the full path on Linux systems) to that writable directory at:
    - line 10 of *studio_importer.php* 
    - line 23 of *studio_importer.php*
4. Set *studio_importer.php* to run in a cron job every minute

## To do list ##
- Delete imported audio from the remote *uploads/* directory
- Present a record of when clips were played in the UI (we can get this from the *_SRT tables in the Rivendell database)
- Implement HTTP Basic Auth to restrict access
- Ensure sticky stories appear at the top of the list
