<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/test', function(){
//     $response = Http::get('content.guardianapis.com/search', [
//         'api-key' => env('GUARDIAN_API_KEY'),
//     ]);
//     $data= $response->json();
//     foreach($data['response']['results'] as $article){
//         echo $article['id'] . '<br>';
//         echo $article['webPublicationDate'] . '<br>';
//         echo $article['sectionId'] . '<br><br>';
//     }
//     // return $response->json();
// });

// Route::get('/test', function(){
//     $response = Http::get('api.nytimes.com/svc/search/v2/articlesearch.json', [
//         // 'fq' => 'section_name:("Sports")',
//         'api-key' => env('NYTIMES_API_KEY'),
//     ]);
//     $data= $response->json();
//     foreach($data['response']['docs'] as $article){
//         echo $article['_id'] . '<br>';
//         echo $article['pub_date'] . '<br>';
//         echo $article['section_name'] . '<br>';
//         echo $article['byline']['original'] . '<br><br>';
//     }
//     // return $response->json();
// });
