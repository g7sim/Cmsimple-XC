<?php

function fullBackup()
{
    global $pth, $o;

    $sourceDirectory = realpath($pth['folder']['base']);
    if ($sourceDirectory === false) {
        $errorMessage = 'Error: Source directory path is invalid or does not exist: ' . htmlspecialchars($pth['folder']['base']);
        if (function_exists('XH_message')) {
             $GLOBALS['o'] .= XH_message('fail', $errorMessage);
        } else {
            error_log('Backup Error: ' . $errorMessage);
        }
        return false;
    }
    
    $sourceDirectory = rtrim(str_replace('\\', '/', $sourceDirectory), '/');

    $backupDirectoryRelative = 'userfiles/backups'; 
    $backupDirectory = $sourceDirectory . '/' . $backupDirectoryRelative; 

    $maxsize = 200000000; // 200 MB limit 
    $part = 1;
    $date = date('Y-m-d');
    $backupFileBaseName = "Sicherung-{$date}"; // Base name for zip files

    if (!is_dir($sourceDirectory) || !is_readable($sourceDirectory)) {
        $errorMessage = 'Error: Source directory not found or not readable: ' . htmlspecialchars($sourceDirectory);
         if (function_exists('XH_message')) {
             $GLOBALS['o'] .= XH_message('fail', $errorMessage);
        } else {
            error_log('Backup Error: ' . $errorMessage);
        }
        return false;
    }

    if (!is_dir($backupDirectory)) {
        if (!mkdir($backupDirectory, 0775, true)) { // Recursive creation
            $errorMessage = 'Error: Could not create backup directory: ' . htmlspecialchars($backupDirectory);
             if (function_exists('XH_message')) {
                 $GLOBALS['o'] .= XH_message('fail', $errorMessage);
            } else {
                error_log('Backup Error: ' . $errorMessage);
            }
            return false;
        }
    }
    if (!is_writable($backupDirectory)) {
         $errorMessage = 'Error: Backup directory is not writable: ' . htmlspecialchars($backupDirectory);
         if (function_exists('XH_message')) {
            $GLOBALS['o'] .= XH_message('fail', $errorMessage);
        } else {
            error_log('Backup Error: ' . $errorMessage);
        }
        return false;
    }

    $absoluteExcludeDir = str_replace('\\', '/', $backupDirectory); 

    $archive = new ZipArchive();
    $currentArchiveName = $backupDirectory . '/' . $backupFileBaseName . "_$part.zip";

    if ($archive->open($currentArchiveName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        $errorMessage = 'Error: Cannot create zip file: ' . htmlspecialchars($currentArchiveName);
        if (function_exists('XH_message')) {
             $GLOBALS['o'] .= XH_message('fail', $errorMessage);
        } else {
             error_log('Backup Error: ' . $errorMessage);
        }
        return false;
    }

    $totalSize = 0;

    try {
        $directoryIterator = new RecursiveDirectoryIterator(
            $sourceDirectory,
            RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::UNIX_PATHS // Use UNIX paths for consistency
        );

        $it = new RecursiveIteratorIterator(
            $directoryIterator,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($it as $fileinfo) {
           
            $filePath = $fileinfo->getPathname(); 

            if (strpos($filePath, $absoluteExcludeDir . '/') === 0) { 
			                continue; 
            }

            $relativePath = $it->getSubPathname();
            
            if (empty($relativePath)) {
                continue;
            }
            
            $relativePath = str_replace('\\', '/', $relativePath);

            if ($fileinfo->isDir()) {
                
                 $archive->addEmptyDir($relativePath);
                
            } elseif ($fileinfo->isFile()) {
                $fileSize = $fileinfo->getSize();

                if ($totalSize > 0 && ($totalSize + $fileSize) > $maxsize) {
                    $archive->close();
                    $part++;
                    $currentArchiveName = $backupDirectory . '/' . $backupFileBaseName . "_$part.zip";
                    if ($archive->open($currentArchiveName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                        $errorMessage = 'Error: Cannot create zip file part: ' . htmlspecialchars($currentArchiveName);
                        if (function_exists('XH_message')) {
                             $GLOBALS['o'] .= XH_message('fail', $errorMessage);
                        } else {
                             error_log('Backup Error: ' . $errorMessage);
                        }
                        return false; 
                    }
                    $totalSize = 0;
                }

                if ($archive->addFile($filePath, $relativePath)) {
                    $totalSize += $fileSize;
                    
                } else {
                     $warningMessage = 'Warning: Could not add file to zip: ' . htmlspecialchars($relativePath);
                     if (function_exists('XH_message')) {
                        $GLOBALS['o'] .= XH_message('warning', $warningMessage);
                    } else {
                        error_log('Backup Warning: ' . $warningMessage);
                    }
                }
            }
        }

    } catch (Exception $e) {
         $errorMessage = 'Error during backup iteration: ' . $e->getMessage();
        if (function_exists('XH_message')) {
             $GLOBALS['o'] .= XH_message('fail', $errorMessage);
        } else {
            error_log('Backup Error: Exception during iteration: ' . $e->getMessage());
        }
        if ($archive->filename) {
            @$archive->close();
        }
        return false;
    }

    $archive->close();
    return true; 
}


if (defined('XH_ADM') && XH_ADM && isset($_GET['backup'])) {
    if (fullBackup()) {
        if (function_exists('XH_message')) {
             $GLOBALS['o'] .= XH_message('success', 'Backup finished successfully. Download available in Menu: Files -> backups.');
        } else {
            error_log('Backup finished successfully.');
        }
    } else {
        if (!function_exists('XH_message')) {
             error_log('Backup process failed.'); 
         }
    }
}


/* ------- polyfills ----------- */
require_once('assets/polyfills/bootstrap.php');



/* -------------------- uriclean and security filter -----------------------*/

function redir301($goto, $protocol = "http://")
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $protocol . $goto);
    header("Connection: close"); 
    exit(); 
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443
            ? "https://"
            : "http://";

$current_uri = $_SERVER['REQUEST_URI'];

$chars_to_remove = ['?', '[', ']', '(', ')', '==']; 

$needs_redirect = false;

foreach ($chars_to_remove as $char) {
    if (strpos($current_uri, $char) !== false) {
        $needs_redirect = true;
        break; 
    }
}

if ($needs_redirect) {
    
    $host = $_SERVER['HTTP_HOST'];

    $cleaned_uri = str_replace($chars_to_remove, '', $current_uri);

    $cleaned_uri = preg_replace('#/+#', '/', $cleaned_uri);

    $goto = $host . $cleaned_uri;

    $is_admin = isset($adm) && $adm; 
    $is_login_action = isset($su) && $su == '&login'; 

    if (!$is_admin && !$is_login_action && ($host . $current_uri !== $goto) ) {
         redir301($goto, $protocol);
    }
}

/* ----------------------  anchors -------------------*/

if (!(XH_ADM && $edit)) {
$i = $s > -1 ? $s : 0;
$c[$i] = preg_replace(
'/<a([^>]*)href="?#(.*)"?/',
'<a$1href="' . $su . '#$2',
$c[$i]
);
}

/*----------------------CssCrush init----------------------------------------*/
require_once ('assets/crush/CssCrush.php');
use CssCrush\Crush;

/*----------------------Jshrink init----------------------------------------*/
require_once ('assets/js/Minifier.php');
use JShrink\Minifier;

