<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CommandUtils;

class CreateJSComponent extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Custom';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'create:component';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Creates a js component in the specified directory';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'folder' => "the folder to the file to be created"
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $path = $params[0] ?? null;

        if (!$path) {
            CLI::write("Folder name is required", "red");
            return;
        }

        $exploded = explode("/",$path);
        // check if its length is 2 
        if (count($exploded) !== 2) {
            CLI::write("Invalid folder name given", "red");
            return;
        }

        $this->createComponent($path);
    }

    protected function createComponent($path){
        $utils = new CommandUtils();

        $file_name = explode("/" , $path)[1];
        $folder_name = explode("/" , $path)[0];

        $folder_path = FCPATH . "assets/js/" . $folder_name . "/components";
        $file_path = $folder_path . "/" . $file_name . ".js";

        if (!file_exists($folder_path)) {
            $utils->createFolder($folder_path);
        }

        $className = ucfirst($file_name);
        $componentContent = <<<JS
        import * as utils from '../../globals/utils.js'

        export class {$className} {

        }
        JS;

        $utils->createFile($file_path, $componentContent);

        // import it in the index file for the page
        $page_index_file = FCPATH ."assets/js/" . $folder_name . "/index.js";
        $newContent = <<<JS
        import {$className} from "./components/{$file_name}.js";
        JS;

        $utils->prependToFile($page_index_file, $newContent);

        CLI::write("Component creted successfully: " . $file_path, "green");
    }
}
