<?php
define('ROOT', dirname(__DIR__));

use Src\Http\Kernel;
use Src\Http\Request;
use Src\Routing\Redirect;
use Src\Routing\Routes;
use Src\Storage\Storage;
use Src\View\View;

require_once dirname(__DIR__) . '/vendor/autoload.php';

View::path(ROOT . '/views/');
Storage::path(ROOT . '/storage/');

Routes::get('/', function() {
    $data = Storage::read();

    return View::render('home', [
        'data' => json_encode($data)
    ]);
});

Routes::get('/translate/{group}/{item}', function($group, $item) {
    $data = Storage::read()[$group][$item];

    $data = $data['translated'] ?? $data['subtitles'];

    return View::render('translate', [
        'group' => $group,
        'item'  => $item,
        'data'  => json_encode($data)
    ]);
});

Routes::get('/practice/{group}/{item}', function($group, $item) {
    $data = Storage::read()[$group][$item];

    $difficult = range(1, 5);

    array_splice($difficult, -2);

    $text = array_map(function($i) use ($difficult) {

        $broke = array_map(function($j) use($difficult) {
            if(rand(1, end($difficult)) == 1 && preg_match('/[\d\wÀ-Ÿ]+/', $j)) {
                return '<span data-word="'. preg_replace('/[^\d\wÀ-Ÿ]/', '', $j) .'">'. str_repeat('*', mb_strlen($j)) .'</span>';
            }

            return $j;
        }, explode(' ', $i));
    
        return implode(' ', $broke);
    }, $data['subtitles']);

    return View::render('practice', [
        'subtitles' => json_encode($text),
        'player'    => $data['youtube']
    ]);
});

Routes::post('/save/{group}/{item}', function($group, $item) {
    $data = Storage::read();

    $data[$group][$item]['translated'] = file_get_contents('php://input');

    Storage::store($data);

    die('Saved');
});

Routes::post('/', function(Request $request) {
    $data = Storage::read();

    $youtube   = $request->input('youtube');
    $saveAs    = $request->input('title');
    $subtitles = $request->input('subtitles');

    $saveAs = str_replace(' ', '-', strtolower($saveAs));

    if(in_array($saveAs, $data['titles'])) {
        Redirect::to('/');
    }

    $subtitles = array_map('trim', explode("\n", $subtitles));
    $subtitles = array_filter($subtitles);

    $timestamps = array_filter($subtitles, fn($i) => preg_match('/^[0-9]+(.*?)[0-9]+$/', $i));
    $timestamps = array_map(function($i) {
        [$min, $sec] = explode(':', $i);

        return $min * 60 + $sec;
    }, $timestamps);

    $texts = array_filter($subtitles, fn($i) => !preg_match('/^[0-9]+(.*?)[0-9]+$/', $i));

    $subtitles = array_combine($timestamps, $texts);

    $data['last-added'][strtolower($saveAs)] = [
        'subtitles' => $subtitles,
        'youtube'   => $youtube,
        'file'      => null
    ];

    $data['titles'][] = $saveAs;

    Storage::store($data);

    Redirect::to('/');
});


die(
    Kernel::send(Request::capture())
);
