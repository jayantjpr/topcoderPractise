---------------------------------------------------------------------------
--
-- topcoder_database.sql :-
-- File which creates Schema for Topcoder Practice Website in Postgresql
--
---------------------------------------------------------------------------


---------------------------------------------------------------------------
--
-- Tables
--
---------------------------------------------------------------------------
CREATE TABLE registrants(
	idr INTEGER PRIMARY KEY,
	name VARCHAR(500) NOT NULL,
	handle VARCHAR(500) UNIQUE NOT NULL,
	password VARCHAR(500) NOT NULL
);

CREATE TABLE competitions(
	idc SERIAL PRIMARY KEY,
	name VARCHAR(500) NOT NULL,
	start_time TIMESTAMP NOT NULL,
	end_time TIMESTAMP NOT NULL,
	description VARCHAR(1000) NOT NULL,
	isevaluated BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE problems(
	idp INTEGER PRIMARY KEY,
	name VARCHAR(500) NOT NULL,
	room_id INTEGER NOT NULL,
	room_name VARCHAR(500) NOT NULL,
	difficulty_level INTEGER NOT NULL
);

CREATE TABLE competitions_problems(
	idc INTEGER NOT NULL,
	idp INTEGER NOT NULL,
	PRIMARY KEY(idc, idp),
	FOREIGN KEY (idc) REFERENCES competitions(idc) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (idp) REFERENCES problems(idp) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE submissions(
	idc INTEGER NOT NULL,
	idp INTEGER NOT NULL,
	idr INTEGER NOT NULL,
	score REAL NOT NULL DEFAULT 0,
	submission_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(idc, idp, idr),
	FOREIGN KEY (idc) REFERENCES competitions(idc) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (idp) REFERENCES problems(idp) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (idr) REFERENCES registrants(idr) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO registrants VALUES ('23101510', 'Jayant Gupta', 'jayantjpr', 'abc123');
INSERT INTO registrants VALUES ('22700151', 'Chinese Guy', 'pladene', 'abc123');
INSERT INTO competitions(name, start_time, end_time, description) VALUES  ('DP1', '2014-01-09 19:25:52.707311+05:30', '2014-01-09 20:25:52.707311+05:30', 'test');
INSERT INTO problems VALUES ('12075', 'PillarsDivTwo', '15289', 'SRM 547 DIV 2', '2');
INSERT INTO competitions_problems VALUES ('1','12075');
INSERT INTO submissions VALUES ('1', '12075', '22700151');


