// Custom JavaScript for AutoEnthusiasts forum

document.addEventListener('DOMContentLoaded', function() {
    // Form validation for registration
    const registerForm = document.querySelector('#registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                confirmPassword.focus();
            }
            
            // Check if at least one interest is selected
            const interests = document.querySelectorAll('input[type="checkbox"]:checked');
            if (interests.length === 0) {
                e.preventDefault();
                alert('Please select at least one vehicle interest!');
            }
        });
    }
    
    // Form validation for login
    const loginForm = document.querySelector('#loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('loginEmail');
            const password = document.getElementById('loginPassword');
            
            if (!email.value || !password.value) {
                e.preventDefault();
                alert('Please fill in all fields!');
            }
        });
    }
    
    // Thread creation form validation
    const newThreadForm = document.querySelector('#newThreadForm');
    if (newThreadForm) {
        newThreadForm.addEventListener('submit', function(e) {
            const title = document.getElementById('threadTitle');
            const content = document.getElementById('threadContent');
            
            if (!title.value || !content.value) {
                e.preventDefault();
                alert('Please fill in all required fields!');
            }
        });
    }
    
    // Reply form validation
    const replyForm = document.querySelector('#replyForm');
    if (replyForm) {
        replyForm.addEventListener('submit', function(e) {
            const replyContent = document.getElementById('replyContent');
            
            if (!replyContent.value) {
                e.preventDefault();
                alert('Please write your reply before posting!');
            }
        });
    }
    
    // Simulate loading threads based on category from URL
    if (window.location.pathname.includes('threads.html')) {
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        
        if (category) {
            // Update page title and breadcrumb based on category
            const categoryTitles = {
                'cars': 'Cars Discussion',
                'motorcycles': 'Motorcycles Discussion',
                'trucks': 'Trucks & SUVs Discussion',
                'classic': 'Classic Vehicles Discussion',
                'electric': 'Electric Vehicles Discussion'
            };
            
            const pageTitle = categoryTitles[category] || 'Vehicle Discussion';
            document.querySelector('h2').innerHTML = `<i class="fas ${getCategoryIcon(category)} me-2"></i>${pageTitle}`;
            document.querySelector('.breadcrumb .active').textContent = pageTitle.split(' ')[0];
        }
    }
    
    // Function to get icon based on category
    function getCategoryIcon(category) {
        const icons = {
            'cars': 'fa-car',
            'motorcycles': 'fa-motorcycle',
            'trucks': 'fa-truck-pickup',
            'classic': 'fa-history',
            'electric': 'fa-charging-station'
        };
        return icons[category] || 'fa-comments';
    }
    
    // Like button functionality
    document.querySelectorAll('.btn-like').forEach(button => {
        button.addEventListener('click', function() {
            const likeCount = this.querySelector('.like-count');
            let count = parseInt(likeCount.textContent);
            
            if (this.classList.contains('btn-outline-primary')) {
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                likeCount.textContent = count + 1;
            } else {
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
                likeCount.textContent = count - 1;
            }
        });
    });
});