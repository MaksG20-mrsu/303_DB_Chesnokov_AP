-- Adding five new users (myself and four group mates)
INSERT INTO users (name, email, gender_id, register_date, occupation_id)
VALUES 
    ('John Johnson', 'john.johnson@example.com', 
     (SELECT id FROM genders WHERE code = 'male'), 
     CURRENT_TIMESTAMP, 
     (SELECT id FROM occupations WHERE name = 'student')),
    
    ('Peter Peterson', 'peter.peterson@example.com',
     (SELECT id FROM genders WHERE code = 'male'),
     CURRENT_TIMESTAMP,
     (SELECT id FROM occupations WHERE name = 'student')),
    
    ('Mary Smith', 'mary.smith@example.com',
     (SELECT id FROM genders WHERE code = 'female'),
     CURRENT_TIMESTAMP,
     (SELECT id FROM occupations WHERE name = 'student')),
    
    ('Anna Brown', 'anna.brown@example.com',
     (SELECT id FROM genders WHERE code = 'female'),
     CURRENT_TIMESTAMP,
     (SELECT id FROM occupations WHERE name = 'student')),
    
    ('Alex Wilson', 'alex.wilson@example.com',
     (SELECT id FROM genders WHERE code = 'male'),
     CURRENT_TIMESTAMP,
     (SELECT id FROM occupations WHERE name = 'student'));

-- Adding new genres if they don't exist
INSERT OR IGNORE INTO genres (name) VALUES
    ('Sci-Fi'),
    ('Drama'),
    ('Crime'),
    ('Comedy'),
    ('Adventure');

-- Adding three new movies with different genres
INSERT INTO movies (title, year) VALUES
    ('Interstellar', 2014),
    ('Pulp Fiction', 1994),
    ('Forrest Gump', 1994);

-- Linking movies with genres
INSERT INTO movie_genres (movie_id, genre_id)
VALUES
    -- Interstellar - Sci-Fi, Drama, Adventure
    ((SELECT id FROM movies WHERE title = 'Interstellar' AND year = 2014),
     (SELECT id FROM genres WHERE name = 'Sci-Fi')),
    ((SELECT id FROM movies WHERE title = 'Interstellar' AND year = 2014),
     (SELECT id FROM genres WHERE name = 'Drama')),
    ((SELECT id FROM movies WHERE title = 'Interstellar' AND year = 2014),
     (SELECT id FROM genres WHERE name = 'Adventure')),
    
    -- Pulp Fiction - Crime, Drama
    ((SELECT id FROM movies WHERE title = 'Pulp Fiction' AND year = 1994),
     (SELECT id FROM genres WHERE name = 'Crime')),
    ((SELECT id FROM movies WHERE title = 'Pulp Fiction' AND year = 1994),
     (SELECT id FROM genres WHERE name = 'Drama')),
    
    -- Forrest Gump - Drama, Comedy
    ((SELECT id FROM movies WHERE title = 'Forrest Gump' AND year = 1994),
     (SELECT id FROM genres WHERE name = 'Drama')),
    ((SELECT id FROM movies WHERE title = 'Forrest Gump' AND year = 1994),
     (SELECT id FROM genres WHERE name = 'Comedy'));

-- Adding three ratings from John Johnson
INSERT INTO ratings (user_id, movie_id, rating, timestamp)
VALUES
    ((SELECT id FROM users WHERE email = 'john.johnson@example.com'),
     (SELECT id FROM movies WHERE title = 'Interstellar' AND year = 2014),
     5.0,
     CAST(strftime('%s', 'now') AS INTEGER)),
    
    ((SELECT id FROM users WHERE email = 'john.johnson@example.com'),
     (SELECT id FROM movies WHERE title = 'Pulp Fiction' AND year = 1994),
     4.5,
     CAST(strftime('%s', 'now') AS INTEGER)),
    
    ((SELECT id FROM users WHERE email = 'john.johnson@example.com'),
     (SELECT id FROM movies WHERE title = 'Forrest Gump' AND year = 1994),
     4.8,
     CAST(strftime('%s', 'now') AS INTEGER));

-- Adding tags for movies
INSERT INTO tags (user_id, movie_id, tag, timestamp)
VALUES
    ((SELECT id FROM users WHERE email = 'john.johnson@example.com'),
     (SELECT id FROM movies WHERE title = 'Interstellar' AND year = 2014),
     'mind-blowing',
     CAST(strftime('%s', 'now') AS INTEGER)),
    
    ((SELECT id FROM users WHERE email = 'john.johnson@example.com'),
     (SELECT id FROM movies WHERE title = 'Pulp Fiction' AND year = 1994),
     'cult classic',
     CAST(strftime('%s', 'now') AS INTEGER)),
    
    ((SELECT id FROM users WHERE email = 'john.johnson@example.com'),
     (SELECT id FROM movies WHERE title = 'Forrest Gump' AND year = 1994),
     'inspiring',
     CAST(strftime('%s', 'now') AS INTEGER));

