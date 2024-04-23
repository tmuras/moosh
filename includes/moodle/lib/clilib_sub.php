<?php


function cli_problem($text)
{
    fwrite(STDERR, $text . "\n");
}

function cli_error($text, $errorcode = 1)
{
    cli_problem($text);

    die($errorcode);
}