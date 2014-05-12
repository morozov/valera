DROP TABLE IF EXISTS resource_queue;
DROP TABLE IF EXISTS resource_in_progress;
DROP TABLE IF EXISTS resource_failed;
DROP TABLE IF EXISTS resource_completed;
DROP TABLE IF EXISTS resource;

CREATE TABLE resource (
  hash VARCHAR(32) NOT NULL,
  data TEXT,
  PRIMARY KEY (hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE resource_queue (
  hash VARCHAR(32) NOT NULL,
  position INT UNSIGNED AUTO_INCREMENT NOT NULL,
  PRIMARY KEY (hash),
  UNIQUE KEY uq_resource_queue_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE resource_in_progress (
  hash VARCHAR(32) NOT NULL,
  start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE resource_completed (
  hash VARCHAR(32) NOT NULL,
  PRIMARY KEY (hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE resource_failed (
  hash VARCHAR(32) NOT NULL,
  reason VARCHAR(64) NOT NULL,
  PRIMARY KEY (hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE resource_queue
  ADD CONSTRAINT fk_resource_queue_hash
  FOREIGN KEY (hash)
  REFERENCES resource (hash) ON DELETE CASCADE;

ALTER TABLE resource_in_progress
  ADD CONSTRAINT fk_resource_in_progress_hash
  FOREIGN KEY (hash)
  REFERENCES resource (hash) ON DELETE CASCADE;

ALTER TABLE resource_completed
  ADD CONSTRAINT fk_resource_completed_hash
  FOREIGN KEY (hash)
  REFERENCES resource (hash) ON DELETE CASCADE;

ALTER TABLE resource_failed
  ADD CONSTRAINT fk_resource_failed_hash
  FOREIGN KEY (hash)
  REFERENCES resource (hash) ON DELETE CASCADE;
