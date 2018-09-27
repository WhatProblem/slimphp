<?php


return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

    
         // Database connection settings           
        "db" => [
            "host" => "localhost",
            "dbname" => "whatproblem",
            "user" => "root",
            "pass" => "",
            "options" => array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"
            )
        ],
    ],
];


