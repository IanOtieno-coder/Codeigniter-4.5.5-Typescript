<?php

class CommandUtils
{
    public function createFolder($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Helper method to create a file with the provided content.
     */
    public function createFile($filePath, $content)
    {
        if (!file_exists($filePath)) {
            file_put_contents($filePath, $content);
        }
    }

    public function prependToFile($filePath, $newContent)
    {
        // Check if the file exists
        if (!file_exists($filePath)) {
            throw new Exception("File does not exist.");
        }

        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'prefix_');

        // Open both the temporary file and the original file
        $input = fopen($filePath, 'r');
        $output = fopen($tempFile, 'w');

        if ($input === false || $output === false) {
            throw new Exception("Failed to open file streams.");
        }

        // Write the new content to the temporary file
        fwrite($output, $newContent . PHP_EOL);

        // Append the original file content in chunks to the temporary file
        while (!feof($input)) {
            $chunk = fread($input, 4096); // Read in 4KB chunks
            fwrite($output, $chunk);
        }

        // Close both file handlers
        fclose($input);
        fclose($output);

        // Replace the original file with the modified temp file
        rename($tempFile, $filePath);

        echo "Content added to the start of the file.";
    }
}
