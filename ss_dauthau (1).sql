-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th7 26, 2024 lúc 01:29 PM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ss_dauthau`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `path` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `size` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `enterprises`
--

CREATE TABLE `enterprises` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `representative_name` varchar(191) NOT NULL,
  `address` varchar(191) NOT NULL,
  `website` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `establish_date` date NOT NULL,
  `avg_document_rating` int(11) NOT NULL,
  `field_active_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_blacklist` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(8, '2014_10_12_000000_create_users_table', 1),
(9, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(10, '2019_08_19_000000_create_failed_jobs_table', 1),
(11, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(12, '2024_06_02_182903_create_staffs_table', 1),
(13, '2024_06_20_230842_create_systems_table', 1),
(14, '2024_07_01_041253_create_permission_tables', 1),
(15, '2024_07_14_082930_create_taxcode_column', 2),
(16, '2024_07_14_083505_change_taxcode_column', 3),
(17, '2024_07_20_080645_create_enterprises_table', 4),
(18, '2024_07_20_094754_create_attachments_table', 4),
(19, '2024_07_20_103010_create_notifications_table', 4),
(20, '2024_07_25_045819_add_column_user_table', 4),
(22, '2024_07_26_095904_change_role_id_collumn_staff_table', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(3, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 17),
(3, 'App\\Models\\User', 25),
(7, 'App\\Models\\User', 3),
(7, 'App\\Models\\User', 25),
(8, 'App\\Models\\User', 4),
(8, 'App\\Models\\User', 5),
(8, 'App\\Models\\User', 6),
(8, 'App\\Models\\User', 7),
(8, 'App\\Models\\User', 8),
(8, 'App\\Models\\User', 9);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `section` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'list_staff', 'staff', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(2, 'create_staff', 'staff', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(3, 'detail_staff', 'staff', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(4, 'update_staff', 'staff', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(5, 'destroy_staff', 'staff', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(6, 'list_enterpise', 'enterpise', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(7, 'create_enterpise', 'enterpise', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(8, 'detail_enterpise', 'enterpise', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(9, 'update_enterpise', 'enterpise', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(10, 'destroy_enterpise', 'enterpise', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(11, 'list_project', 'project', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(12, 'create_project', 'project', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(13, 'detail_project', 'project', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(14, 'update_project', 'project', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(15, 'destroy_project', 'project', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(16, 'list_package', 'package', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(17, 'create_package', 'package', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(18, 'detail_package', 'package', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(19, 'update_package', 'package', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(20, 'destroy_package', 'package', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(21, 'list_role', 'role', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(22, 'create_role', 'role', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(23, 'detail_role', 'role', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(24, 'update_role', 'role', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(25, 'destroy_role', 'role', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35'),
(26, 'list_system', 'system', 'api', '2024-07-20 18:40:34', '2024-07-20 18:40:38'),
(27, 'update_system', 'system', 'api', '2024-06-02 16:08:35', '2024-06-02 16:08:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(3, 'App\\Models\\User', 2, 'API Token', 'b3142a3e7e508f8507777f43526f6c9c39cfd8d55a1889efa4e171cf0a4fa518', '[\"*\"]', '2024-06-30 23:10:23', NULL, '2024-06-30 23:10:14', '2024-06-30 23:10:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(3, 'Admin', 'api', '2024-06-02 10:07:52', '2024-06-02 10:07:52'),
(7, 'Nhân viên nhập liệu', 'api', '2024-06-30 22:07:30', '2024-06-30 22:07:30'),
(8, 'Chuyên viên 1', 'api', '2024-07-25 08:57:33', '2024-07-25 08:57:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 3),
(1, 7),
(1, 8),
(2, 3),
(2, 7),
(2, 8),
(3, 3),
(3, 7),
(3, 8),
(4, 3),
(4, 7),
(4, 8),
(5, 3),
(5, 7),
(5, 8),
(6, 3),
(6, 7),
(6, 8),
(7, 3),
(7, 7),
(7, 8),
(8, 3),
(8, 7),
(8, 8),
(9, 3),
(9, 7),
(9, 8),
(10, 3),
(10, 7),
(11, 3),
(11, 7),
(12, 3),
(12, 7),
(13, 3),
(13, 7),
(14, 3),
(14, 7),
(15, 3),
(15, 7),
(16, 3),
(16, 7),
(17, 3),
(17, 7),
(18, 3),
(18, 7),
(19, 3),
(19, 7),
(20, 3),
(20, 7),
(21, 3),
(21, 7),
(22, 3),
(22, 7),
(23, 3),
(23, 7),
(24, 3),
(24, 7),
(25, 3),
(25, 7),
(26, 3),
(26, 7),
(27, 3),
(27, 7);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `staffs`
--

CREATE TABLE `staffs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`role_id`)),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `staffs`
--

