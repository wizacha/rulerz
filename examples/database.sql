CREATE TABLE roles (
  id INTEGER PRIMARY KEY NOT NULL,
  name CLOB NOT NULL
);

CREATE TABLE groups (
  id INTEGER PRIMARY KEY NOT NULL,
  role_id INTEGER DEFAULT NULL,
  name CLOB NOT NULL,
  CONSTRAINT FK_F06D3970D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)
);

CREATE TABLE players(
  pseudo   VARCHAR(50) PRIMARY KEY NOT NULL,
  group_id INTEGER DEFAULT NULL,
  fullname VARCHAR(50) NOT NULL,
  gender   CHAR(1) NOT NULL,
  age      UNSIGNED TINYINT NOT NULL,
  points   UNSIGNED TINYINT NOT NULL,
  CONSTRAINT FK_264E43A6FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id)
);

CREATE INDEX IDX_264E43A6FE54D947 ON players (group_id);
CREATE INDEX IDX_F06D3970D60322AC ON groups (role_id);

INSERT INTO roles (id, name) VALUES
(1, 'ROLE_ADMIN'),
(2, 'ROLE_PLAYER')
;

INSERT INTO groups (id, role_id, name) VALUES
(1, 1, 'Admin'),
(2, 2, 'Players')
;

INSERT INTO players (pseudo, group_id, fullname, gender, age, points) VALUES
('Joe',      1, 'Joe la frite',      'M', 34,  2500),
('Bob',      2, 'Bob Morane',        'M', 62,  9001),
('Ada',      2, 'Ada Lovelace',      'F', 175, 10000),
('Kévin',    2, 'Yup, that is me.',  'M', 24,  100),
('Margaret', 2, 'Margaret Hamilton', 'F', 78,  5000),
('Alice',    1, 'Alice Foo',         'F', 30,  175)
;


select *
from players p
left join groups g on g.id = p.group_id
left join roles r on r.id = g.role_id;
