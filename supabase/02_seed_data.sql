-- Optional: copies the existing rows from your local MySQL database.
-- Run after 01_schema.sql. Skip this file if you want a fresh, empty database
-- (then create your admin by registering, and setting role = 'admin' in the table editor).

insert into public.users (user_id, username, email, password, role, created_at, is_active, profile_picture) values
(1, 'yanna', 's2400638@usls.edu.ph', '$2y$10$azKl87zmOZ1az3uNZljLJOcxlLvy5ybchVzCBVTZikg14Kuze9nOu', 'admin', '2026-11-05 06:05:02+00', 1, null),
(2, 'Test1', 's2400317@usls.edu.com', '$2y$10$U6xTygn2.QE0rpA65RT3DuMRVvBpM1M.aoxyxNtKZNrYXjQdfhSby', 'user', '2026-11-05 07:37:58+00', 1, 'uploads/profile_pictures/user_2_7510abf07a62ec26.png'),
(5, 'test2', '2402410@gmail.com', '$2y$10$OJbb.t18.bSxkiFptscGXeS/SKhgozCUZdW9VJjY3WTBlhfhTZiNa', 'user', '2026-11-20 11:05:45+00', 1, null);

insert into public.game_records (game_id, user_id, score, orbs_collected, fuel_remaining, game_status, played_at) values
(1, 2, 0, 0, 0, 'lost', '2026-11-06 09:32:08+00'),
(2, 2, 100, 10, 60, 'won', '2026-11-06 09:34:31+00'),
(3, 2, 100, 10, 74, 'won', '2026-11-06 10:12:36+00'),
(18, 2, 100, 10, 36, 'won', '2026-11-16 01:34:11+00'),
(19, 2, 80, 8, 0, 'lost', '2026-11-16 01:35:03+00'),
(20, 2, 0, 0, 0, 'lost', '2026-11-20 13:43:24+00');

-- Move the auto-increment counters past the imported ids
select setval(pg_get_serial_sequence('public.users', 'user_id'),        (select max(user_id) from public.users));
select setval(pg_get_serial_sequence('public.game_records', 'game_id'), (select max(game_id) from public.game_records));
