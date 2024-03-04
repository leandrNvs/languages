<?php
clearstatcache();

$act = $_GET['act'];

if($act === 'text') {

    $text = file($_FILES['text']['tmp_name']);

    $text = array_map(function($i) {
        $i = preg_replace('/^\d(.*?\d)?$/', '', $i);
        return trim($i);
    }, $text);

    $text = array_filter($text);

    die(implode(" ", $text));
}

if($act === 'save') {

    if(!file_exists('files')) {
        mkdir('files');
    }

    file_put_contents(
        'files/tmp.txt',
        file_get_contents('php://input')
    );

    die('saved');
}

if($act === 'load') {
    if(file_exists('files/tmp.txt')) {
        die(
            file_get_contents('files/tmp.txt')
        );
    }
}

if($act === 'download') {

    $text = file_get_contents('php://input');

    $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Languages</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    outline: none;
                    box-sizing: border-box;
                    font-family: sans-serif;
                }

                body {
                    background-color: #eee;
                    padding: 2rem;
                }

                p {
                    font-size: 1.2rem;
                    text-align: justify;
                    line-height: 1.5
                }

                span.word {
                    position: relative;
                    color: crimson;
                    cursor: pointer;
                }

                span.word:hover span {
                    display: block;
                }

                span.word span {
                    display: none;
                    position: absolute;
                    top: -150%;
                    left: 0;
                    background-color: #000;
                    padding: .3rem;
                    border-radius: .3rem;
                    color: #fff;
                    font-size: 0.925rem;
                    width: max-content;
                }
            </style>
        </head>
        <body>
            <p>
                {$text}
            </p>
        </body>
        </html>
    HTML;

    file_put_contents('files/text.html', $html);
    
    die('files/text.html');
}

if($act === 'upload') {

    if(!file_exists('audios')) {
        mkdir('audios');
    }

    if(end($_FILES)['type'] === 'text/plain') {

        $file = file(end($_FILES)['tmp_name']);

        $file = array_map(function($i) {
            $i = trim($i);
            $i = preg_split("/\t|\[sound:/", $i);
            $i[1] = str_replace(']', '', $i[1]);

            return $i;
        }, $file);


        foreach($file as $key => $value) {
            $new = strlen($key) === 1? '0' . $key : $key;
            @rename('audios/' . $value[1], 'audios/' . $new . '.mp3');
        }

        $file = array_map(function($i, $key) {
            $new = strlen($key) === 1? '0' . $key : $key;

            return <<<ITEM
                <dt>
                    <span>{$i[0]}</span>
                    <button data-index="{$key}" data-audio="{$new}.mp3">play</button>
                </dt>
                <dd>
                    </span>{$i[2]}</span>
                </dd>
            ITEM;
        }, $file, array_keys($file));

        $file = implode(" ", $file);

        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Languages</title>
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        outline: none;
                        box-sizing: border-box;
                        font-family: sans-serif;
                    }

                    body {
                        background-color: #eee;
                        padding: 2rem;
                    }

                    dt {
                        background: #ddd;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        cursor: pointer;
                    }

                    dt, dd {
                        padding: 1rem 2rem;
                    }

                    dd {
                        display: none;
                    }

                    button {
                        padding: 0.5rem 1rem;
                    }
                </style>
            </head>
            <body>
                <dl>
                    {$file}
                </dl>

                <audio src=""></audio>

                <script>
                    const dt = document.querySelectorAll('dt');
                    const buttons = document.querySelectorAll('button');
                    const audio = document.querySelector('audio');
                    let last = null;

                    dt.forEach(i => {
                        i.addEventListener('click', function() {
                            this.nextElementSibling.style.display = 'block';

                            if(last) last.nextElementSibling.style.display = 'none';
                            
                            last = this;
                        });
                    })

                    buttons.forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.stopPropagation();

                            audio.src = 'audios/' + this.dataset.audio
                            audio.play();
                        });
                    });
                </script>
            </body>
            </html>
        HTML;

        $audios = glob('audios/*');

        $zip = new ZipArchive;

        $zip->open('languages.zip', ZipArchive::CREATE|ZipArchive::OVERWRITE);

        $zip->addEmptyDir('audios');

        foreach($audios as $audio) {
            $zip->addFile($audio, $audio);
        }

        $zip->addFromString('phrases.html', $html);

        $zip->close();

        foreach($audios as $audio) {
            unlink($audio);
        }

        die('languages.zip');
    } else {

        if(file_exists('languages.zip')) {
            unlink('languages.zip');
        }

        foreach($_FILES as $file) {
            @move_uploaded_file($file['tmp_name'], 'audios/' . $file['name']);
        }
    }
}