<?php
/**
 * لینت PHP بدون نیاز به باینری php: token_get_all با TOKEN_PARSE.
 * استفاده: php-run.mjs tools/php-lint.php <dir|file> ...
 */
$targets = array_slice( $argv, 1 );
$files   = array();

foreach ( $targets as $target ) {
	if ( is_file( $target ) ) {
		$files[] = $target;
		continue;
	}

	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $target, FilesystemIterator::SKIP_DOTS ) );

	foreach ( $iterator as $file ) {
		if ( $file->isFile() && 'php' === $file->getExtension() ) {
			$files[] = $file->getPathname();
		}
	}
}

sort( $files );

$errors = 0;

foreach ( $files as $file ) {
	$code = file_get_contents( $file );

	try {
		token_get_all( $code, TOKEN_PARSE );
	} catch ( Throwable $e ) {
		$errors++;
		echo 'FAIL ' . $file . ' :: ' . $e->getMessage() . ' (خط ' . $e->getLine() . ")\n";
	}
}

echo count( $files ) . ' فایل، ' . $errors . " خطا\n";

exit( $errors > 0 ? 1 : 0 );