INSERT INTO `staffs` (`id`, `user_id`, `role_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, 2, '3', NULL, '2024-06-30 21:58:01', '2024-06-30 22:04:57'),
(3, 3, '7', NULL, '2024-06-30 22:12:18', '2024-06-30 22:12:18'),
(4, 4, '8', NULL, '2024-07-25 08:57:57', '2024-07-25 08:57:57'),
(5, 5, '8', NULL, '2024-07-25 09:36:52', '2024-07-25 09:36:52'),
(6, 6, '8', NULL, '2024-07-25 09:38:07', '2024-07-25 09:38:07'),
(7, 7, '8', NULL, '2024-07-25 09:40:11', '2024-07-25 09:40:11'),
(8, 8, '8', NULL, '2024-07-25 09:44:48', '2024-07-25 09:44:48'),
(9, 9, '8', '2024-07-26 04:26:45', '2024-07-25 09:51:29', '2024-07-26 04:26:45'),
(19, 25, '\"[3,7]\"', NULL, '2024-07-26 03:18:06', '2024-07-26 04:15:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `systems`
--

CREATE TABLE `systems` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `systems`
--

INSERT INTO `systems` (`id`, `name`, `logo`, `phone`, `email`, `address`) VALUES
(1, 'BecomeDev', 'logo mới', '0338475943', 'lequyhieu1024@gmail.com', 'Lê Quý Hiếu, Liên Chung, Tân Yên, Bắc Giang');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `taxcode` bigint(20) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `account_ban_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `taxcode`, `phone`, `email`, `email_verified_at`, `account_ban_at`, `password`, `avatar`, `type`, `otp`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Admin', 1233456789, '0338475943', 'lequyhieu1024@gmail.com', '2024-07-01 04:59:30', NULL, '$2y$12$P6Vf8BeRG4QgSNSkTd7ZPey6d0PLvvgRNvGfvbEzGOX8XJNHm3q3q', 'uploads/images/66823758705f5.jpg', 'staff', NULL, NULL, '2024-06-30 21:58:01', '2024-06-30 21:58:01', NULL),
(3, 'Nhân viên nhập liệu', 2288338833, '0338475942', 'nvnl@gmail.com', NULL, NULL, '$2y$12$3WKxmSHyWOdtjWbIDjZIkuIPfiHN5SsF/NpZi6Bt3b6el4zWDKXx6', 'uploads/images/66823ab1f0105.jpg', 'staff', NULL, NULL, '2024-06-30 22:12:18', '2024-06-30 22:12:18', NULL),
(4, 'Lê QUý Hiếu', 123456789, '0338475941', 'lqh@gmail.com', NULL, NULL, '$2y$12$S9s/YprEiIwpsnbXDJXkYuUazKfX7vXK7zqmWWPbQ6xrk7wb2ks9m', NULL, 'staff', NULL, NULL, '2024-07-25 08:57:57', '2024-07-25 08:57:57', NULL),
(5, 'Lê Chuyên VIên 1', 123456781, '0338475911', 'lqh@gmail.com1', NULL, NULL, '$2y$12$LGN7p8CxF/NKSOBPoVke5e5gNDuEaICfBgGvS6WZwqKggZyf1Ka4y', NULL, 'staff', NULL, NULL, '2024-07-25 09:36:52', '2024-07-25 09:36:52', NULL),
(6, 'Lê Chuyên VIên 1', 123456799, '0338475900', '12lqh@gmail.com', NULL, NULL, '$2y$12$tMdzvTbGJLaKfg4GTgsDj.j0RzoXHiPy6GSPHQ/9m/KFvAcsHTD7e', 'uploads/images/66a27f6e8fd46.jpeg', 'staff', NULL, NULL, '2024-07-25 09:38:07', '2024-07-25 09:38:07', NULL),
(7, 'Lê Chuyên VIên 2', 123456792, '0338475901', '12l2qh@gmail.com', NULL, '2024-07-25 10:08:10', '$2y$12$lTIP7bmAfhqGwRDF1BlEy.MWkFdkM8mutrxLkJBkjw1/ZmnxczHLa', 'uploads/images/66a27feb5d2d1.jpeg', 'staff', NULL, NULL, '2024-07-25 09:40:11', '2024-07-25 10:08:10', NULL),
(8, 'Lê Chuyên VIên 3', 123456777, '0338475987', '12l22qh@gmail.com', NULL, '2024-07-03 16:51:58', '$2y$12$DaMsWNNP6mnxLGK3zUSLV.fqB9r9bPkSIuXQQSNgSpM8eMgU0G3i6', 'uploads/images/66a280ff9870c.jpeg', 'staff', NULL, NULL, '2024-07-25 09:44:48', '2024-07-25 09:44:48', NULL),
(9, 'Lê Chuyên VIên 4', 123456778, '0338475980', '12l22qh12@gmail.com', NULL, '2024-07-25 07:30:00', '$2y$12$3TZR3oC3pEIBkLPw/seOtO4W/F/P7LtxQ08lUQ.2/9//d3.V3sqLy', 'uploads/images/66a28290bb8d1.jpeg', 'staff', NULL, NULL, '2024-07-25 09:51:29', '2024-07-26 04:26:45', '2024-07-26 04:26:45'),
(25, 'Nhân viên viên 2', 111456677, '0221423212', 'lfs1df1@gmail.com', NULL, NULL, '$2y$12$iPJufrejOXdAsD6MV1Emc.zHlUaW33GCFsEGNDt3QP00Ke/fKy6u.', NULL, 'staff', NULL, NULL, '2024-07-26 03:18:06', '2024-07-26 04:24:24', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `enterprises`
--
ALTER TABLE `enterprises`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Chỉ mục cho bảng `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Chỉ mục cho bảng `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Chỉ mục cho bảng `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `systems`
--
ALTER TABLE `systems`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_code_unique` (`taxcode`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `enterprises`
--
ALTER TABLE `enterprises`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `staffs`
--
ALTER TABLE `staffs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `systems`
--
ALTER TABLE `systems`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
