<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CvDownloadController extends Controller
{
public function __invoke(\Illuminate\Http\Request $req, \App\Models\Application $application)
{
    abort_unless($req->hasValidSignature(), 403);
    $this->authorize('viewCv', $application);

    $path = \Storage::disk('private')->path($application->cv_path);
    abort_unless(is_file($path), 404);

    $safe = 'CV_'.$application->name.'.pdf';
    return response()->download($path, $safe);
}

}
