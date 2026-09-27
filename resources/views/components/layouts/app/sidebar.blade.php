<?php
/** @var string|null $title */
?>
<!DOCTYPE html>
<html>
<head><title>{{ $title ?? "Test" }}</title></head>
<body>{{ $slot }}</body>
</html>