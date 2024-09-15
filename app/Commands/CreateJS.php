<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CommandUtils;

class CreateJS extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'create:js';
    protected $description = 'Creates a css file in the assets/css directory';
    protected $usage = 'create:js folder_name file_name';

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

        $this->createJSFile($folder, $file);
    }

    protected function createJSFile($folder, $file)
    {
        $utils = new CommandUtils();
        $cssPath = FCPATH . "assets/js/" . $folder;

        if (!file_exists($cssPath)) {
            $utils->createFolder($cssPath);
        }

        $className = ucfirst($folder) . ucfirst($file);
        $jsContent = <<<JS
        export class {$className} {
            #utils
            #fetch
            constructor() {

            }
        }
        JS;

        $utils->createFile($cssPath . "/" . $file . ".js", $jsContent);

        CLI::write("CSS file created successfully: " . $folder . "/" . $file, "green");
    }
}
