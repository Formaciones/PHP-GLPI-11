<?php
// Script para eliminar comentarios PHP en todos los archivos bajo 03/plugins/
if (php_sapi_name() !== 'cli') {
    echo "Run from CLI only\n";
    exit(1);
}
$root = __DIR__ . '/../03/plugins';
if (!is_dir($root)) {
    echo "Directorio no encontrado: $root\n";
    exit(1);
}
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$files = [];
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    if (strtolower($file->getExtension()) === 'php') {
        $files[] = $file->getPathname();
    }
}
$changed = 0;
foreach ($files as $file) {
    $code = file_get_contents($file);
    if ($code === false) continue;
    // Use tokenizer to remove comments and preserve everything else
    $tokens = token_get_all($code);
    $out = '';
    foreach ($tokens as $token) {
        if (is_array($token)) {
            $id = $token[0];
            $text = $token[1];
            if ($id === T_COMMENT || $id === T_DOC_COMMENT) {
                // replace comment with equivalent whitespace/newlines to preserve line numbers
                $lines = substr_count($text, "\n");
                if ($lines > 0) {
                    $out .= str_repeat("\n", $lines);
                } else {
                    // single-line comment: preserve nothing (but keep a single space to avoid token concatenation issues)
                    $out .= ' ';
                }
            } else {
                $out .= $text;
            }
        } else {
            $out .= $token;
        }
    }
    // Trim trailing spaces on lines introduced
    $out = preg_replace('/[ \t]+$/m', '', $out);
    if ($out !== $code) {
        file_put_contents($file, $out);
        echo "Modified: $file\n";
        $changed++;
    }
}
echo "Done. Files changed: $changed\n";
