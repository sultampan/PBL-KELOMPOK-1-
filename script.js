// Toggle Search Box
function toggleSearch() {
    const overlay = document.getElementById('searchOverlay');
    const input = document.getElementById('searchInput');
    
    overlay.classList.toggle('active');
    
    if (overlay.classList.contains('active')) {
        input.focus();
    } else {
        input.value = '';
    }
}

// Close search when clicking outside
document.getElementById('searchOverlay')?.addEventListener('click', function(e) {
    if (e.target === this) {
        toggleSearch();
    }
});

// Search functionality (Enter key)
document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const searchTerm = this.value;
        alert('Searching for: ' + searchTerm);
        // Di sini Anda bisa tambahkan fungsi search yang sebenarnya
    }
});

// Carousel functionality
const carouselStates = {
    'industry': 0,
    'education': 0,
    'government': 0
};

function scrollCarousel(carouselId, direction) {
    const carousel = document.getElementById(`${carouselId}-carousel`);
    const track = carousel.querySelector('.carousel-track');
    const items = track.querySelectorAll('img');
    const itemWidth = items[0].offsetWidth + 40; // width + gap
    
    // Update state
    carouselStates[carouselId] += direction;
    
    // Prevent scrolling beyond limits
    const maxScroll = -(items.length - 3) * itemWidth;
    const currentScroll = carouselStates[carouselId] * -itemWidth;
    
    if (currentScroll > 0) {
        carouselStates[carouselId] = 0;
    } else if (currentScroll < maxScroll) {
        carouselStates[carouselId] = Math.ceil(maxScroll / itemWidth);
    }
    
    // Apply transform
    track.style.transform = `translateX(${carouselStates[carouselId] * -itemWidth}px)`;
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add scroll effect to navbar
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.boxShadow = '0 4px 10px rgba(0,0,0,0.15)';
    } else {
        navbar.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
    }
});