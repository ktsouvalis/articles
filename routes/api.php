<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/articles', [ArticleController::class, 'getArticlesByCriteria']);

// API Documentation

// Endpoint: /articles
// Method: GET

// Description: Fetches articles based on the given criteria. The criteria are optional and can be combined to filter the results.
// contents key includes the source's original content of the article.

// Query Parameters:

// author (optional): Filter articles by author name. Supports partial matches.
// category (optional): Filter articles by category. Supports partial matches.
// published_at (optional): Filter articles by a specific publication date (format: YYYY-MM-DD).
// source (optional): Filter articles by source. Supports partial matches.

// // // the next two must be combined but if only one is provided it will be handled as published_at
// published_at_start (optional): Start date for filtering articles by publication date range (format: YYYY-MM-DD).
// published_at_end (optional): End date for filtering articles by publication date range (format: YYYY-MM-DD).
// // // 

// Response:
// Status: 200 OK
// Body: JSON array of articles, each containing the content field.

	
// total	325
// current_page	1
// last_page	17
// per_page	20
// next_page_url	"http://localhost:8000/api/articles?page=2"
// prev_page_url	null
// data	
//  0	
//      content	
//          url	"https://www.theguardian.…is-deep-in-trump-country"
//          title	"From brutalist school to…s deep in Trump country"
//          author	null
//          summary	"From brutalist school to…s deep in Trump country"
//          category	"Art and design"
//          published_at	"2024-12-09T05:00:13.000000Z"
//  1	{…}
//  2	{…}
//  3	{…}
//  4	{…}
//  5	{…}
//  6	{…}
//  7	{…}
//  8	{…}
//  9	{…}
//  10	{…}
//  11	{…}
//  12	{…}
//  13	{…}
//  14	{…}
//  15	{…}
//  16	{…}
//  17	{…}
//  18	{…}
//  19	{…}

// Notes:
// If no query parameters are provided, all articles will be returned.
// If both published_at_start and published_at_end are provided, articles within the specified date range will be returned.
// If only published_at is provided, articles published on that specific date will be returned.

// Example Calls:

// http://localhost:8000/api/articles?category=sport
// http://localhost:8000/api/articles?published_at_start=2024-12-01&published_at_end=2024-12-31
// http://localhost:8000/api/articles?published_at=2024-12-08
// http://localhost:8000/api/articles?source=guardian
// http://localhost:8000/api/articles?category=sport&published_at_start=2024-12-01&published_at_end=2024-12-31
// http://localhost:8000/api/articles?category=sport&published_at=2024-12-08
// http://localhost:8000/api/articles?source=guardian&published_at_start=2024-12-01&published_at_end=2024-12-31
// http://localhost:8000/api/articles?source=nyt&published_at=2024-12-08
// http://localhost:8000/api/articles?category=sport&source=guardian&published_at_start=2024-12-01&published_at_end=2024-12-31
// http://localhost:8000/api/articles?category=sport&source=guardian&published_at=2024-12-09


// page and page_size can be also added as query parameters to paginate the results. The default page_size is 20 and the maximum is 50.
// http:://localhost:8000/api/articles?page=2&page_size=10