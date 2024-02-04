-- script invalide à cause de notes.owner qui référencie users.id... j'aurais essayé.

DELETE FROM note_shares;
DELETE FROM text_notes;
DELETE FROM checklist_note_items;
DELETE FROM checklist_notes;
DELETE FROM notes;
DELETE FROM users;


INSERT INTO users (mail, hashed_password, full_name, role)
VALUES
    ('rayan@example.com', 'pwd1', 'Rayan', 'user'),
    ('hani@example.com', 'pw2', 'Hani', 'user'),
    ('ayman@example.com', 'pw2', 'Ayman', 'user');


INSERT INTO notes (title, owner, created_at, edited_at, pinned, archived, weight)
VALUES
    ('Note 1', 1, NOW(), NOW(), 0, 0, 1),
    ('Note 2', 1, NOW(), NOW(), 1, 0, 2),
    ('Note 3', 2, NOW(), NOW(), 0, 1, 3);

INSERT INTO note_shares (note, user, editor)
VALUES
    (1, 1, 1), 
    (2, 2, 0), 
    (3, 1, 0);

INSERT INTO text_notes (id, content)
VALUES
    (1, 'Voici une text note'),
    (2, 'Ceci est une deuxième text note'),
    (3, 'Et de trois text note');

INSERT INTO checklist_notes (id)
VALUES
    (1),
    (2),
    (3);

INSERT INTO checklist_note_items (checklist_note, content, checked)
VALUES
    (1, 'Item 1 for Note 1', 0),
    (1, 'Item 2 for Note 1', 1),
    (2, 'Item 1 for Note 2', 0),
    (2, 'Item 2 for Note 2', 1),
    (3, 'Item 1 for Note 3', 1),
    (3, 'Item 2 for Note 3', 0);