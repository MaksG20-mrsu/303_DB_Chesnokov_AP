#!/bin/bash
chcp 65001

echo "1. Найти все пары пользователей, оценивших один и тот же фильм. Устранить дубликаты, проверить отсутствие пар с самим собой. Для каждой пары должны быть указаны имена пользователей и название фильма, который они оценили. В списке оставить первые 100 записей."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "
SELECT DISTINCT 
    u1.name AS user1_name,
    u2.name AS user2_name, 
    m.title AS movie_title
FROM ratings r1
JOIN ratings r2 ON r1.movie_id = r2.movie_id AND r1.user_id < r2.user_id
JOIN users u1 ON r1.user_id = u1.id
JOIN users u2 ON r2.user_id = u2.id
JOIN movies m ON r1.movie_id = m.movieId
ORDER BY u1.name, u2.name, m.title
LIMIT 100;"
echo " "

echo "2. Найти 10 самых старых оценок от разных пользователей, вывести названия фильмов, имена пользователей, оценку, дату отзыва в формате ГГГГ-ММ-ДД."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "
SELECT DISTINCT
    m.title AS movie_title,
    u.name AS user_name,
    r.rating,
    date(r.timestamp, 'unixepoch') AS rating_date
FROM ratings r
JOIN users u ON r.user_id = u.id
JOIN movies m ON r.movie_id = m.movieId
ORDER BY r.timestamp ASC
LIMIT 10;"
echo " "

echo "3. Вывести в одном списке все фильмы с максимальным средним рейтингом и все фильмы с минимальным средним рейтингом. Общий список отсортировать по году выпуска и названию фильма. В зависимости от рейтинга в колонке 'Рекомендуем' для фильмов должно быть написано 'Да' или 'Нет'."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "
WITH movie_ratings AS (
    SELECT 
        m.movieId,
        m.title,
        CAST(substr(m.title, -5, 4) AS INTEGER) AS release_year,
        AVG(r.rating) AS avg_rating
    FROM movies m
    JOIN ratings r ON m.movieId = r.movie_id
    GROUP BY m.movieId, m.title
),
min_max_ratings AS (
    SELECT 
        MIN(avg_rating) AS min_rating,
        MAX(avg_rating) AS max_rating
    FROM movie_ratings
)
SELECT 
    mr.title,
    mr.release_year,
    mr.avg_rating,
    CASE 
        WHEN mr.avg_rating = (SELECT max_rating FROM min_max_ratings) THEN 'Да'
        ELSE 'Нет'
    END AS Рекомендуем
FROM movie_ratings mr
WHERE mr.avg_rating = (SELECT min_rating FROM min_max_ratings)
   OR mr.avg_rating = (SELECT max_rating FROM min_max_ratings)
ORDER BY mr.release_year, mr.title;"
echo " "

echo "4. Вычислить количество оценок и среднюю оценку, которую дали фильмам пользователи-мужчины в период с 2011 по 2014 год."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "
SELECT 
    COUNT(*) AS ratings_count,
    ROUND(AVG(r.rating), 2) AS avg_rating
FROM ratings r
JOIN users u ON r.user_id = u.id
WHERE u.gender = 'M'
  AND strftime('%Y', datetime(r.timestamp, 'unixepoch')) BETWEEN '2011' AND '2014';"
echo " "

echo "5. Составить список фильмов с указанием средней оценки и количества пользователей, которые их оценили. Полученный список отсортировать по году выпуска и названиям фильмов. В списке оставить первые 20 записей."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "
SELECT 
    m.title,
    CAST(substr(m.title, -5, 4) AS INTEGER) AS release_year,
    ROUND(AVG(r.rating), 2) AS avg_rating,
    COUNT(DISTINCT r.user_id) AS users_count
FROM movies m
JOIN ratings r ON m.movieId = r.movie_id
GROUP BY m.movieId, m.title
ORDER BY release_year DESC, m.title
LIMIT 20;"
echo " "

echo "6. Определить самый распространенный жанр фильма и количество фильмов в этом жанре. Отдельную таблицу для жанров не использовать, жанры нужно извлекать из таблицы movies."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "
WITH genre_counts AS (
    WITH RECURSIVE split(genre, rest) AS (
        SELECT 
            substr(genres, 1, instr(genres || '|', '|') - 1),
            substr(genres, instr(genres || '|', '|') + 1)
        FROM movies
        WHERE genres IS NOT NULL AND genres != ''
        UNION ALL
        SELECT 
            substr(rest, 1, instr(rest || '|', '|') - 1),
            substr(rest, instr(rest || '|', '|') + 1)
        FROM split
        WHERE rest != ''
    )
    SELECT 
        genre,
        COUNT(*) AS genre_count
    FROM split
    WHERE genre != '' AND genre IS NOT NULL
    GROUP BY genre
    ORDER BY genre_count DESC
)
SELECT genre, genre_count
FROM genre_counts
ORDER BY genre_count DESC
LIMIT 1;"
echo " "

echo "7. Вывести список из 10 последних зарегистрированных пользователей в формате 'Фамилия Имя|Дата регистрации' (сначала фамилия, потом имя)."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "
SELECT 
    substr(name, instr(name, ' ') + 1) || ' ' || substr(name, 1, instr(name, ' ') - 1) AS full_name,
    register_date
FROM users
ORDER BY register_date DESC
LIMIT 10;"
echo " "

echo "8. С помощью рекурсивного CTE определить, на какие дни недели приходился ваш день рождения в каждом году."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "
WITH RECURSIVE birthday_years(year_num) AS (
    VALUES(2000)
    UNION ALL
    SELECT year_num + 1
    FROM birthday_years
    WHERE year_num < 2024
)
SELECT 
    year_num AS год,
    CASE strftime('%w', year_num || '-11-15')
        WHEN '0' THEN 'Воскресенье'
        WHEN '1' THEN 'Понедельник'
        WHEN '2' THEN 'Вторник'
        WHEN '3' THEN 'Среда'
        WHEN '4' THEN 'Четверг'
        WHEN '5' THEN 'Пятница'
        WHEN '6' THEN 'Суббота'
    END AS день_недели
FROM birthday_years
ORDER BY year_num;"
echo " "