<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scoop;
use Durlecode\EJSParser\Parser;
use Illuminate\Http\Request;

class ScoopController extends Controller
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
        $request->validate([
            'title' => 'required',
            'content' => 'json',

            'cover' => 'image',
            'attachments' => 'array',
            'attachments.*' => 'file',
        ]);

        $scoop = new Scoop;
        $scoop->title = $request->input('title');

        if ($request->filled('content')) {
            $html = Parser::parse($request->input('content'))->toHtml();
            $scoop->content_json = $request->input('content');
            $scoop->content_html = $html;
        }

        $scoop->saveQuietly();

        if ($request->hasFile('cover')) {
            $scoop
                ->addMediaFromRequest('cover')
                ->toMediaCollection('covers');
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $scoop
                    ->addMedia($attachment)
                    ->toMediaCollection('attachments');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Scoop $scoop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scoop $scoop)
    {
        if (boolval(count($request->all()))) {
            $request->validate([
                'title' => 'sometimes|required',
                'content' => 'json',

                'cover' => 'image',
                'attachments' => 'array',
                'attachments.*' => 'file',
            ]);

            if ($request->filled('title')) {
                $scoop->title = $request->input('title');
            }

            if ($request->filled('content')) {
                $html = Parser::parse($request->input('content'))->toHtml();
                $scoop->content_json = $request->input('content');
                $scoop->content_html = $html;
            }

            $scoop->saveQuietly();

            if ($request->hasFile('cover')) {
                $scoop
                    ->addMediaFromRequest('cover')
                    ->toMediaCollection('covers');
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $scoop
                        ->addMedia($attachment)
                        ->toMediaCollection('attachments');
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scoop $scoop)
    {
        // Not deleteQuietly or files will be kept.
        $scoop->delete();
    }

    public function toggleVisibility(Scoop $scoop)
    {
        $scoop->published = !$scoop->published;
        $scoop->saveQuietly();
    }

    public function deleteAttachment(Scoop $scoop, int $attachment)
    {
        $scoop->getMedia('*')[$attachment]->delete();
    }
}
