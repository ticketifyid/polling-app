<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Setting;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteFlowTest extends TestCase
{
    use RefreshDatabase;

    private function candidate(): Candidate
    {
        return Candidate::create([
            'nama'      => 'Kandidat A',
            'foto'      => 'kandidat/a.jpg',
            'urutan'    => 1,
            'is_active' => true,
        ]);
    }

    public function test_form_shown_when_polling_open(): void
    {
        Setting::set('polling_dibuka', '1');
        $this->candidate();

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Kandidat A');
    }

    public function test_closed_message_when_polling_closed(): void
    {
        Setting::set('polling_dibuka', '0');

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Voting Telah Ditutup');
    }

    public function test_vote_stored(): void
    {
        Setting::set('polling_dibuka', '1');
        $candidate = $this->candidate();

        $this->post('/vote', [
            'nama'         => 'Budi',
            'candidate_id' => $candidate->id,
        ])->assertRedirect(route('vote.success'));

        $this->assertDatabaseHas('votes', [
            'nama'         => 'Budi',
            'candidate_id' => $candidate->id,
        ]);
    }

    public function test_nama_is_required(): void
    {
        Setting::set('polling_dibuka', '1');
        $candidate = $this->candidate();

        $this->post('/vote', ['candidate_id' => $candidate->id])
            ->assertSessionHasErrors('nama');

        $this->assertSame(0, Vote::count());
    }

    public function test_admin_area_requires_login(): void
    {
        $this->get('/admin/kandidat')->assertRedirect(route('admin.login'));
    }

    public function test_result_page_requires_login(): void
    {
        $this->get(route('admin.result.index'))->assertRedirect(route('admin.login'));
    }

    public function test_result_page_visible_to_logged_in_admin(): void
    {
        $candidate = $this->candidate();
        Vote::create(['nama' => 'Budi', 'candidate_id' => $candidate->id]);

        $this->withSession(['admin' => true])
            ->get(route('admin.result.index'))
            ->assertStatus(200)
            ->assertSee('Kandidat A');
    }
}
