<?php
    return [
        'sourcePath'   => __DIR__ . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR,
        'languages'    => ['vi', 'en', 'ru'], //,'de','es','it','ja' https://cloud.google.com/translate/v2/using_rest#language-params http://www.w3.org/International/O-charset-lang.html Add languages to the array for the language files to be generated.
        'translator'   => 'Yii::t',
        'sort'         => false,
        'removeUnused' => false,
        'only'         => ['*.php'],
        'except'       => [
            '.svn',
            '.git',
            '.gitignore',
            '.gitkeep',
            '.hgignore',
            '.hgkeep',
            '/messages',
            '/vendor',
        ],
        'format'       => 'php',
        'messagePath'  => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'messages',
        'overwrite'    => true,
    ];