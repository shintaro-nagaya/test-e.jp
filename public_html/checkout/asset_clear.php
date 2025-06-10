<?php
require_once (__DIR__. "/env.php");
function rm(string $dir, $root): void
{
    if(!str_contains($dir, $root)) {
        return;
    }
    foreach(glob($dir. "/*") as $item) {
        if(is_dir($item)) {
            rm($item, $root);
        } else {
            unlink($item);
        }
    }
    rmdir($dir);
}
rm(BUILD_DIR, dirname(__DIR__)."/build");

$build_res = shell_exec(SH_COMM. " ". BUILD_SH);
logging($build_res);