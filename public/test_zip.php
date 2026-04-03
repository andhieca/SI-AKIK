<?php
if (class_exists('ZipArchive')) {
    echo "<h1>Zip Extension is ENABLED ✅</h1>";
    echo "<p>PHP Version: " . phpversion() . "</p>";
    echo "<p>Loaded Configuration File: " . php_ini_loaded_file() . "</p>";
} else {
    echo "<h1>Zip Extension is MISSING ❌</h1>";
    echo "<p>PHP Version: " . phpversion() . "</p>";
    echo "<p>Loaded Configuration File: " . php_ini_loaded_file() . "</p>";
    echo "<p>Please enable <code>extension=zip</code> in the file above and RESTART the server.</p>";
}
