<?php

// Function to organize files based on their types into folders
function organizeFiles($directory, $fileTypes) {

    // Check if the specified directory exists
    if (!is_dir($directory)) {
        echo "Error: Check if that directory exists!\n\n";
        return;
    }

    // Check if the user wants to organize all files [NEW]
    $organizeAllFiles = false;
    if(count($fileTypes) == 1 && trim($fileTypes[0]) === 'A') {
        $organizeAllFiles = true;
    }

    // Check if valid file types are provided
    if ($fileTypes == ['']) {
        echo "Error: Provide valid file types!\n\n";
        return;
    }

    // Get the list of files in the directory
    $files = scandir($directory);

    // Filter files based on specified file types or organize all files [MODIFIED]
    $filesToOrganize = ($organizeAllFiles)
        ? array_filter($files, function ($file) {
            return $file !== '.' && $file !== '..';
        })
        : array_filter($files, function ($file) use ($fileTypes) {
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
            return in_array($fileExtension, $fileTypes);
        });

    // Check if no files with specified types are found in the directory
    if (empty($filesToOrganize)) {
        echo "Error: No files with the specified types found in the directory!\n\n";
        return;
    }

    // Organize each file into its corresponding folder
    foreach ($filesToOrganize as $file) {

        // Skip special directory entries "." and ".."
        if ($file === '.' || $file === '..') {
            continue;
        }

        // Get the file extension and set the destination folder
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $destinationFolder = $directory . '/' . $fileExtension;

        // Create the destination folder if it doesn't exist
        if (!is_dir($destinationFolder)) {
            mkdir($destinationFolder);
        }

        // Move the file to its corresponding folder
        rename($directory . '/' . $file, $destinationFolder . '/' . $file);

        // Write changes to a log file
        $logFile = fopen('history.log', 'a');
        $logEntry = date('d-m-Y H:i:s') . " - Moved $file to $fileExtension folder - [$directory]\n\n";
        fwrite($logFile, $logEntry);
        fclose($logFile);
    }
    
    // Display completion message
    echo "\nFile organization complete.\n\n";
}

// Display the program title
echo "\n  .-')                           
 ( OO ).                         
(_)---\_)   ,------. .-'),-----. 
/    _ | ('-| _.---'( OO'  .-.  '
\  :` `. (OO|(_\    /   |  | |  |
 '..`''.)/  |  '--. \_) |  |\|  |
 .-._)   \_)|  .--'   \ |  | |  |
\       /  \|  |_)     `'  '-'  '
 `-----'    `--'         `-----' 

\n";

// Main loop to continuously get user input for directory and file types
while (true) {
    $directory = readline("Directory Path (CTRL+C to exit): ");
    echo "\nType [A] to organize all files\n"; // [NEW]
    $fileTypes = explode(',', readline("File Types (comma-separated without spaces): "));

    // Call the organizeFiles function with the provided inputs
    organizeFiles($directory, $fileTypes);
}

?>