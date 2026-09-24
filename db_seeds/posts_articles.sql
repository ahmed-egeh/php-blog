START TRANSACTION;

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'Why Mars still owns our imagination',
'Mars is close enough to photograph in detail and far enough to stay mysterious. Dust storms can wrap the planet for months, ice caps shrink and grow, and dry riverbeds hint at a wetter past. That mix of evidence and unanswered questions is why it keeps showing up in science and in stories.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Planets'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Why Mars still owns our imagination');

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'A short walk through the solar system',
'Eight planets, countless rocks, and a thin haze of dust all orbit the same star. Inner worlds are rocky and scorched or frozen. Outer worlds are gas and ice giants with rings and crowded moon systems. Scale is the hard part: if Earth is a peppercorn, the Sun is a beach ball several metres away.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Solar System'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'A short walk through the solar system');

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'What a star actually is',
'A star is a balancing act: gravity pulling in, fusion pushing out. Hydrogen becomes helium in the core, light leaks out over thousands of years, and the surface we photograph is only the last layer. When the fuel runs low, mass decides the ending: a quiet white dwarf, a supernova, or a black hole.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Stars'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'What a star actually is');

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'The Milky Way is not a quiet neighbourhood',
'From Earth the galaxy looks like a pale band. Inside it, stars form in clouds, older stars drift in the halo, and a supermassive black hole sits in the centre. We live on an outer arm, which is why the winter sky in the northern hemisphere is packed with bright landmarks.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Galaxies'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'The Milky Way is not a quiet neighbourhood');

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'Black holes without the movie dialogue',
'A black hole is a region where gravity is steep enough that light cannot climb out. The event horizon is not a physical surface. Matter that falls in heats up and can shine fiercely before it disappears from view. We infer them from orbits, gravitational waves, and the shadow imaged in nearby galaxies.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Black Holes'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Black holes without the movie dialogue');

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'How we leave Earth at all',
'Rockets work because they throw mass downward very fast. Staging drops empty tanks so the next burn is lighter. Getting to orbit is the expensive step; travelling onward is mostly patience, gravity assists, and careful navigation. Every kilogram sent to space is a negotiation with fuel.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Space Exploration'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'How we leave Earth at all');

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'The sky is a time machine',
'Light has a speed limit, so every telescope looks into the past. The Sun is eight minutes old. Nearby stars are years old. Distant galaxies are snapshots from when the universe was younger and smaller. Astronomy is history written in photons.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Astronomy'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'The sky is a time machine');

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'Worlds around other suns',
'Exoplanets are found by tiny dips in starlight or by a star wobbling under a planet''s pull. Most are nothing like Earth: hot Jupiters, lava worlds, ice giants. A few sit in a temperate zone where liquid water could exist. That is a starting condition for life, not proof of it.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Exoplanets'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Worlds around other suns');

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'The universe is still expanding',
'Space between galaxies stretches, so distant objects recede faster. That is not an explosion in a room; the room itself is growing. Cosmic microwave background light is leftover heat from an early, dense state. Dark energy is the name we give to whatever is speeding the expansion up.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Cosmology'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'The universe is still expanding');

INSERT INTO posts (user_id, title, content, active, category_id)
SELECT u.id, 'Where else might life hide',
'Life as we know it wants liquid water, chemistry, and time. Mars had rivers. Europa and Enceladus likely have oceans under ice. Titan has lakes of methane. None of that is a biosphere yet, but it is a short list of places worth sampling carefully instead of guessing from Earth.',
1, c.id
FROM (SELECT id FROM users ORDER BY id LIMIT 1) u
JOIN categories c ON c.name = 'Astrobiology'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Where else might life hide');

COMMIT;
