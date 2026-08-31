<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sprint 1B — Make asset_id nullable in borrowings table.
 *
 * WHY raw DB::statement() instead of Schema Builder / ->change():
 *   MariaDB 10.4 raises error 1833 ("Cannot change column used in a generated
 *   column expression") when altering a column that is referenced by a STORED
 *   generated column. No Laravel helper (including ->change()) can bypass this
 *   engine-level DDL restriction. doctrine/dbal is therefore NOT required.
 *
 * WHY active_asset_id must be dropped and recreated:
 *   active_asset_id is a STORED GENERATED column whose expression reads asset_id
 *   directly: CASE WHEN status IN (...) THEN `asset_id` ELSE NULL END.
 *   The STORED type means MySQL/MariaDB persists the computed value to disk and
 *   indexes it (UNIQUE KEY). We must drop it before altering asset_id, then
 *   recreate it with an identical expression and constraint so the business rule
 *   ("one asset cannot be actively borrowed twice") is fully preserved.
 *
 * EXPRESSION used (taken verbatim from SHOW CREATE TABLE output):
 *   CASE WHEN `status` IN ('approved','borrowed','return_pending_verification')
 *        THEN `asset_id` ELSE NULL END
 */
return new class extends Migration
{
    /**
     * Make asset_id nullable.
     *
     * Step order is mandatory:
     *   1. Drop UNIQUE index that covers active_asset_id (required before DROP COLUMN).
     *   2. Drop the generated column active_asset_id.
     *   3. Alter asset_id to allow NULL.
     *   4. Recreate active_asset_id with the identical STORED expression.
     *   5. Restore the UNIQUE constraint on active_asset_id.
     */
    public function up(): void
    {
        // 1. Drop UNIQUE index (MariaDB requires index removal before column drop
        //    when the column is the sole member of a unique key).
        DB::statement('ALTER TABLE `borrowings` DROP INDEX `borrowings_active_asset_id_unique`');

        // 2. Drop the STORED generated column that references asset_id.
        DB::statement('ALTER TABLE `borrowings` DROP COLUMN `active_asset_id`');

        // 3. Make asset_id nullable so Item-based borrowings (asset_id = null) succeed.
        DB::statement('ALTER TABLE `borrowings` MODIFY COLUMN `asset_id` BIGINT UNSIGNED NULL');

        // 4. Recreate active_asset_id with the original expression — business rule intact.
        DB::statement(
            "ALTER TABLE `borrowings`
             ADD COLUMN `active_asset_id` BIGINT UNSIGNED
             GENERATED ALWAYS AS (
                 CASE WHEN `status` IN ('approved', 'borrowed', 'return_pending_verification')
                      THEN `asset_id` ELSE NULL END
             ) STORED"
        );

        // 5. Restore the UNIQUE constraint (prevents double-active borrowing of one asset).
        DB::statement('ALTER TABLE `borrowings` ADD UNIQUE KEY `borrowings_active_asset_id_unique` (`active_asset_id`)');
    }

    /**
     * Reverse to the original state: asset_id NOT NULL.
     *
     * This rollback is only safe if NO borrowing row has asset_id = NULL.
     * If Item-based borrowings already exist, this method will throw a
     * RuntimeException with a clear, actionable message before touching the schema.
     *
     * Rollback sequence mirrors up() with NOT NULL restored at step 3.
     */
    public function down(): void
    {
        // Guard: refuse rollback if any Item-based borrowing (asset_id = NULL) exists.
        $nullCount = DB::table('borrowings')->whereNull('asset_id')->count();

        if ($nullCount > 0) {
            throw new \RuntimeException(
                "Cannot roll back migration: {$nullCount} borrowing row(s) have asset_id = NULL. " .
                "Rolling back would violate the NOT NULL constraint and corrupt existing Item-based borrowing data. " .
                "Delete or reassign those rows before attempting rollback."
            );
        }

        // 1. Drop UNIQUE index.
        DB::statement('ALTER TABLE `borrowings` DROP INDEX `borrowings_active_asset_id_unique`');

        // 2. Drop the generated column.
        DB::statement('ALTER TABLE `borrowings` DROP COLUMN `active_asset_id`');

        // 3. Restore asset_id as NOT NULL — safe because guard above confirmed no NULLs exist.
        DB::statement('ALTER TABLE `borrowings` MODIFY COLUMN `asset_id` BIGINT UNSIGNED NOT NULL');

        // 4. Recreate generated column with identical expression.
        DB::statement(
            "ALTER TABLE `borrowings`
             ADD COLUMN `active_asset_id` BIGINT UNSIGNED
             GENERATED ALWAYS AS (
                 CASE WHEN `status` IN ('approved', 'borrowed', 'return_pending_verification')
                      THEN `asset_id` ELSE NULL END
             ) STORED"
        );

        // 5. Restore the UNIQUE constraint.
        DB::statement('ALTER TABLE `borrowings` ADD UNIQUE KEY `borrowings_active_asset_id_unique` (`active_asset_id`)');
    }
};
