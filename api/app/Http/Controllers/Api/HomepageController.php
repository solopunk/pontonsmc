<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use Durlecode\EJSParser\Parser;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Homepage $homepage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (boolval(count($request->all()))) {

            $homepage = Homepage::find(1);

            $request->validate([
                'title' => 'sometimes|required|string',
                'intro' => 'json'
            ]);

            if ($request->filled('title')) {
                $homepage->title = $request->input('title');
            }

            if ($request->filled('intro')) {
                $html = Parser::parse($request->input('intro'))->toHtml();
                $homepage->intro_json = $request->input('intro');
                $homepage->intro_html = $html;
            }

            $homepage->saveQuietly();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Homepage $homepage)
    {
        //
    }
}
