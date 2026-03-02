<script>
// Search functionality
const searchInput = document.getElementById('searchInput');
const gameCards = document.querySelectorAll('.game-card');
const gameCount = document.querySelector('.game-count');
const categorySections = document.querySelectorAll('.category-section');
const categorySeparators = document.querySelectorAll('.category-separator');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        let visibleCount = 0;

        // Show all category sections during search
        categorySections.forEach(section => {
            section.style.display = 'block';
        });
        categorySeparators.forEach(sep => {
            sep.style.display = 'block';
        });

        gameCards.forEach(card => {
            const name = card.dataset.name || '';
            if (name.includes(query)) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        // Hide empty category sections
        categorySections.forEach(section => {
            const visibleCards = section.querySelectorAll('.game-card:not(.hidden)');
            if (visibleCards.length === 0) {
                section.style.display = 'none';
            }
        });

        if (gameCount) {
            gameCount.textContent = visibleCount + ' games';
        }
    });
}

// Category filter
const catPills = document.querySelectorAll('.cat-pill');
catPills.forEach(pill => {
    pill.addEventListener('click', function() {
        // Remove active from all
        catPills.forEach(p => p.classList.remove('active'));
        // Add active to clicked
        this.classList.add('active');

        const filter = this.dataset.filter;
        let visibleCount = 0;

        if (filter === 'all') {
            // Show all sections and cards
            categorySections.forEach(section => {
                section.style.display = 'block';
            });
            categorySeparators.forEach(sep => {
                sep.style.display = 'block';
            });
            gameCards.forEach(card => {
                card.classList.remove('hidden');
                visibleCount++;
            });
        } else if (filter === 'popular') {
            // Show only popular games across all categories
            categorySections.forEach(section => {
                section.style.display = 'block';
            });
            categorySeparators.forEach(sep => {
                sep.style.display = 'block';
            });

            gameCards.forEach(card => {
                if (card.dataset.popular === '1') {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            // Hide empty category sections
            categorySections.forEach(section => {
                const visibleCards = section.querySelectorAll('.game-card:not(.hidden)');
                if (visibleCards.length === 0) {
                    section.style.display = 'none';
                }
            });
        } else {
            // Show only the selected category section
            categorySections.forEach(section => {
                if (section.dataset.category === filter) {
                    section.style.display = 'block';
                    // Show all games in this category
                    const cards = section.querySelectorAll('.game-card');
                    cards.forEach(card => {
                        card.classList.remove('hidden');
                        visibleCount++;
                    });
                } else {
                    section.style.display = 'none';
                }
            });

            // Hide all separators when filtering by category
            categorySeparators.forEach(sep => {
                sep.style.display = 'none';
            });
        }

        if (gameCount) {
            gameCount.textContent = visibleCount + ' games';
        }

        // Clear search when filtering
        if (searchInput) {
            searchInput.value = '';
        }
    });
});

// FAQ toggle
function toggleFAQ(header) {
    const item = header.parentElement;
    const isActive = item.classList.contains('active');

    // Close all FAQs
    document.querySelectorAll('.faq-item').forEach(faq => {
        faq.classList.remove('active');
    });

    // Open clicked FAQ if it wasn't active
    if (!isActive) {
        item.classList.add('active');
    }
}

// Scroll to top button
const scrollTopBtn = document.getElementById('scroll-top-btn');
if (scrollTopBtn) {
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            scrollTopBtn.classList.add('visible');
        } else {
            scrollTopBtn.classList.remove('visible');
        }
    });

    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
</script>
