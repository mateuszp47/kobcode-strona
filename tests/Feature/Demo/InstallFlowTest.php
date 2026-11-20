<?php
namespace Tests\Feature\Demo;

use App\Demo\InstallFlow\{Money, Repository, Workspace};
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB, File, Mail, Notification, Schema};
use Tests\TestCase;

class InstallFlowTest extends TestCase
{
    use RefreshDatabase;
    private string $demoRoot;
    protected function setUp(): void
    {
        parent::setUp(); $this->withoutVite();
        $this->demoRoot = storage_path('framework/testing/installflow-'.bin2hex(random_bytes(8)));
        config(['demo.enabled'=>true, 'demo.allow_public'=>false, 'demo.storage_path'=>$this->demoRoot]);
    }
    protected function tearDown(): void
    {
        DB::purge('installflow'); File::deleteDirectory($this->demoRoot);
        parent::tearDown();
    }
    private function open(): void { $this->get('/demo/installflow')->assertOk(); }
    private function db() { return DB::connection('installflow'); }
    private function clientData(array $changes=[]): array
    {
        return $changes + ['name'=>'Klient testowy sesji','type'=>'person','email'=>'nowy@installflow.test','phone'=>'+48 000 000 000','city'=>'Radom','address'=>'ul. Przykładowa 1','notes'=>'Dane fikcyjne'];
    }
    private function offerData(array $changes=[]): array
    {
        return $changes + ['client_id'=>1,'title'=>'Oferta testowa','status'=>'draft','valid_until'=>now()->addDays(7)->toDateString(),
            'items'=>[['description'=>'Urządzenie','quantity'=>'2','unit_price'=>'100.01'],['description'=>'Montaż','quantity'=>'1.50','unit_price'=>'10.00']]];
    }
    public function test_every_screen_renders_and_seed_data_are_consistent_without_default_database_tables(): void
    {
        $this->open();
        $this->assertSame(30, $this->db()->table('clients')->count());
        $this->assertSame(24, $this->db()->table('orders')->count());
        $this->assertSame(18, $this->db()->table('offers')->count());
        $this->assertSame(6, $this->db()->table('employees')->count());
        $this->assertFalse(Schema::connection(config('database.default'))->hasTable('offers'));
        $paths=['/demo','/demo/installflow/opis','/demo/installflow','/demo/installflow/raporty','/demo/installflow/kalendarz'];
        foreach (['klienci','oferty','zlecenia','pracownicy'] as $path) {
            foreach (['','/nowy','/1','/1/edycja'] as $suffix) $paths[]='/demo/installflow/'.$path.$suffix;
        }
        $paths[]='/demo/installflow/oferty/1/druk';
        foreach ($paths as $path) {
            $response=$this->get($path)->assertOk();
            $dom=new \DOMDocument; @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
            $this->assertSame(1, (new \DOMXPath($dom))->query('//h1')->length, $path);
            if ($path!='/demo' && $path!='/demo/installflow/opis') {
                $response->assertHeader('X-Robots-Tag','noindex, nofollow');
                $this->assertStringContainsString('no-store',$response->headers->get('Cache-Control'));
            }
        }
        foreach ($this->db()->table('orders')->whereNotNull('offer_id')->get() as $order) {
            $offer=$this->db()->table('offers')->find($order->offer_id);
            $this->assertSame($offer->gross_cents,$order->gross_cents);
            $this->assertSame($offer->client_id,$order->client_id);
            $this->assertSame('accepted',$offer->status);
        }
    }
    public function test_client_create_edit_notes_search_delete_and_validation(): void
    {
        $this->open(); Mail::fake(); Notification::fake();
        $this->post('/demo/installflow/klienci',[])->assertSessionHasErrors(['name','city','type']);
        $this->post('/demo/installflow/klienci',$this->clientData())->assertRedirect('/demo/installflow/klienci/31');
        $this->get('/demo/installflow/klienci?q=Klient%20testowy')->assertOk()->assertSee('Klient testowy sesji');
        $this->put('/demo/installflow/klienci/31',$this->clientData(['name'=>'Zmieniona nazwa']))->assertRedirect();
        $this->post('/demo/installflow/klienci/31/kontakt',['channel'=>'email','note'=>'Ustalono termin.'])->assertRedirect();
        $this->get('/demo/installflow/klienci/31')->assertSee('Zmieniona nazwa')->assertSee('Ustalono termin.');
        $this->post('/demo/installflow/klienci/31/kontakt',['channel'=>'note','note'=>'<script>alert(1)</script>'])->assertRedirect();
        $this->get('/demo/installflow/klienci/31')->assertDontSee('<script>alert(1)</script>',false)->assertSee('&lt;script&gt;',false);
        $this->delete('/demo/installflow/klienci/1')->assertSessionHasErrors('client');
        $this->delete('/demo/installflow/klienci/31')->assertRedirect('/demo/installflow/klienci');
        $this->get('/demo/installflow/klienci/31')->assertNotFound();
        Mail::assertNothingSent(); Notification::assertNothingSent();
    }
    public function test_offer_calculation_is_exact_and_server_ignores_forged_totals(): void
    {
        $this->open();
        $this->post('/demo/installflow/oferty',$this->offerData(['gross_cents'=>1,'net_cents'=>1]))->assertRedirect('/demo/installflow/oferty/19');
        $offer=$this->db()->table('offers')->find(19);
        $this->assertSame(21502,$offer->net_cents); $this->assertSame(4945,$offer->vat_cents); $this->assertSame(26447,$offer->gross_cents);
        $this->assertSame(2,$this->db()->table('offer_items')->where('offer_id',19)->count());
        $this->put('/demo/installflow/oferty/19',$this->offerData(['status'=>'accepted']))->assertRedirect();
        $this->get('/demo/installflow/oferty/19/druk')->assertOk()->assertSee('264,47')->assertSee('Dokument demonstracyjny');
        $this->assertSame(3, Money::calculate([['description'=>'Rounding','quantity'=>'0.33','unit_price'=>'0.10']])['net_cents']);
        $this->post('/demo/installflow/oferty',$this->offerData(['items'=>[['description'=>'Zła cena','quantity'=>'1','unit_price'=>'-1']]]))->assertSessionHasErrors('items.0.unit_price');
        $this->post('/demo/installflow/oferty',$this->offerData(['client_id'=>999]))->assertSessionHasErrors('client_id');
        $this->assertSame(19,$this->db()->table('offers')->count());
    }
    public function test_accepted_offer_converts_once_and_keeps_original_terms(): void
    {
        $this->open();
        $this->post('/demo/installflow/oferty',$this->offerData())->assertRedirect();
        $this->post('/demo/installflow/oferty/19/zlecenie')->assertStatus(422);
        $this->put('/demo/installflow/oferty/19',$this->offerData(['status'=>'accepted']))->assertRedirect();
        $this->post('/demo/installflow/oferty/19/zlecenie')->assertRedirect('/demo/installflow/zlecenia/25');
        $this->post('/demo/installflow/oferty/19/zlecenie')->assertRedirect('/demo/installflow/zlecenia/25');
        $this->assertSame(25,$this->db()->table('orders')->count());
        $this->put('/demo/installflow/oferty/19',$this->offerData())->assertSessionHasErrors('offer');
        $this->put('/demo/installflow/zlecenia/25',['title'=>'Zlecenie z oferty','client_id'=>2,'gross'=>'1','status'=>'completed'])->assertRedirect();
        $order=$this->db()->table('orders')->find(25);
        $this->assertSame(1,$order->client_id); $this->assertSame(26447,$order->gross_cents); $this->assertNotNull($order->completed_at);
        $this->assertSame(2,$this->db()->table('order_events')->where('order_id',25)->count());
    }
    public function test_calendar_reschedule_and_reports_are_based_on_saved_data(): void
    {
        $this->open(); $before=(new Repository)->metrics();
        $date=now()->addDays(3)->setTime(10,30);
        $this->post('/demo/installflow/kalendarz',['order_id'=>1,'employee_id'=>3,'scheduled_at'=>$date->format('Y-m-d\TH:i')])->assertRedirect();
        $order=$this->db()->table('orders')->find(1);
        $this->assertSame('scheduled',$order->status); $this->assertSame(3,$order->employee_id);
        $this->get('/demo/installflow/kalendarz?month='.$date->format('Y-m'))->assertSee('10:30')->assertSee('Montaż klimatyzacji split');
        $this->put('/demo/installflow/zlecenia/1',['title'=>$order->title,'client_id'=>$order->client_id,'gross'=>'1','status'=>'completed','employee_id'=>3,'scheduled_at'=>$date->toDateTimeString()])->assertRedirect();
        $after=(new Repository)->metrics();
        $this->assertSame($before['completed']+1,$after['completed']);
        $this->assertSame($before['revenue']+$order->gross_cents,$after['revenue']);
        $this->assertSame($after['revenue'],$after['team']->sum('value'));
        $this->post('/demo/installflow/kalendarz',['order_id'=>1,'employee_id'=>2,'scheduled_at'=>$date->toDateTimeString()])->assertSessionHasErrors('schedule');
        $this->get('/demo/installflow/raporty')->assertSee(Money::format($after['revenue']));
        $this->get('/demo/installflow/kalendarz?month=wrong')->assertSessionHasErrors('month');
    }
    public function test_employee_crud_changes_availability_without_creating_real_accounts(): void
    {
        $this->open(); $users=User::count();
        $data=['name'=>'Pracownik demonstracyjny','role'=>'Monter','email'=>'pracownik@test.example','phone'=>'+48 000 000 000','availability'=>'available'];
        $this->post('/demo/installflow/pracownicy',$data)->assertRedirect('/demo/installflow/pracownicy/7');
        $this->put('/demo/installflow/pracownicy/7',array_replace($data,['availability'=>'busy']))->assertRedirect();
        $this->get('/demo/installflow/pracownicy/7')->assertOk()->assertSee('Zajęty');
        $this->assertSame($users,User::count());
    }
    public function test_direct_order_creation_validation_edit_and_authenticated_reset(): void
    {
        $user=User::factory()->create(); $this->actingAs($user); $this->open();
        $data=['title'=>'Montaż demonstracyjny','client_id'=>2,'employee_id'=>1,'status'=>'scheduled','scheduled_at'=>now()->addDays(2)->format('Y-m-d\TH:i'),'gross'=>'1234.56','notes'=>'Notatka demonstracyjna'];
        $this->post('/demo/installflow/zlecenia',array_replace($data,['status'=>'invalid']))->assertSessionHasErrors('status');
        $this->post('/demo/installflow/zlecenia',array_replace($data,['scheduled_at'=>null]))->assertSessionHasErrors('scheduled_at');
        $this->post('/demo/installflow/zlecenia',$data)->assertRedirect('/demo/installflow/zlecenia/25');
        $this->assertSame(123456,$this->db()->table('orders')->find(25)->gross_cents);
        $this->put('/demo/installflow/zlecenia/25',array_replace($data,['gross'=>'1500','status'=>'cancelled','employee_id'=>2]))->assertRedirect();
        $order=$this->db()->table('orders')->find(25);
        $this->assertSame('cancelled',$order->status); $this->assertSame(150000,$order->gross_cents);
        $this->get('/demo/installflow/zlecenia?status=cancelled&q=Monta%C5%BC%20demonstracyjny')->assertSee('Montaż demonstracyjny');
        $this->assertSame(2,$this->db()->table('order_events')->where('order_id',25)->count());
        $this->post('/demo/installflow/reset')->assertRedirect();
        $this->assertAuthenticatedAs($user);
        $this->get('/demo/installflow/zlecenia/25')->assertNotFound();
    }
    public function test_visitors_cannot_read_or_modify_another_sessions_records_and_reset_is_local(): void
    {
        $a=str_repeat('a',64); $b=str_repeat('b',64);
        $this->withSession(['installflow.token'=>$a,'installflow.started'=>now()->timestamp])->get('/demo/installflow')->assertOk();
        $this->post('/demo/installflow/klienci',$this->clientData(['name'=>'Tylko w sesji A']))->assertRedirect();
        $this->withSession(['installflow.token'=>$b,'installflow.started'=>now()->timestamp])->get('/demo/installflow/klienci?workspace='.$a)->assertOk()->assertDontSee('Tylko w sesji A');
        $this->get('/demo/installflow/klienci/31')->assertNotFound();
        $this->put('/demo/installflow/klienci/31',$this->clientData())->assertNotFound();
        $this->assertSame(30,$this->db()->table('clients')->count());
        $this->withSession(['installflow.token'=>$a,'installflow.started'=>now()->timestamp])->get('/demo/installflow/klienci/31')->assertOk()->assertSee('Tylko w sesji A');
        $this->post('/demo/installflow/reset')->assertRedirect();
        $this->get('/demo/installflow/klienci')->assertDontSee('Tylko w sesji A');
        $this->assertSame(30,$this->db()->table('clients')->count());
        $this->assertFileExists(Workspace::path($a)); $this->assertFileExists(Workspace::path($b));
    }
    public function test_expired_session_gets_fresh_data_and_prune_removes_only_old_files(): void
    {
        $a=str_repeat('c',64);
        $this->withSession(['installflow.token'=>$a,'installflow.started'=>now()->timestamp])->get('/demo/installflow')->assertOk();
        $this->post('/demo/installflow/klienci',$this->clientData())->assertRedirect();
        $this->withSession(['installflow.started'=>now()->subHours(3)->timestamp])->get('/demo/installflow/klienci/31')->assertNotFound();
        $this->assertSame(30,$this->db()->table('clients')->count());
        touch(Workspace::path($a), now()->subHours(4)->timestamp);
        $this->artisan('demo:installflow --prune')->assertSuccessful();
        $this->assertFileDoesNotExist(Workspace::path($a));
    }
    public function test_production_requires_both_opt_ins_and_only_marketing_can_enter_sitemap(): void
    {
        $this->app['env']='production'; config(['site.indexable'=>true]);
        $this->get('/demo')->assertNotFound(); $this->get('/demo/installflow')->assertNotFound();
        config(['demo.allow_public'=>true]);
        $this->get('/demo')->assertOk()->assertSee('noindex,nofollow');
        $this->get('/sitemap.xml')->assertDontSee('/demo');
        config(['demo.index_presentations'=>true]);
        $this->get('/sitemap.xml')->assertSee('/demo</loc>',false)->assertSee('/demo/installflow/opis')->assertDontSee('/demo/installflow/klienci');
        $this->get('/demo')->assertSee('index,follow,max-image-preview:large');
        $this->app['env']='testing';
        $this->get('/demo/installflow')->assertOk()->assertHeader('X-Robots-Tag','noindex, nofollow');
        config(['demo.enabled'=>false]); $this->get('/demo')->assertNotFound();
        $this->get('/')->assertOk()->assertDontSee('Demo aplikacji');
    }
    public function test_real_http_csrf_protection_rejects_unverified_writes(): void
    {
        $this->app['env']='local';
        $this->get('/demo/installflow')->assertOk();
        $this->post('/demo/installflow/klienci',$this->clientData())->assertStatus(419);
        $this->assertSame(30,$this->db()->table('clients')->count());
    }
}
