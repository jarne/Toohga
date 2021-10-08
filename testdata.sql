DELETE FROM urls;
DELETE FROM users;

INSERT INTO users (id, upin, displayName) VALUES (1, 'OjN5Df', 'John');
INSERT INTO users (id, upin, displayName) VALUES (2, 'qiVo5a', 'Robert');
INSERT INTO users (id, upin, displayName) VALUES (3, 'rTUx', 'Michael');
INSERT INTO users (id, upin, displayName) VALUES (4, '9UxOmi8u5e4U', 'William');

INSERT INTO urls (id, created, client, target, userId) VALUES (0, '2021-10-03 10:10:37', '123.123.123.123', 'https://github.com/jarne/Toohga', 1);
INSERT INTO urls (id, created, client, target, userId) VALUES (1, '2021-07-03 10:10:37', '192.168.1.17', 'https://developer.mozilla.org/en-US/docs/Learn/Common_questions/What_is_a_URL', 4);
INSERT INTO urls (id, created, client, target, userId) VALUES (2, '2021-06-04 10:10:37', '10.0.0.115', 'https://www.iana.org/domains/example', 2);
INSERT INTO urls (id, created, client, target, userId) VALUES (3, '2018-11-25 06:10:37', '123.123.123.123', 'https://github.com', null);
INSERT INTO urls (id, created, client, target, userId) VALUES (4, '2021-05-04 14:10:37', '172.16.5.129', 'https://stackoverflow.com/questions', null);
