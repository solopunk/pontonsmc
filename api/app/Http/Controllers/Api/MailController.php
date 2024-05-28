<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewOrReminder;
use App\Models\Mail;
use App\Models\MailType;
use App\Models\Member;
use App\Models\MemberType;
use Durlecode\EJSParser\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MailController extends Controller
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
            'type' => ['required', 'string', Rule::in(['new', 'reminder'])],
            'title' => 'required|string',
            'content' => 'json',
            'to' => ['required_if:type,new', 'array', 'prohibited_if:type,reminder', Rule::in(['supporter', 'active', 'committee'])],
            'sheet' => 'boolean',

            'attachments' => 'array',
            'attachments.*' => 'file',

            'sent' => 'prohibited',
        ]);

        $mail = new Mail;

        // title
        $mail->title = $request->input('title');

        // content
        if ($request->filled('content')) {
            $html = Parser::parse($request->input('content'))->toHtml();
            $mail->content_json = $request->input('content');
            $mail->content_html = $html;
        }

        // mail type
        $type = MailType::where('uid', $request->input('type'))->first();
        $mail->mail_type()->associate($type);

        // aggregated by 'to' input
        switch ($request->input('type')) {
            case 'new':
                $members = [];
                foreach ($request->input('to') as $member) {
                    array_push($members, $member);
                }
                $mail->to = json_encode(['members' => $members]);
                break;

            case 'reminder':
                $mail->to = json_encode(['members' => ['latecomer']]);
                break;
        }

        if ($request->filled('sheet')) {
            $mail->sheet = $request->input('sheet');
        }

        $mail->saveQuietly();

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $mail
                    ->addMedia($attachment)
                    ->toMediaCollection('attachments');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mail $mail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mail $mail)
    {
        if (boolval(count($request->all()))) {
            $request->validate([
                'type' => ['sometimes', 'required', 'string', Rule::in(['new', 'reminder'])],
                'title' => 'sometimes|required|string',
                'content' => 'json',
                'to' => ['sometimes', 'required', 'required_if:type,new', 'array', 'prohibited_if:type,reminder', Rule::in(['supporter', 'active', 'committee'])],
                'sheet' => 'boolean',

                'attachments' => 'array',
                'attachments.*' => 'file',

                'sent' => 'prohibited',
            ]);

            // title
            if ($request->filled('title')) {
                $mail->title = $request->input('title');
            }

            // content
            if ($request->filled('content')) {
                $html = Parser::parse($request->input('content'))->toHtml();
                $mail->content_json = $request->input('content');
                $mail->content_html = $html;
            }

            // aggregated
            if ($request->filled('to')) {
                $members = [];
                foreach ($request->input('to') as $member) {
                    array_push($members, $member);
                }
                $mail->to = json_encode(['members' => $members]);
            }

            // type
            if ($request->filled('type')) {
                $type = MailType::where('uid', $request->input('type'))->first();
                $mail->mail_type()->associate($type);

                if ($request->input('type') === 'reminder') {
                    $mail->to = json_encode(['members' => ['latecomer']]);
                }
            }

            // sheet
            if ($request->filled('sheet')) {
                $mail->sheet = $request->input('sheet');
            }

            $mail->saveQuietly();

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $mail
                        ->addMedia($attachment)
                        ->toMediaCollection('attachments');
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mail $mail)
    {
        if (boolval($mail->sent) === false) {
            $mail->delete();
        }
    }

    public function deleteAttachment(Mail $mail, int $attachment)
    {
        $mail->getMedia('*')[$attachment]->delete();
    }

    public function send(Mail $mail)
    {
        $typesAggregated = json_decode($mail->to)->members;

        // retrieve mails of aggregated members
        $emails = Member::whereHas('member_types', function ($query) use ($typesAggregated) {
            $query->whereIn('uid', $typesAggregated);
        })->pluck('email');

        // if mails retrieved
        if (boolval($emails->toArray())) {
            // send mail
            FacadesMail::to($emails)->send(new NewOrReminder($mail));

            // switch mail to 'sent' mode
            $mail->sent = true;
            $mail->saveQuietly();
        }
    }
}
