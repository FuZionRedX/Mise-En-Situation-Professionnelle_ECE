-- Minecraft Block Generator — SQLite schema
-- Generated 2026-06-06
-- Usage: sqlite3 database.sqlite < schema.sql

PRAGMA journal_mode = WAL;
PRAGMA foreign_keys = ON;

-- ──────────────────────────────────────────────
--  Laravel internals
-- ──────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS "migrations" (
    "id"        INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "migration" VARCHAR NOT NULL,
    "batch"     INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS "cache" (
    "key"        VARCHAR NOT NULL,
    "value"      TEXT NOT NULL,
    "expiration" INTEGER NOT NULL,
    PRIMARY KEY ("key")
);

CREATE TABLE IF NOT EXISTS "cache_locks" (
    "key"        VARCHAR NOT NULL,
    "owner"      VARCHAR NOT NULL,
    "expiration" INTEGER NOT NULL,
    PRIMARY KEY ("key")
);

CREATE TABLE IF NOT EXISTS "jobs" (
    "id"           INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "queue"        VARCHAR NOT NULL,
    "payload"      TEXT NOT NULL,
    "attempts"     INTEGER NOT NULL,
    "reserved_at"  INTEGER,
    "available_at" INTEGER NOT NULL,
    "created_at"   INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS "jobs_queue_index" ON "jobs" ("queue");

CREATE TABLE IF NOT EXISTS "job_batches" (
    "id"             VARCHAR NOT NULL,
    "name"           VARCHAR NOT NULL,
    "total_jobs"     INTEGER NOT NULL,
    "pending_jobs"   INTEGER NOT NULL,
    "failed_jobs"    INTEGER NOT NULL,
    "failed_job_ids" TEXT NOT NULL,
    "options"        TEXT,
    "cancelled_at"   INTEGER,
    "created_at"     INTEGER NOT NULL,
    "finished_at"    INTEGER,
    PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "failed_jobs" (
    "id"         INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "uuid"       VARCHAR NOT NULL UNIQUE,
    "connection" TEXT NOT NULL,
    "queue"      TEXT NOT NULL,
    "payload"    TEXT NOT NULL,
    "exception"  TEXT NOT NULL,
    "failed_at"  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS "users" (
    "id"                INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "identifier"        VARCHAR NOT NULL UNIQUE,
    "name"              VARCHAR NOT NULL,
    "email"             VARCHAR NOT NULL UNIQUE,
    "email_verified_at" DATETIME,
    "password"          VARCHAR NOT NULL,
    "role"              VARCHAR CHECK("role" IN ('user','admin')) NOT NULL DEFAULT 'user',
    "remember_token"    VARCHAR,
    "created_at"        DATETIME,
    "updated_at"        DATETIME
);

CREATE TABLE IF NOT EXISTS "password_reset_tokens" (
    "email"      VARCHAR NOT NULL,
    "token"      VARCHAR NOT NULL,
    "created_at" DATETIME,
    PRIMARY KEY ("email")
);

CREATE TABLE IF NOT EXISTS "sessions" (
    "id"            VARCHAR NOT NULL,
    "user_id"       INTEGER,
    "ip_address"    VARCHAR,
    "user_agent"    TEXT,
    "payload"       TEXT NOT NULL,
    "last_activity" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);
CREATE INDEX IF NOT EXISTS "sessions_user_id_index"       ON "sessions" ("user_id");
CREATE INDEX IF NOT EXISTS "sessions_last_activity_index" ON "sessions" ("last_activity");

-- ──────────────────────────────────────────────
--  Application tables
-- ──────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS "blocks" (
    "id"                 INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "name"               VARCHAR NOT NULL,
    "identifier"         VARCHAR NOT NULL,
    "creator_identifier" VARCHAR,
    "solid"              TINYINT(1) NOT NULL DEFAULT 1,
    "destructible"       TINYINT(1) NOT NULL DEFAULT 1,
    "resistance"         FLOAT NOT NULL,
    "light_emission"     TINYINT UNSIGNED NOT NULL DEFAULT 0,
    "texture_path"       VARCHAR NOT NULL,
    "geometry"           VARCHAR NOT NULL DEFAULT 'cube',
    "geometry_json_path" TEXT,
    "created_at"         DATETIME,
    "updated_at"         DATETIME
);

CREATE TABLE IF NOT EXISTS "mobs" (
    "id"                   INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "name"                 VARCHAR NOT NULL,
    "identifier"           VARCHAR NOT NULL UNIQUE,
    "creator_identifier"   VARCHAR,
    "health"               INTEGER NOT NULL DEFAULT 20,
    "speed"                FLOAT NOT NULL DEFAULT 0.25,
    "behavior_type"        VARCHAR CHECK("behavior_type" IN ('passive','neutral','hostile')) NOT NULL DEFAULT 'passive',
    "attack_damage"        INTEGER,
    "is_spawnable"         TINYINT(1) NOT NULL DEFAULT 1,
    "is_summonable"        TINYINT(1) NOT NULL DEFAULT 1,
    "collision_width"      FLOAT NOT NULL DEFAULT 0.6,
    "collision_height"     FLOAT NOT NULL DEFAULT 1.8,
    "scale"                FLOAT NOT NULL DEFAULT 1.0,
    "model_type"           VARCHAR CHECK("model_type" IN ('humanoid','quadruped','creeper')) NOT NULL DEFAULT 'humanoid',
    "texture_path"         VARCHAR NOT NULL,
    "geometry_json_path"   VARCHAR,
    "spawn_egg_primary"    VARCHAR NOT NULL DEFAULT '#a06040',
    "spawn_egg_secondary"  VARCHAR NOT NULL DEFAULT '#ffffff',
    "created_at"           DATETIME,
    "updated_at"           DATETIME
);
