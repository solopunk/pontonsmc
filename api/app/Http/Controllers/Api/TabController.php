<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tab;
use Durlecode\EJSParser\Parser;
use Illuminate\Http\Request;

class TabController extends Controller
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
    public function show(Tab $tab)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tab $tab)
    {
        if (boolval(count($request->all()))) {
            $request->validate([
                'title' => 'sometimes|required|string',
                'content' => 'json'
            ]);

            if ($request->filled('title')) {
                $tab->title = $request->input('title');
            }

            if ($request->filled('content')) {
                $html = Parser::parse($request->input('content'))->toHtml();
                $tab->content_json = $request->input('content');
                $tab->content_html = $html;
            }

            $tab->saveQuietly();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tab $tab)
    {
        //
    }
}
