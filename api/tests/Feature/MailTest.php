<?php

namespace Tests\Feature;

use App\Mail\NewOrReminder;
use App\Models\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_mail_for_supporters(): void
    {
        $this->seed();

        $json = json_encode([
            'time' => 1716647560421,
            'blocks' => [
                [
                    'id' => 'mhTl6ghSkV',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Hey. Meet the new Editor. On this picture you can see it in action. Then, try a demo.'
                    ]
                ]
            ],
            'version' => '2.16.1'
        ]);

        $title = fake()->word();

        $this->post('api/mail', [
            'type' => 'new',
            'title' => $title,
            'content' => $json,
            'to' => ['supporter']
        ]);

        $this->assertDatabaseHas('mails', [
            'mail_type_id' => 1,
            'title' => $title,
            'content_json' => $json,
            'to' => json_encode(['members' => ['supporter']]),
            'sent' => false,
            'sheet' => false
        ]);
    }

    public function test_create_mail_for_actives_and_commitee_w_sheet(): void
    {
        $this->seed();

        $title = fake()->word();

        $this->post('api/mail', [
            'type' => 'new',
            'title' => $title,
            'to' => ['active', 'committee'],
            'sheet' => true
        ]);

        $this->assertDatabaseHas('mails', [
            'mail_type_id' => 1,
            'title' => $title,
            'to' => json_encode(['members' => ['active', 'committee']]),
            'sent' => false,
            'sheet' => true
        ]);
    }

    public function test_mail_fail_when_type_new_and_to_latecomer(): void
    {
        $this->seed();

        $title = fake()->word();
        $resp = $this->post('api/mail', [
            'type' => 'new',
            'title' => $title,
            'to' => ['latecommer'],
        ]);

        $resp->assertInvalid(['to']);
        $resp->assertStatus(302);
        $this->assertDatabaseCount('mails', 0);
    }

    public function test_create_mail_w_attachments(): void
    {
        $this->seed();

        $mediaName1 = 'doc1.pdf';
        $mediaName2 = 'doc2.pdf';
        $this->post('api/mail', [
            'type' => 'new',
            'title' => fake()->word(),
            'to' => ['supporter'],
            'attachments' => [
                UploadedFile::fake()->create($mediaName1),
                UploadedFile::fake()->create($mediaName2),
            ],
        ]);

        $this->assertDatabaseCount('media', 2);
    }

    public function test_delete_attachment(): void
    {
        $this->seed();

        $mediaName1 = 'doc1.pdf';
        $mediaName2 = 'doc2.pdf';
        $this->post('api/mail', [
            'type' => 'new',
            'title' => fake()->word(),
            'to' => ['supporter'],
            'attachments' => [
                UploadedFile::fake()->create($mediaName1),
                UploadedFile::fake()->create($mediaName2),
            ],
        ]);

        $this->assertDatabaseCount('media', 2);

        $this->delete('api/mail/1/delete-attachment/0');

        $this->assertDatabaseCount('media', 1);
    }

    public function test_patch_title_and_content_of_mail(): void
    {
        $this->seed();

        $json = json_encode([
            'time' => 1716647560421,
            'blocks' => [
                [
                    'id' => 'mhTl6ghSkV',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Hey. Meet the new Editor. On this picture you can see it in action. Then, try a demo.'
                    ]
                ]
            ],
            'version' => '2.16.1'
        ]);

        $title = fake()->word();

        $this->post('api/mail', [
            'type' => 'new',
            'title' => $title,
            'content' => $json,
            'to' => ['supporter'],
        ]);

        $this->assertDatabaseHas('mails', [
            'mail_type_id' => 1,
            'title' => $title,
            'content_json' => $json,
            'to' => json_encode(['members' => ['supporter']]),
            'sent' => false,
            'sheet' => false
        ]);

        $newTitle = fake()->word();
        $newJson = json_encode([
            'time' => 1716647560421,
            'blocks' => [
                [
                    'id' => 'mhTl6ghSkV',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'new'
                    ]
                ]
            ],
            'version' => '2.16.1'
        ]);

        $this->patch('api/mail/1', [
            'title' => $newTitle,
            'content' => $newJson,
        ]);

        $this->assertDatabaseCount('mails', 1);
        $this->assertDatabaseHas('mails', [
            'mail_type_id' => 1,
            'title' => $newTitle,
            'content_json' => $newJson,
            'to' => json_encode(['members' => ['supporter']]),
            'sent' => false,
            'sheet' => false
        ]);
    }

    public function test_patch_title_content_and_aggregates_of_mail(): void
    {
        $this->seed();

        $json = json_encode([
            'time' => 1716647560421,
            'blocks' => [
                [
                    'id' => 'mhTl6ghSkV',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Hey. Meet the new Editor. On this picture you can see it in action. Then, try a demo.'
                    ]
                ]
            ],
            'version' => '2.16.1'
        ]);

        $title = fake()->word();

        $this->post('api/mail', [
            'type' => 'new',
            'title' => $title,
            'content' => $json,
            'to' => ['supporter'],
        ]);

        $this->assertDatabaseHas('mails', [
            'mail_type_id' => 1,
            'title' => $title,
            'content_json' => $json,
            'to' => json_encode(['members' => ['supporter']]),
            'sent' => false,
            'sheet' => false
        ]);

        $newTitle = fake()->word();
        $newJson = json_encode([
            'time' => 1716647560421,
            'blocks' => [
                [
                    'id' => 'mhTl6ghSkV',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'new'
                    ]
                ]
            ],
            'version' => '2.16.1'
        ]);

        $this->patch('api/mail/1', [
            'title' => $newTitle,
            'content' => $newJson,
            'to' => ['supporter', 'active', 'committee'],
        ]);

        $this->assertDatabaseCount('mails', 1);
        $this->assertDatabaseHas('mails', [
            'mail_type_id' => 1,
            'title' => $newTitle,
            'content_json' => $newJson,
            'to' => json_encode(['members' => ['supporter', 'active', 'committee']]),
            'sent' => false,
            'sheet' => false
        ]);
    }

    public function test_patch_mail_type_from_new_to_reminder(): void
    {
        $this->seed();

        $this->post('api/mail', [
            'type' => 'new',
            'title' => fake()->sentence(),
            'to' => ['supporter', 'active', 'committee'],
        ]);

        $this->assertDatabaseHas('mails', [
            'mail_type_id' => 1,
            'to' => json_encode(['members' => ['supporter', 'active', 'committee']]),
        ]);

        $this->patch('api/mail/1', [
            'type' => 'reminder'
        ]);

        $this->assertDatabaseCount('mails', 1);
        $this->assertDatabaseCount('mail_types', 2);
    }

    public function test_patch_mail_type_from_reminder_to_new(): void
    {
        $this->seed();

        $this->post('api/mail', [
            'type' => 'reminder',
            'title' => fake()->sentence(),
        ]);

        $this->assertDatabaseHas('mails', [
            'mail_type_id' => 2,
            'to' => json_encode(['members' => ['latecomer']]),
        ]);

        $this->patch('api/mail/1', [
            'type' => 'new',
            'to' => ['supporter', 'committee']
        ]);

        $this->assertDatabaseCount('mails', 1);
        $this->assertDatabaseHas('mails', [
            'mail_type_id' => 1,
            'to' => json_encode(['members' => ['supporter', 'committee']]),
        ]);
    }

    public function test_patch_mail_sheet(): void
    {
        $this->seed();

        $this->post('api/mail', [
            'type' => 'new',
            'title' => fake()->sentence(),
            'to' => ['supporter', 'active', 'committee'],
        ]);

        $this->assertDatabaseHas('mails', [
            'sheet' => false
        ]);

        $this->patch('api/mail/1', [
            'sheet' => true
        ]);

        $this->assertDatabaseHas('mails', [
            'sheet' => true
        ]);
    }

    public function test_add_attachments(): void
    {
        $this->seed();

        $this->post('api/mail', [
            'type' => 'new',
            'title' => fake()->sentence(),
            'to' => ['supporter', 'active', 'committee'],
        ]);

        $this->assertDatabaseCount('mails', 1);

        $mediaName1 = 'doc1.pdf';
        $mediaName2 = 'doc2.pdf';
        $this->patch('api/mail/1', [
            'attachments' => [
                UploadedFile::fake()->create($mediaName1),
                UploadedFile::fake()->create($mediaName2),
            ],
        ]);

        $this->assertDatabaseCount('mails', 1);
        $this->assertDatabaseCount('media', 2);
    }

    public function test_delete_mail_w_attachments(): void
    {
        $this->seed();

        $mediaName1 = 'doc1.pdf';
        $mediaName2 = 'doc2.pdf';
        $this->post('api/mail', [
            'type' => 'new',
            'title' => fake()->sentence(),
            'to' => ['supporter'],
            'attachments' => [
                UploadedFile::fake()->create($mediaName1),
                UploadedFile::fake()->create($mediaName2),
            ],
        ]);

        $this->assertDatabaseCount('mails', 1);
        $this->assertDatabaseCount('media', 2);

        $this->delete('api/mail/1');

        $this->assertDatabaseCount('mails', 0);
        $this->assertDatabaseCount('media', 0);
    }

    public function test_mail_dont_delete_when_sent(): void
    {
        $this->seed();

        Mail::create([
            'mail_type_id' => 1,
            'title' => fake()->sentence(),
            'content_json' => json_encode([fake()->word() => fake()->sentence()]),
            'content_html' => fake()->randomHtml(),
            'to' => json_encode(['members' => ['supporter', 'committee']]),
            'sent' => true,
            'sheet' => false
        ]);

        $this->assertDatabaseCount('mails', 1);

        $this->delete('api/mail/1');

        $this->assertDatabaseCount('mails', 1);
    }

    public function test_send_email(): void
    {
        $this->seed();
        FacadesMail::fake();

        $supporterMail = 'ex@gmail.org';
        $supporterMail2 = 'ex222@gmail.org';
        $activeMail = fake()->email();

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => $supporterMail,
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->word(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->word()
            ]
        ]);

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => $supporterMail2,
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->word(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->word()
            ]
        ]);

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => $activeMail,
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->word(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2),
                'width' => fake()->randomFloat(2),
                'type' => 'engine',
                'homeport' => 'hercule'
            ]
        ]);

        $this->assertDatabaseCount('members', 3);

        $mailTitle = fake()->sentence();
        $this->post('api/mail', [
            'type' => 'new',
            'title' => $mailTitle,
            'to' => ['supporter', 'active'],
        ]);

        $resp = $this->get('api/mail/1/send');
        $resp->assertOk();

        FacadesMail::assertSent(NewOrReminder::class, function (NewOrReminder $mail) use ($supporterMail, $supporterMail2, $activeMail, $mailTitle) {
            return
                $mail->hasFrom('flobono@me.com') &&
                $mail->hasTo($supporterMail) &&
                $mail->hasTo($supporterMail2) &&
                $mail->hasTo($activeMail) &&
                $mail->hasSubject($mailTitle);
        });

        $this->assertDatabaseHas('mails', [
            'sent' => true
        ]);
    }

    public function test_send_emai_w_sheet(): void
    {
        $this->seed();
        FacadesMail::fake();

        $supporterMail = 'ex@gmail.org';

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => $supporterMail,
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->word(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->word()
            ]
        ]);

        $this->assertDatabaseCount('members', 1);

        $this->post('api/mail', [
            'type' => 'new',
            'title' => fake()->sentence(),
            'to' => ['supporter'],
            'sheet' => true
        ]);

        $resp = $this->get('api/mail/1/send');
        $resp->assertOk();

        FacadesMail::assertSent(NewOrReminder::class, function (NewOrReminder $mail) use ($supporterMail) {
            return
                $mail->hasFrom('flobono@me.com') &&
                $mail->hasTo($supporterMail) &&
                $mail->hasAttachment(
                    Attachment::fromPath(storage_path('app/public/sheet.pdf'))
                );
        });

        $this->assertDatabaseHas('mails', [
            'sent' => true
        ]);
    }
}
