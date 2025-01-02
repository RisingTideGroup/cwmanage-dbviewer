# ConnectWise Manage Database Viewer

Small PHP web application that'll connect directly to the MSSQL Database for CW Manage and display records.

Built with PHP 8.2.x designed to be run on IIS directly on the Manage Server

### No Authentication! BEWARE. Designed to login directly to the MSSQL Database

Please be sure to only use Read-Only MSSQL User access


Requires the use of the PHP MSSQL Driver from Microsoft

<img width="975" alt="33bb25a5-d132-40f4-91f9-4dd2c9e303cc" src="https://github.com/RisingTideGroup/cwmanage-dbviewer/assets/16570127/62d3e6ff-7515-4790-9c15-070c7e94e7dd">

## Install SQL and mount the database

Make sure you use SQL Server Authentication for this

## Install IIS and configure it
 1. Install IIS features, including CGI which will also install FastCGI
 2. Extract this repo and mount it in IIS, ensuring IUSR has modify rights
 3. Download PHP 8.2.x - make sure to get Non Thread Safe ZIP file download
 4. [Install PHP Manager](https://docs.lextudio.com/phpmanager/getting-started/installation.html)
 5. Extract contents to C:\PHP and register it on the website
 6. [Download the PHP MSSQL Driver package and extract it](https://learn.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server?view=sql-server-ver16)
 
### 💡 Tip
> Make sure you use the PDO Driver for the NTS PHP version you installed, for the architecture you installed. This is called out in the name of the file.

 7. Copy the PDO driver file for NTS php 8.2 for the architecture you installed into the ext folder where you extracted PHP
 9. Enable the extension using PHP Manager and run IIS Reset
 10. Setup a RO SQL User and connect to the website
