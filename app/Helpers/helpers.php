<?php


// if (! function_exists('static_asset')) {
//     /**
//      * Generate an asset path for the application.
//      *
//      * @param  string  $path
//      * @param  bool|null  $secure
//      * @return string
//      */
//     function static_asset($path, $secure = null)
//     {
//         return config('app.static_asset_url').$path;
//     }
// }

if (! function_exists('authorHelper')) {
    
    function authorHelper($auth_ids) {
        // Fetch all the students from the 'student' table.
        $authors = \App\Author::select(['id','name'])->whereIn('id', $auth_ids)->get()->toArray();
        return $authors;
    }
}

if (! function_exists('firstAuthorHelper')) {
    
    function firstAuthorHelper($auth_id) {
        // Fetch all the students from the 'student' table.
        $author = \App\Models\Author::select(['id','name', 'image'])->where('id', $auth_id)->first();
        return $author;
    }
}