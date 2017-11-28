<?php

function generatePage($body, $style, $title="Home/About") {
    $page = <<<EOPAGE
<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="$style">
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>$title</title>
    </head>

    <body>
            $body
    </body>
</html>
EOPAGE;

    return $page;
}

?>
