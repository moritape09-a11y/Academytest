/**
 * AJAX Search - جستجوی حرفه‌ای بدون رفرش صفحه
 */

(function($) {
    'use strict';
    
    // Configuration
    const EDU_AJAX = {
        apiUrl: eduAjaxData.restUrl + 'edu/v1/search',
        nonce: eduAjaxData.nonce,
        debounceDelay: 500,
        currentRequest: null,
    };
    
    /**
     * Initialize AJAX Search
     */
    function initAjaxSearch() {
        const $form = $('.edu-advanced-search');
        if ($form.length === 0) return;
        
        const $searchInput = $form.find('.edu-search-input-main');
        const $typeSelect = $form.find('select[name="edu_type"]');
        const $citySelect = $form.find('select[name="edu_city"]');
        const $subjectSelect = $form.find('select[name="edu_subject"]');
        const $ratingSelect = $form.find('select[name="edu_rating"]');
        const $searchBtn = $form.find('.edu-btn-search');
        const $resetBtn = $form.find('.edu-btn-reset');
        const $resultsContainer = $form.find('.edu-search-results');
        
        // Prevent form submission
        $form.on('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
        
        // Live search on input (with debounce)
        let searchTimeout;
        $searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                performSearch();
            }, EDU_AJAX.debounceDelay);
        });
        
        // Search on filter change
        $typeSelect.add($citySelect).add($subjectSelect).add($ratingSelect).on('change', function() {
            performSearch();
        });
        
        // Search button
        $searchBtn.on('click', function(e) {
            e.preventDefault();
            performSearch();
        });
        
        // Reset button
        $resetBtn.on('click', function(e) {
            e.preventDefault();
            resetFilters();
        });
        
        /**
         * Perform AJAX Search
         */
        function performSearch() {
            // Cancel previous request
            if (EDU_AJAX.currentRequest) {
                EDU_AJAX.currentRequest.abort();
            }
            
            // Get values
            const query = $searchInput.val().trim();
            const type = $typeSelect.val();
            const city = $citySelect.val();
            const subject = $subjectSelect.val();
            const rating = $ratingSelect.val();
            
            // Check if we have any search criteria
            if (!query && type === 'all' && !city && !subject && !rating) {
                $resultsContainer.empty();
                return;
            }
            
            // Show loading
            showLoading();
            
            // Update URL (without page reload)
            updateURL(query, type, city, subject, rating);
            
            // Make AJAX request
            EDU_AJAX.currentRequest = $.ajax({
                url: EDU_AJAX.apiUrl,
                method: 'GET',
                data: {
                    query: query,
                    type: type,
                    city: city,
                    subject: subject,
                    rating: rating,
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', EDU_AJAX.nonce);
                },
                success: function(response) {
                    if (response.success) {
                        displayResults(response);
                    } else {
                        showError('خطا در دریافت نتایج');
                    }
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        showError('خطا در ارتباط با سرور');
                    }
                },
                complete: function() {
                    EDU_AJAX.currentRequest = null;
                }
            });
        }
        
        /**
         * Display Results
         */
        function displayResults(response) {
            $resultsContainer.empty();
            
            if (response.total === 0) {
                $resultsContainer.html(`
                    <div class="edu-results-header">
                        <div class="edu-no-results-box edu-fade-in">
                            <div class="edu-no-results-icon">😕</div>
                            <h3 class="edu-no-results-title">موردی یافت نشد</h3>
                            <p class="edu-no-results-text">لطفاً فیلترهای مختلفی امتحان کنید یا عبارت دیگری جستجو کنید.</p>
                        </div>
                    </div>
                `);
                return;
            }
            
            let html = '<div class="edu-results-header edu-fade-in">';
            html += '<h3 class="edu-results-title">✨ نتایج جستجو</h3>';
            html += '<p class="edu-results-meta">' + response.total + ' مورد یافت شد';
            
            if (response.query) {
                html += ' برای "<strong>' + escapeHtml(response.query) + '</strong>"';
            }
            
            html += '</p></div>';
            
            // Display results by type
            response.results.forEach(function(section, index) {
                html += '<div class="edu-results-section edu-fade-in" style="animation-delay: ' + (index * 0.1) + 's">';
                html += '<h3 class="edu-results-section-title">';
                html += section.label;
                html += ' <span class="edu-results-count">(' + section.count + ')</span>';
                html += '</h3>';
                html += '<div class="edu-grid">';
                
                section.posts.forEach(function(post) {
                    html += renderCard(post);
                });
                
                html += '</div></div>';
            });
            
            $resultsContainer.html(html);
            
            // Scroll to results (smooth)
            $('html, body').animate({
                scrollTop: $resultsContainer.offset().top - 100
            }, 500);
        }
        
        /**
         * Render Card
         */
        function renderCard(post) {
            let html = '<div class="edu-card">';
            
            // Featured Image
            if (post.thumbnail) {
                html += '<div class="edu-card-image">';
                html += '<img src="' + post.thumbnail + '" alt="' + escapeHtml(post.title) + '">';
                
                // Rating badge
                if (post.rating > 0) {
                    html += '<span class="edu-rating-badge">';
                    html += '<span class="edu-rating-badge-icon">⭐</span>';
                    html += post.rating;
                    html += '</span>';
                }
                
                html += '</div>';
            }
            
            // Content
            html += '<div class="edu-card-content">';
            html += '<h3 class="edu-card-title">';
            html += '<a href="' + post.permalink + '">' + escapeHtml(post.title) + '</a>';
            html += '</h3>';
            
            // Rating stars
            if (post.rating > 0) {
                html += '<div class="edu-rating">' + renderStars(post.rating) + '</div>';
            }
            
            // Meta info
            if (post.city || post.subjects.length > 0) {
                html += '<div class="edu-card-meta">';
                
                if (post.city) {
                    html += '<span class="edu-meta-item">';
                    html += '<span class="edu-meta-icon">📍</span>';
                    html += escapeHtml(post.city);
                    html += '</span>';
                }
                
                if (post.subjects.length > 0) {
                    html += '<span class="edu-meta-item">';
                    html += '<span class="edu-meta-icon">📚</span>';
                    html += escapeHtml(post.subjects.slice(0, 2).join('، '));
                    if (post.subjects.length > 2) {
                        html += ' +' + (post.subjects.length - 2);
                    }
                    html += '</span>';
                }
                
                html += '</div>';
            }
            
            // Excerpt
            if (post.excerpt) {
                html += '<p class="edu-excerpt">' + escapeHtml(post.excerpt) + '</p>';
            }
            
            // Actions
            html += '<div class="edu-card-actions">';
            html += '<a href="' + post.permalink + '" class="edu-btn-primary">مشاهده بیشتر</a>';
            if (post.phone) {
                html += '<a href="tel:' + post.phone + '" class="edu-btn-secondary">تماس</a>';
            }
            html += '</div>';
            
            html += '</div></div>';
            
            return html;
        }
        
        /**
         * Render Stars
         */
        function renderStars(rating) {
            let html = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = (rating % 1) >= 0.5;
            const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
            
            // Full stars
            for (let i = 0; i < fullStars; i++) {
                html += '<span class="edu-star edu-star-full">★</span>';
            }
            
            // Half star
            if (hasHalfStar) {
                html += '<span class="edu-star edu-star-half">★</span>';
            }
            
            // Empty stars
            for (let i = 0; i < emptyStars; i++) {
                html += '<span class="edu-star edu-star-empty">☆</span>';
            }
            
            return html;
        }
        
        /**
         * Show Loading
         */
        function showLoading() {
            $resultsContainer.html(`
                <div class="edu-loading">
                    <div class="edu-loading-spinner"></div>
                    <p class="edu-loading-text">در حال جستجو...</p>
                </div>
                <div class="edu-skeleton-grid">
                    ${renderSkeleton()}
                    ${renderSkeleton()}
                    ${renderSkeleton()}
                    ${renderSkeleton()}
                </div>
            `);
        }
        
        /**
         * Render Skeleton
         */
        function renderSkeleton() {
            return `
                <div class="edu-skeleton-card">
                    <div class="edu-skeleton-image"></div>
                    <div class="edu-skeleton-content">
                        <div class="edu-skeleton-title"></div>
                        <div class="edu-skeleton-text"></div>
                        <div class="edu-skeleton-text"></div>
                    </div>
                </div>
            `;
        }
        
        /**
         * Show Error
         */
        function showError(message) {
            $resultsContainer.html(`
                <div class="edu-error-box">
                    <div class="edu-error-icon">⚠️</div>
                    <h3 class="edu-error-title">خطا</h3>
                    <p class="edu-error-text">${message}</p>
                </div>
            `);
        }
        
        /**
         * Reset Filters
         */
        function resetFilters() {
            $searchInput.val('');
            $typeSelect.val('all');
            $citySelect.val('');
            $subjectSelect.val('');
            $ratingSelect.val('');
            $resultsContainer.empty();
            
            // Update URL
            if (window.history.pushState) {
                window.history.pushState({}, '', window.location.pathname);
            }
        }
        
        /**
         * Update URL (History API)
         */
        function updateURL(query, type, city, subject, rating) {
            if (!window.history.pushState) return;
            
            const params = new URLSearchParams();
            if (query) params.set('q', query);
            if (type && type !== 'all') params.set('type', type);
            if (city) params.set('city', city);
            if (subject) params.set('subject', subject);
            if (rating) params.set('rating', rating);
            
            const newUrl = params.toString() ? 
                window.location.pathname + '?' + params.toString() : 
                window.location.pathname;
            
            window.history.pushState({}, '', newUrl);
        }
        
        /**
         * Escape HTML
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        /**
         * Load from URL params on page load
         */
        function loadFromURL() {
            const params = new URLSearchParams(window.location.search);
            
            if (params.has('q')) {
                $searchInput.val(params.get('q'));
            }
            if (params.has('type')) {
                $typeSelect.val(params.get('type'));
            }
            if (params.has('city')) {
                $citySelect.val(params.get('city'));
            }
            if (params.has('subject')) {
                $subjectSelect.val(params.get('subject'));
            }
            if (params.has('rating')) {
                $ratingSelect.val(params.get('rating'));
            }
            
            // If we have params, trigger search
            if (params.toString()) {
                performSearch();
            }
        }
        
        // Load from URL on init
        loadFromURL();
        
        // Handle browser back/forward
        window.addEventListener('popstate', function() {
            loadFromURL();
        });
    }
    
    // Initialize on document ready
    $(document).ready(function() {
        initAjaxSearch();
    });
    
})(jQuery);
