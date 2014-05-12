DROP TABLE IF EXISTS resource_queue;
DROP TABLE IF EXISTS resource_in_progress;
DROP TABLE IF EXISTS resource_failed;
DROP TABLE IF EXISTS resource_completed;
DROP TABLE IF EXISTS resource;

CREATE TABLE resource (
  hash VARCHAR(32) PRIMARY KEY,
  data CLOB NOT NULL
);

CREATE TABLE resource_queue (
  hash VARCHAR(32),
  position INTEGER PRIMARY KEY,
  FOREIGN KEY(hash) REFERENCES resource(hash) ON DELETE CASCADE
);

CREATE UNIQUE INDEX uq_resource_queue_hash
ON resource_queue (hash);

CREATE TABLE resource_in_progress (
  hash VARCHAR(32) NOT NULL,
  start_date TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(hash) REFERENCES resource(hash) ON DELETE CASCADE
);

CREATE UNIQUE INDEX uq_resource_in_progress_hash
ON resource_queue (hash);

CREATE TABLE resource_completed (
  hash VARCHAR(32) NOT NULL,
  FOREIGN KEY(hash) REFERENCES resource(hash) ON DELETE CASCADE
);

CREATE UNIQUE INDEX uq_resource_completed_hash
ON resource_queue (hash);

CREATE TABLE resource_failed (
  hash VARCHAR(32) NOT NULL,
  reason VARCHAR(64) NOT NULL,
  FOREIGN KEY(hash) REFERENCES resource(hash) ON DELETE CASCADE
);

CREATE UNIQUE INDEX uq_resource_failed_hash
ON resource_queue (hash);
