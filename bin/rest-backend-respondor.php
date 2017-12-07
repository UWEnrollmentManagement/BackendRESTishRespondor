#!/usr/bin/env php
<?php

function copy_dir($baseSource, $source, $destination, $search = [], $replace = [])
{
    $files = scandir("$baseSource\\$source");
    $files = array_diff($files, ['.', '..']);

    foreach ($files as $file) {
        if (is_dir("$baseSource\\$source\\$file")) {
            mkdir("$destination\\$source\\$file");
            copy_dir($baseSource, "$source\\$file", $destination, $search, $replace);
        } else {
            $contents = file_get_contents("$baseSource\\$source\\$file");
            $contents = str_replace($search, $replace, $contents);
            file_put_contents("$destination\\$source\\$file", $contents);
        }
    }


}

$arg = $argv[1];
switch ($arg) {
    case "init":
        if ($argc < 4) {
            echo("Usage: rest-backend-respondor init MEDIATOR NAMESPACE \n");
            echo("Eg: rest-backend-respondor init propel ExampleAPI\n");
            die();
        }
        $mediator = $argv[2];
        $namespace = $argv[3];
        copy_dir(__DIR__ . "\\file-templates\\$mediator", '', getcwd(), ['{{ namespace }}'], [$namespace]);

        echo("Project initiated. You may now wish to:\n");
        echo("$: cd schema\n");
        echo("$: ../vendor/bin/propel model:build\n");
        echo("$: ../vendor/bin/propel sql:build\n");
        echo("$: ../vendor/bin/propel sql:insert\n");
        echo("$: cd ..\n");
        echo("$: composer dump-autoload\n");
        echo("$: ./vendor/bin/phpunit test\n");
        break;
}
