<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Services\OlayaCrmDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OlayaInquiryDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp(); $this->withoutVite(); Http::preventStrayRequests();
        config(['site.contact_enabled'=>true, 'services.olaya_crm.enabled'=>true,
            'services.olaya_crm.url'=>'https://crm.example.test/api/public/crm/kobcode-leads',
            'services.olaya_crm.key'=>'test-key']);
    }
    private function message(array $changes = []): array
    {
        return $changes + ['name'=>'Klient demonstracyjny','email'=>'klient@kobcode.test',
            'company'=>'Firma demonstracyjna','phone'=>'+48 000 000 000','message'=>'Potrzebujemy systemu do obsługi zleceń.'];
    }
    private function fake(callable $callback): void
    {
        Http::swap(new \Illuminate\Http\Client\Factory);
        Http::preventStrayRequests(); Http::fake($callback);
    }
    private function acknowledge(): void
    {
        $this->fake(fn ($request) => Http::response(['request_id'=>$request['request_id'], 'data'=>['id'=>42], 'duplicate'=>false], 201));
    }
    public function test_form_persists_before_delivery_and_maps_all_fields(): void
    {
        $this->fake(function ($request) {
            $this->assertDatabaseHas('inquiries', ['email'=>'klient@kobcode.test', 'olaya_status'=>'pending']);
            $this->assertSame('test-key', $request->header('X-Website-Key')[0]);
            foreach ($this->message() as $key=>$value) $this->assertSame($value, $request[$key]);
            $this->assertArrayNotHasKey('website', $request->data());
            $this->assertArrayNotHasKey('_token', $request->data());
            return Http::response(['request_id'=>$request['request_id'],'data'=>['id'=>42]], 201);
        });
        $this->post('/kontakt', $this->message()+['website'=>''])->assertRedirect('/kontakt')->assertSessionHas('success');
        $inquiry=Inquiry::sole();
        $this->assertSame('sent', $inquiry->olaya_status); $this->assertSame(42, $inquiry->olaya_lead_id);
        $this->assertNotNull($inquiry->olaya_delivered_at); $this->assertSame(1,$inquiry->olaya_attempts);
        app(OlayaCrmDelivery::class)->send($inquiry->id);
        Http::assertSentCount(1);
    }
    public function test_transient_failure_keeps_message_and_retries_with_same_identifier(): void
    {
        Http::fakeSequence()->push([],503)->push(['request_id'=>'different-id','data'=>['id'=>2]],201);
        $this->post('/kontakt',$this->message())->assertRedirect('/kontakt');
        $inquiry=Inquiry::sole(); $uuid=$inquiry->olaya_request_id;
        $this->assertSame('pending',$inquiry->olaya_status); $this->assertSame('http_503',$inquiry->olaya_last_error);
        $this->artisan('kobcode:deliver-inquiries')->assertSuccessful(); Http::assertSentCount(1);
        $this->travel(61)->seconds();
        $this->artisan('kobcode:deliver-inquiries')->assertSuccessful();
        $this->assertSame('invalid_acknowledgement',$inquiry->fresh()->olaya_last_error);
        $this->acknowledge(); $this->travel(301)->seconds();
        $this->artisan('kobcode:deliver-inquiries')->assertSuccessful();
        $this->assertSame('sent',$inquiry->fresh()->olaya_status);
        Http::assertSent(fn ($request) => $request['request_id']===$uuid);
    }
    public function test_timeout_auth_error_and_manual_retry_are_durable(): void
    {
        $this->fake(fn () => throw new ConnectionException('Private transport detail'));
        $this->post('/kontakt',$this->message())->assertRedirect('/kontakt');
        $inquiry=Inquiry::sole(); $uuid=$inquiry->olaya_request_id;
        $this->assertSame('connection',$inquiry->olaya_last_error);
        $this->fake(fn () => Http::response([],403)); $this->travel(61)->seconds();
        $this->artisan('kobcode:deliver-inquiries')->assertSuccessful();
        $this->assertSame('failed',$inquiry->fresh()->olaya_status); $this->assertNull($inquiry->fresh()->olaya_next_attempt_at);
        $this->acknowledge();
        $this->artisan('kobcode:deliver-inquiries',['--inquiry'=>$inquiry->id])->assertSuccessful();
        $this->assertSame('sent',$inquiry->fresh()->olaya_status); $this->assertSame($uuid,$inquiry->fresh()->olaya_request_id);
    }
    public function test_disabled_integration_and_spam_make_no_external_requests(): void
    {
        config(['services.olaya_crm.enabled'=>false]);
        $this->post('/kontakt',$this->message())->assertRedirect();
        $this->assertNull(Inquiry::sole()->olaya_request_id); Http::assertNothingSent();
        config(['services.olaya_crm.enabled'=>true]);
        $this->post('/kontakt',$this->message()+['website'=>'spam'])->assertSessionHasErrors('website');
        $this->post('/kontakt',['name'=>'Incomplete'])->assertSessionHasErrors('email');
        Http::assertNothingSent(); $this->assertSame(1,Inquiry::count());
    }
    public function test_plain_http_production_is_rejected_and_redirects_are_not_followed(): void
    {
        $this->app['env']='production'; config(['services.olaya_crm.url'=>'http://crm.example.test/receive']);
        $inquiry=app(OlayaCrmDelivery::class)->store($this->message());
        app(OlayaCrmDelivery::class)->send($inquiry->id);
        $this->assertSame('configuration',Inquiry::sole()->olaya_last_error); Http::assertNothingSent();
        $this->app['env']='testing'; config(['services.olaya_crm.url'=>'https://crm.example.test/receive']);
        $this->fake(fn ($request) => Http::response('',302,['Location'=>'https://elsewhere.test/']));
        $this->artisan('kobcode:deliver-inquiries',['--inquiry'=>Inquiry::sole()->id])->assertFailed();
        $this->assertSame('http_302',Inquiry::sole()->olaya_last_error); Http::assertSentCount(1);
    }
    public function test_retry_limit_stops_and_deleted_remote_lead_is_not_resent(): void
    {
        $this->fake(fn () => Http::response([],500));
        $inquiry=app(OlayaCrmDelivery::class)->store($this->message());
        $inquiry->forceFill(['olaya_attempts'=>7])->save();
        app(OlayaCrmDelivery::class)->send($inquiry->id);
        $this->assertSame('failed',$inquiry->fresh()->olaya_status); $this->assertSame(8,$inquiry->fresh()->olaya_attempts);
        $this->fake(fn ($request) => Http::response(['request_id'=>$request['request_id'],'duplicate'=>true,'removed'=>true,'data'=>['id'=>null]],200));
        $this->artisan('kobcode:deliver-inquiries',['--inquiry'=>$inquiry->id])->assertSuccessful();
        $this->assertSame('sent',$inquiry->fresh()->olaya_status); $this->assertNull($inquiry->fresh()->olaya_lead_id);
    }
}
