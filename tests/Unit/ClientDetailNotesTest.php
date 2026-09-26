<?php

namespace Tests\Unit;

use App\Support\ClientDetailNotes;
use Tests\TestCase;

class ClientDetailNotesTest extends TestCase
{
    public function test_page_size_is_capped_for_load_more(): void
    {
        $this->assertSame(25, ClientDetailNotes::PAGE_SIZE);
    }

    public function test_query_scopes_client_notes_tab_rows(): void
    {
        $query = ClientDetailNotes::queryForClient(42, 'client');

        $sql = strtolower($query->toSql());
        $bindings = $query->getBindings();

        $this->assertStringContainsString('client_id', $sql);
        $this->assertStringContainsString('assigned_to', $sql);
        $this->assertStringContainsString('task_group', $sql);
        $this->assertStringContainsString('type', $sql);
        $this->assertContains(42, $bindings);
        $this->assertContains('client', $bindings);
    }

    public function test_query_orders_pinned_notes_first(): void
    {
        $sql = strtolower(ClientDetailNotes::queryForClient(9, 'client')->toSql());

        $this->assertStringContainsString('order by', $sql);
        $this->assertStringContainsString('pin', $sql);
        $this->assertStringContainsString('created_at', $sql);
    }
}
