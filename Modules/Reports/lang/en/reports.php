<?php

return [
    'title' => 'Reports & Analytics',
    'description' => 'Comprehensive insights for job postings and performance',
    
    // General
    'status' => 'Status',
    'count' => 'Count',
    'category' => 'Category',
    'city' => 'City',
    'user' => 'User',
    'posts' => 'Posts',
    'posts_count' => 'Posts Count',
    'no_data' => 'No data available',
    'view_details' => 'View Details',
    'total_revenue' => 'Total Revenue',
    'all_registered_members' => 'All registered members',
    'average_revenue_per_user' => 'Average revenue per user',
    'back_to_reports' => 'Back to Reports',
    
    // Statuses
    'statuses' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        'accepted' => 'Accepted',
        'awaiting_payment' => 'Awaiting Payment',
        'payment_failed' => 'Payment Failed',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    
    // Reports sections
    'reports' => [
        // Posts Report
        'posts' => [
            'title' => 'Posts Analytics',
            'description' => 'Detailed statistics and insights for all job posts',
            'total' => 'Total Posts',
            'active' => 'Active Posts',
            'pending' => 'Pending Posts',
            'expired' => 'Expired Posts',
            'featured' => 'Featured Posts',
            'by_status' => 'Posts by Status',
            'status_breakdown' => 'Distribution by post status',
            'by_category' => 'Posts by Category',
            'category_distribution' => 'Distribution across categories',
            'post_types' => 'Post Types',
            'featured_vs_regular' => 'Featured vs Regular comparison',
            'regular_posts' => 'Regular Posts',
            'standard_listings' => 'Standard job listings',
            'featured_posts' => 'Featured Posts',
            'premium_listings' => 'Premium highlighted listings',
            'top_cities' => 'Top Cities',
            'geographic_distribution' => 'Posts by location',
            'most_active_users' => 'Most Active Users',
            'top_contributors' => 'Top users by posts created',
        ],
        
        // Job Offers Report
        'job_offers' => [
            'title' => 'Job Offers Analytics',
            'description' => 'Track job offers and acceptance rates',
            'total' => 'Total Job Offers',
            'acceptance_rate' => 'Acceptance Rate',
            'avg_salary' => 'Average Salary',
            'pending' => 'Pending Offers',
            'by_status' => 'Offers by Status',
            'status_breakdown' => 'Distribution by offer status',
            'top_employers' => 'Top Employers',
            'employers_description' => 'Users sending most offers',
            'offers_sent' => 'Offers Sent',
            'popular_posts' => 'Popular Posts',
            'posts_description' => 'Posts receiving most offers',
            'post_title' => 'Post Title',
            'post_owner' => 'Post Owner',
            'offers_received' => 'Offers Received',
        ],
        
        // Members Report
        'members' => [
            'title' => 'Members Analytics',
            'description' => 'User registrations and growth statistics',
            'total' => 'Total Members',
            'new' => 'New Registrations',
            'active' => 'Active Members',
            'growth_rate' => 'Growth Rate',
            'registrations_over_time' => 'Registrations Over Time',
            'timeline_description' => 'User sign-ups timeline',
            'period' => 'Period',
            'registrations' => 'Registrations',
        ],
        
        // Financial Report
        'financial' => [
            'title' => 'Revenue Analytics',
            'description' => 'Package revenue and financial insights',
            'total_revenue' => 'Total Revenue',
            'paid_posts' => 'Paid Posts',
            'arpu' => 'Avg Revenue/User',
            'by_package' => 'Revenue by Package',
            'package_breakdown' => 'Revenue distribution by package',
            'package_name' => 'Package Name',
            'sales' => 'Sales',
            'revenue' => 'Revenue',
            'top_packages' => 'Top Performing Packages',
            'best_sellers' => 'Best-selling packages',
        ],
    ],
];
