<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CommandUtils;

class CreateCss extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'create:css';
    protected $description = 'Creates a css file in the assets/css directory';
    protected $usage = 'create:css folder_name file_name';

    protected $arguments = [
        "folder_name" => "folder name of the file to be created",
        "file_name" => "file name of the file to be created"
    ];

    public function run(array $params)
    {
        $folder = $params[0] ?? null;
        $file = $params[1] ?? null;

        if (!$folder) {
            CLI::write("Folder name is required", "red");
            return;
        }

        if (!$file) {
            CLI::write("File name is required", "red");
            return;
        }

        $this->createCSSFile($folder, $file);
    }

    protected function createCSSFile($folder, $file)
    {
        $utils = new CommandUtils();
        $cssPath = FCPATH . "assets/css/" . $folder;

        if (!file_exists($cssPath)) {
            $utils->createFolder($cssPath);
        }

        $utils->createFile($cssPath . "/" . $file . ".css", "/* css file create via CLI */");

        CLI::write("CSS file created successfully: " . $folder . "/" . $file, "green");
    }
}
