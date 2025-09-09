-- Create blog table if it doesn't exist
CREATE TABLE IF NOT EXISTS `blog` (
  `blog_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) DEFAULT 1,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `publish_date` date DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`blog_id`),
  KEY `idx_author` (`author_id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_publish_date` (`publish_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample blog posts
INSERT INTO `blog` (`title`, `content`, `author_id`, `category`, `status`, `publish_date`) VALUES
('10 Tips for a Successful First Date', 'Meeting someone for the first time can be both exciting and nerve-wracking. Here are 10 essential tips to make your first date memorable and meaningful...', 1, 'Dating Tips', 'published', '2024-06-01'),
('Building Trust in Relationships', 'Trust is the foundation of any strong relationship. Learn the fundamental principles of trust and how to strengthen your bond with your partner...', 1, 'Relationships', 'published', '2024-05-30'),
('Modern Wedding Traditions', 'Discover contemporary approaches to traditional wedding customs. Learn how to blend modern elements with time-honored traditions...', 1, 'Wedding', 'draft', '2024-05-29'),
('Communication Skills for Couples', 'Improve your relationship through better communication techniques. Learn active listening, expressing feelings, and resolving conflicts...', 1, 'Communication', 'published', '2024-05-28'),
('Online Dating Safety Guide', 'Stay safe while finding love in the digital age with these essential tips. Learn how to protect yourself and identify potential red flags...', 1, 'Safety', 'archived', '2024-05-27'); 