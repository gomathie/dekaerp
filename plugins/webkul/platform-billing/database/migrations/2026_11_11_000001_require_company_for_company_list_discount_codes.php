<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * A company-list discount code must name at least one company (user,
 * 2026-11-11). The service checked it, but nothing stopped another writer
 * from leaving a "company list" code with an empty list, which no company
 * could ever redeem.
 *
 * Deferred constraint triggers, checked at commit: the code row is inserted
 * before its company rows, so an immediate check would refuse every new code.
 * PostgreSQL only, like the immutability triggers in this plugin.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // migrate:fresh drops tables but leaves functions behind.
        $this->dropObjects();

        DB::unprepared(<<<'SQL'
CREATE FUNCTION platform_billing_discount_code_needs_company() RETURNS trigger AS $$
DECLARE
    target_id bigint;
BEGIN
    IF TG_TABLE_NAME = 'platform_billing_discount_codes' THEN
        target_id := NEW.id;
    ELSE
        target_id := OLD.discount_code_id;
    END IF;

    IF EXISTS (
        SELECT 1 FROM platform_billing_discount_codes c
        WHERE c.id = target_id
          AND c.eligibility_type = 'company_list'
          AND NOT EXISTS (
              SELECT 1 FROM platform_billing_discount_code_companies p
              WHERE p.discount_code_id = c.id
          )
    ) THEN
        RAISE EXCEPTION 'A company-list discount code must name at least one company.'
            USING ERRCODE = 'check_violation';
    END IF;

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE CONSTRAINT TRIGGER platform_billing_discount_code_needs_company_on_code
    AFTER INSERT OR UPDATE OF eligibility_type ON platform_billing_discount_codes
    DEFERRABLE INITIALLY DEFERRED
    FOR EACH ROW EXECUTE FUNCTION platform_billing_discount_code_needs_company();

CREATE CONSTRAINT TRIGGER platform_billing_discount_code_needs_company_on_list
    AFTER DELETE OR UPDATE OF discount_code_id ON platform_billing_discount_code_companies
    DEFERRABLE INITIALLY DEFERRED
    FOR EACH ROW EXECUTE FUNCTION platform_billing_discount_code_needs_company();
SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $this->dropObjects();
    }

    protected function dropObjects(): void
    {
        DB::unprepared(<<<'SQL'
DROP TRIGGER IF EXISTS platform_billing_discount_code_needs_company_on_code ON platform_billing_discount_codes;
DROP TRIGGER IF EXISTS platform_billing_discount_code_needs_company_on_list ON platform_billing_discount_code_companies;
DROP FUNCTION IF EXISTS platform_billing_discount_code_needs_company();
SQL);
    }
};
