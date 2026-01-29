import { createPieChart, createBarChart } from './charts';

document.addEventListener('DOMContentLoaded', function () {
    // Posts by Status Pie Chart
    const statusChartCanvas = document.getElementById('postsStatusChart');
    if (statusChartCanvas && window.postsChartData) {
        const { byStatus } = window.postsChartData;

        if (byStatus && Object.keys(byStatus).length > 0) {
            const labels = Object.keys(byStatus).map(status =>
                status.charAt(0).toUpperCase() + status.slice(1)
            );
            const data = Object.values(byStatus);
            const colors = {
                'pending': 'rgba(255, 193, 7, 0.8)',
                'approved': 'rgba(76, 175, 80, 0.8)',
                'rejected': 'rgba(244, 67, 54, 0.8)',
                'expired': 'rgba(158, 158, 158, 0.8)'
            };
            const chartColors = Object.keys(byStatus).map(status => colors[status] || 'rgba(102, 126, 234, 0.8)');

            createPieChart('postsStatusChart', labels, data, chartColors);
        }
    }

    // Posts by Category Bar Chart
    const categoryChartCanvas = document.getElementById('postsCategoryChart');
    if (categoryChartCanvas && window.postsChartData) {
        const { byCategory } = window.postsChartData;

        if (byCategory && byCategory.length > 0) {
            const labels = byCategory.map(item => item.category);
            const data = byCategory.map(item => item.count);

            createBarChart('postsCategoryChart', labels, data, 'Posts', 'rgba(102, 126, 234, 0.8)');
        }
    }

    // Posts by City Bar Chart
    const cityChartCanvas = document.getElementById('postsCityChart');
    if (cityChartCanvas && window.postsChartData) {
        const { byCity } = window.postsChartData;

        if (byCity && byCity.length > 0) {
            const labels = byCity.map(item => item.city);
            const data = byCity.map(item => item.count);

            createBarChart('postsCityChart', labels, data, 'Posts', 'rgba(76, 175, 80, 0.8)');
        }
    }
});
