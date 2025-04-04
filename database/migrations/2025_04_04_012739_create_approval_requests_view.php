<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateApprovalRequestsView extends Migration
{
    public function up()
    {
        DB::statement("
            CREATE VIEW approval_requests_view AS
            SELECT
                id,
                user_id,
                main_status,
                status,
                created_at,
                updated_at
            FROM recurring_orders
            WHERE main_status = 'waiting_for_approve'

            UNION ALL

            SELECT
                id,
                user_id,
                main_status,
                status,
                created_at,
                updated_at
            FROM orders
            WHERE main_status = 'waiting_for_approve'
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS approval_requests_view");
    }
}
