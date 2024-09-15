<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CommandUtils;

class CreatePage extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'create:page';
    protected $description = 'Creates a new page folder structure, controller, view, and adds a route';

    protected $usage       = 'create:page [page_name] [template]';
    protected $arguments   = [
        'page_name' => 'The name of the page to be created',
        'template' => 'The name of the template to extend to be created',
    ];


    public function run(array $params)
    {
        // Step 1: Validate and collect the page name
        $pageName = $params[0] ?? null;
        $template = $params[1] ?? null;

        if (!$pageName) {
            CLI::write('Page name is required.', 'red');
            return;
        }

        if (!$template) {
            CLI::write('Page template is required.', 'red');
            return;
        }

        $exploded = explode("/", $pageName);
        $path = "";
        if (count($exploded)  > 1) {
            $info = pathinfo($pageName);
            $pageName = $info['basename'];
            $path = $info['dirname'];
        }

        // Step 2: Check if page already exists (to prevent overwriting)
        if ($this->pageExists($pageName, $path)) {
            CLI::write("Page {$pageName} already exists!", 'yellow');
            return;
        }

        // Step 3: Create JS, CSS, Controller, View, and Route
        $this->createJSandCSS($pageName, $path);
        $this->createController($pageName, $path);
        $this->createView($pageName, $template, $path);
        $this->addRoute($pageName, $path);

        CLI::write("Page {$pageName} has been created successfully.", 'green');
    }

    /**
     * Helper function to check if the page already exists
     */
    protected function pageExists($pageName, $path)
    {
        // Check JS, CSS, Controller, and View existence
        $controllerPath = ucfirst($pageName);
        if ($path !== "") {
            $controllerPath = $path . "/" . ucfirst($pageName);
            $pageName = $path . "/" . $pageName;
        }

        return is_dir(FCPATH . 'assets/js/' . $pageName) ||
            is_dir(FCPATH . 'assets/css/' . $pageName) ||
            file_exists(APPPATH . 'Controllers/' . $controllerPath . '.php') ||
            file_exists(APPPATH . 'Views/pages/' . $pageName . '.php');
    }

    /**
     * Step 3: Create JS and CSS folder structure
     */
    protected function createJSandCSS($pageName, $path)
    {
        if ($path !== "") {
            $pageName = $path . "/" . $pageName;
        }
        // Paths
        $jsPath = FCPATH . 'assets/src/' . $pageName;
        $cssPath = FCPATH . 'assets/css/' . $pageName;

        // create globals
        $this->createGlobals();

        // Create folders
        $this->createFolder($jsPath);
        $this->createFolder($cssPath);

        $className = ucfirst($pageName);
        $jsContent = <<<JS
            // JavaScript for {$pageName}"
            import $ from "jquery"
            import * as t from './types'

            $(() => {
                // code...  
            })
        JS;

        $page = ucfirst(pathinfo($pageName)['basename']);
        $typesContent = <<<JS
        // Types for {$pageName}
            export type {$page}Props = {
                count: number,
                onclick?: () => void
            }
        JS;
        // Create default index.js and index.css
        $this->createFile($jsPath . '/index.ts', $jsContent);
        $this->createFile($jsPath . '/types.ts', $typesContent);
        $this->createFile($cssPath . '/index.css', "/* CSS for {$pageName} */");

        // add the components folder
        $this->createFolder($jsPath . "/components");

        CLI::write("TS and CSS structure created for page: {$pageName}", 'green');
    }

    protected function createGlobals()
    {
        $globalsCSSPath = FCPATH . "assets/css/globals";
        $globalsJSPath = FCPATH . "assets/src/globals";

        if (!file_exists($globalsCSSPath)) {
            $this->createFolder($globalsCSSPath);
            $this->createFile($globalsCSSPath . "/globals.css", "/* global css are written here */");
            CLI::write("CSS globals created", 'green');
        }

        if (!file_exists($globalsJSPath)) {
            $this->createFolder($globalsJSPath);

            $jsContent = <<<JS
            import $ from "jquery"

            $(() => {
                // code...
            })
            JS;

            $this->createFile($globalsJSPath . "/index.ts", $jsContent);
            $this->createFile($globalsJSPath . "/utils.ts", "/* global utility functions are exported here */");
            CLI::write("TS globals created", 'green');
        }
    }

    /**
     * Step 4: Create the Controller
     */
    protected function createController($pageName, $path)
    {
        $title = ucfirst($pageName);
        $namespace = "App\Controllers";
        $uses = "";
        $page = $pageName;

        if ($path !== "") {
            $pageName = $path . "/" . ucfirst($pageName);
            $title = ucfirst(pathinfo($pageName)['basename']);
            $namespace = $namespace . "\\" . implode("\\", explode("/", $path));

            $uses = "use App\Controllers\BaseController;";

            if (!file_exists(APPPATH . "Controllers/" . $path)) {
                $this->createFolder(APPPATH . "Controllers/" . $path);
            }
        }
        // Controller path
        $controllerPath = APPPATH . 'Controllers/' . $pageName . '.php';
        $bundleName = str_replace("/", "_", $path . "/" . $page);

        $controllerName = $title;
        // Controller content
        $controllerContent = <<<PHP
        <?php
        
        namespace {$namespace};

        {$uses}

        class {$controllerName} extends BaseController
        {
            public function index()
            {
             \$data = [
                    'title' => '$title',
                    'pageName' => '$page',
                    'css' => ['assets/css/$path/$page/index.css'],
                    'js' => ['assets/js/dist/$bundleName.bundle.js'],
                ];

               return view('pages/{$path}/{$page}/{$page}', \$data);
            }
        }
        PHP;

        $this->createFile($controllerPath, $controllerContent);
        CLI::write("Controller created for page: {$pageName}", 'green');

        $this->addWebpackEntry($bundleName, $pageName);
    }

    protected function addWebpackEntry($bundleName, $pageName)
    {
        $webpackConfigPath = ROOTPATH . 'webpack.config.js';

        if (! file_exists($webpackConfigPath)) {
            CLI::error('webpack.config.js file does not exist.');
            return;
        }

        // Define new entry to add
        $newEntry = "    {$bundleName}: './public/assets/src/{$pageName}/index.ts', \n";
        
        $configContent = file_get_contents($webpackConfigPath);

        // Find the 'entry' object and insert the new entry inside it
        if (preg_match('/entry: {\s*([^}]*)\s*}/', $configContent, $matches)) {
            $existingEntries = $matches[1]; // Capture the existing entries

            // Add the new entry if it doesn't already exist
            if (strpos($existingEntries, $bundleName) === false) {
                $updatedEntries = $existingEntries . $newEntry;
                $configContent = str_replace($existingEntries, $updatedEntries, $configContent);

                // Write the updated content back to the webpack.config.js file
                file_put_contents($webpackConfigPath, $configContent);

                CLI::write("New entry {$bundleName} added to webpack config!", 'green');
            } else {
                CLI::write("Entry {$bundleName} already exists in the webpack config.", 'yellow');
            }
        } else {
            CLI::error('Could not find the entry object in webpack.config.js.');
        }
    }
    /**
     * Step 5: Create the View with linked JS and CSS
     */
    protected function createView($pageName, $template, $path)
    {

        if ($path !== "") {
            $pageName = $path . "/" . ucfirst($pageName);
            if (!file_exists(APPPATH . "Views/pages/" . $path)) {
                $this->createFolder(APPPATH . "Views/pages/" . $path);
            }
        }

        $explodedTemplate = explode("/", $template);

        if (count($explodedTemplate) > 1) {
            $info = pathinfo($template);
            $templatepath = $info['dirname'];

            if (!file_exists(APPPATH . "Views/layouts/" . $templatepath)) {
                $this->createFolder(APPPATH . "Views/layouts/" . $templatepath);
            }
        }

        $viewPath = APPPATH . 'Views/pages/' . $pageName . "/" . pathinfo($pageName)['basename'] . '.php';
        $templatePath = APPPATH . 'Views/layouts/' . $template . '.php';

        // check if pages path exists
        if (!file_exists(APPPATH . 'Views/pages')) {
            $this->createFolder(APPPATH . 'Views/pages');
        }

        $this->createFolder(APPPATH . 'Views/pages/' . $pageName);

        // check if template exists
        if (!file_exists($templatePath)) {
            // check if layouts folder exists
            if (!file_exists(APPPATH . 'Views/layouts')) {
                $this->createFolder(APPPATH . 'Views/layouts');
            }
            $templateContent = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?= \$title ?? 'My Website' ?></title>

                <!-- Global CSS -->
                <link rel="stylesheet" href="<?= env('app.baseURL') . 'assets/css/globals/globals.css' ?>">

                <!-- Page-specific CSS if any -->
                <?php if (isset(\$css)): ?>
                    <?php foreach (\$css as \$cssFile): ?>
                        <link rel="stylesheet" href="<?= env('app.baseURL') . \$cssFile ?>">
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- JQuery -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

            </head>
            <body>

                <!-- Main content -->
                <?= \$this->renderSection('content') ?>

                <!-- Global JS -->
                <script src="<?= env('app.baseURL') . 'assets/js/dist/app.bundle.js?v=' . time() ?>"></script>

                <!-- Page-specific JS if any -->
                <?php if (isset(\$js)): ?>
                    <?php foreach (\$js as \$jsFile): ?>
                        <script type="module" src="<?= env('app.baseURL') . \$jsFile . '?v=' . time() ?>"></script>
                    <?php endforeach; ?>
                <?php endif; ?>
            </body>
            </html>

            HTML;

            $this->createFile($templatePath, $templateContent);
            CLI::write("Layout created: {$template}", 'green');
        }

        $viewContent = <<<HTML
        <?= \$this->extend("layouts/$template") ?>

        <?= \$this->section('content') ?>
            <p>$pageName page</p>
        <?= \$this->endSection() ?>
        HTML;

        $this->createFile($viewPath, $viewContent);
        CLI::write("View created for page: {$pageName}", 'green');
    }

    /**
     * Step 6: Add a Route for the Page
     */
    protected function addRoute($pageName, $path)
    {
        $routesPath = APPPATH . 'Config/Routes.php';

        $controllerName = ucfirst($pageName);
        $namespace = $controllerName;


        if ($path !== "") {
            $namespace = str_replace("/", "\\", $path) . "\\" . $controllerName;         
        }

        $route = <<<PHP
        
        \$routes->group('{$pageName}', static function(\$routes){
            \$routes->get('/' , '{$namespace}::index');
        });
                
        PHP;

        // Add the route to the Routes.php file
        if (file_put_contents($routesPath, $route, FILE_APPEND)) {
            CLI::write("Route added for page: {$pageName}", 'green');
        } else {
            CLI::write("Failed to add route for page: {$pageName}", 'red');
        }
    }

    /**
     * Helper method to create a folder if it doesn't exist.
     */
    protected function createFolder($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Helper method to create a file with the provided content.
     */
    protected function createFile($filePath, $content)
    {
        if (!file_exists($filePath)) {
            file_put_contents($filePath, $content);
        }
    }
}
