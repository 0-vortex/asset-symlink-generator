<?php
/**
 * @author    TED Vortex (Teodor Eugen Dutulescu)
 */

$config = array(
    'gitignore' => true,
    'vendorPath' => 'vendor',
    'modulePath' => 'module',
    'assetPath' => 'public',
    'maskVendor' => true
);

// ZF2 project root folder containing module/ and vendor/
$rootPath = realpath(__DIR__ . '/../../..');

// ZF2 project public access folder (files will be symlinked here)
$publicPath = $rootPath . '/public/';
if (! is_dir($publicPath)) {
    // Create the public folder
    echo 'Creating directory ' . $publicPath . PHP_EOL;
    mkdir($publicPath);
}

// Default public folder .gitignore file
if ($config['gitignore']) {
    $gitIgnorePath = $publicPath . '.gitignore';
    if (! is_file($gitIgnorePath)) {
        // Add a .gitignore file to the public folder
        echo 'Creating file ' . $gitIgnorePath . PHP_EOL;
    }
    $gitIgnoreHandle = fopen($gitIgnorePath, 'w+');
    fwrite($gitIgnoreHandle, '*');
    fclose($gitIgnoreHandle);
}

$moduleRegexp = "%^{$rootPath}/{$config['modulePath']}/([a-zA-Z0-9\-\_]+)/Module\.php%ism";
$vendorRegexp = "%^{$rootPath}/{$config['vendorPath']}/([a-zA-Z0-9\-\_]+)/([a-zA-Z0-9\-\_]+)/Module\.php%ism";
$directoryIterator = new RecursiveDirectoryIterator($rootPath, FilesystemIterator::SKIP_DOTS);
$recursiveIterator = new RecursiveIteratorIterator($directoryIterator);
$foundModules      = new RegexIterator($recursiveIterator, $moduleRegexp, RecursiveRegexIterator::GET_MATCH);
$foundVendors      = new RegexIterator($recursiveIterator, $vendorRegexp, RecursiveRegexIterator::GET_MATCH);

foreach ($foundModules as $cacheFolder) {
    $thisPath = dirname(realpath($cacheFolder[0]));

    echo 'Reached: ' . $thisPath . PHP_EOL . PHP_EOL;

    if (is_dir($thisPath . '/' . $config['assetPath'])) {
        $symlinkPath = $rootPath . '/' . $config['assetPath'] . '/' . mb_strtolower($cacheFolder[1]);

        // Unlink previously linked symlink
        echo 'Unlinking previously symlinked module asset folder: ' . $symlinkPath . PHP_EOL;
        if (is_link($symlinkPath)) {
            unlink($symlinkPath);
        }

        // Create symlink
        echo 'Creating module asset folder symlink: ' . $symlinkPath . PHP_EOL;
        symlink($thisPath . '/' . $config['assetPath'], $symlinkPath);
    }

    echo PHP_EOL;
}

foreach ($foundVendors as $cacheFolder) {
    $thisPath = dirname(realpath($cacheFolder[0]));

    echo 'Reached: ' . $thisPath . PHP_EOL . PHP_EOL;

    if (is_dir($thisPath . '/' . $config['assetPath'])) {
        $symlinkPath = $rootPath . '/' . $config['assetPath'] . '/' . mb_strtolower($cacheFolder[1]) . ($cacheFolder[2] ? '-' . mb_strtolower($cacheFolder[2]) : '');

        // Unlink previously linked symlink
        echo 'Unlinking previously symlinked module asset folder: ' . $symlinkPath . PHP_EOL;
        if (is_link($symlinkPath)) {
            unlink($symlinkPath);
        }

        // Create symlink
        echo 'Creating module asset folder symlink: ' . $symlinkPath . PHP_EOL;
        symlink($thisPath . '/' . $config['assetPath'], $symlinkPath);
    }

    echo PHP_EOL;
}