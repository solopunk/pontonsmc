<?php

namespace App\Http\Controllers\Api;

use App\ErrorResponseTrait;
use App\Http\Controllers\Controller;
use App\JsonResponseTrait;
use App\Models\Scoop;
use Durlecode\EJSParser\Parser;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ScoopController extends Controller
{
    use JsonResponseTrait, ErrorResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scoops = Scoop::paginate(10);
        return response()->json($scoops, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
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
        $scoop->content = $scoop->content_json;

        $scoop->makeHidden(['created_at', 'updated_at', 'content_html', 'content_json']);

        return response()->json($scoop, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scoop $scoop)
    {
        try {

            if (boolval(count($request->all()))) {
                $request->validate([
                    'title' => 'sometimes|required|string',
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
            redirect()->route('scoop.show', $scoop->id);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return $this->errorResponse('', 500, $e);
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
