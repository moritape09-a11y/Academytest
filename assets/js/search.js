/**
 * Educational Directory - Search Module
 * Professional AJAX Search with Live Results
 * 
 * @package EducationalDirectory
 * @version 3.0.0
 */

(function($) {
    'use strict';
    
    const EduSearch = {
        // Configuration
        config: {
            debounceDelay: 500,
            minSearchLength: 0,
            resultsPerPage: 12,
        },
        
        // State
        state: {
            currentRequest: null,
            debounceTimer: null,
        },
        
        // Cache DOM elements
        cache: {},
        
        /**
         * Initialize
         */
        init: function() {
            this.cacheElements();
            this.bindEvents();
            this.loadFromURL();
        },
        
        /**
         * Cache DOM elements
         */
        cacheElements: function() {
            this.cache = {
                $wrapper: $('.edu-search-wrapper'),
                $searchInput: $('#edu-main-search'),
                $filterType: $('#edu-filter-type'),
                $filterCity: $('#edu-filter-city'),
                $filterSubject: $('#edu-filter-subject'),
                $filterRating: $('#edu-filter-rating'),
                $btnSearch: $('.edu-btn-search'),
                $btnReset: $('.edu-btn-reset'),
                $results: $('#edu-search-results'),
            };
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            const self = this;
            
            // Search input (with debounce)
            this.cache.$searchInput.on('input', function() {
                clearTimeout(self.state.debounceTimer);
                self.state.debounceTimer = setTimeout(function() {
                    self.performSearch();
                }, self.config.debounceDelay);
            });
            
            // Filter changes
            this.cache.$filterType.add(this.cache.$filterCity)
                .add(this.cache.$filterSubject)
                .add(this.cache.$filterRating)
                .on('change', function() {
                    self.performSearch();
                });
            
            // Search button
            this.cache.$btnSearch.on('click', function(e) {
                e.preventDefault();
                self.performSearch();
            });
            
            // Reset button
            this.cache.$btnReset.on('click', function(e) {
                e.preventDefault();
                self.resetFilters();
            });
            
            // Browser back/forward
            $(window).on('popstate', function() {
                self.loadFromURL();
            });
        },
        
        /**
         * Perform search
         */
        performSearch: function() {
            // Cancel previous request
            if (this.state.currentRequest) {
                this.state.currentRequest.abort();
            }
            
            // Get values
            const query = this.cache.$searchInput.val().trim();
            const type = this.cache.$filterType.val();
            const city = this.cache.$filterCity.val();
            const subject = this.cache.$filterSubject.val();
            const rating = this.cache.$filterRating.val();
            
            // Check if we have any criteria
            if (!query && type === 'all' && !city && !subject && !rating) {
                this.cache.$results.empty();
                return;
            }
            
            // Show loading
            this.showLoading();
            
            // Update URL
            this.updateURL(query, type, city, subject, rating);
            
            // Make request
            this.state.currentRequest = $.ajax({
                url: eduDirData.restUrl + 'search',
                method: 'GET',
                data: {
                    query: query,
                    type: type,
                    city: city,
                    subject: subject,
                    rating: rating,
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', eduDirData.nonce);
                },
                success: this.handleSuccess.bind(this),
                error: this.handleError.bind(this),
                complete: function() {
                    this.state.currentRequest = null;
                }.bind(this)
            });
        },
        
        /**
         * Handle successful response
         */
        handleSuccess: function(response) {
            if (!response.success || response.total === 0) {
                this.showNoResults();
                return;
            }
            
            this.displayResults(response);
        },
        
        /**
         * Handle error
         */
        handleError: function(xhr, status) {
            if (status !== 'abort') {
                this.showError('خطا در برقراری ارتباط با سرور. لطفاً دوباره تلاش کنید.');
            }
        },
        
        /**
         * Display results
         */
        displayResults: function(response) {
            let html = '';
            
            // Header
            html += '<div class="edu-results-header edu-fade-in">';
            html += '<h3 class="edu-results-title">✨ نتایج جستجو</h3>';
            html += '<p class="edu-results-meta">' + response.total + ' مورد یافت شد';
            if (response.query) {
                html += ' برای "<strong>' + this.escapeHtml(response.query) + '</strong>"';
            }
            html += '</p></div>';
            
            // Results by type
            response.results.forEach((section, index) => {
                html += '<div class="edu-results-section edu-fade-in" style="animation-delay: ' + (index * 0.1) + 's">';
                html += '<h3 class="edu-results-section-title">';
                html += section.label;
                html += ' <span class="edu-results-count">(' + section.count + ')</span>';
                html += '</h3>';
                html += '<div class="edu-grid">';
                
                section.posts.forEach(post => {
                    html += this.renderCard(post);
                });
                
                html += '</div></div>';
            });
            
            this.cache.$results.html(html);
            
            // Smooth scroll to results
            $('html, body').animate({
                scrollTop: this.cache.$results.offset().top - 100
            }, 500);
        },
        
        /**
         * Render card
         */
        renderCard: function(post) {
            let html = '<div class="edu-card">';
            
            // Image
            html += '<div class="edu-card-image">';
            html += '<img src="' + post.thumbnail + '" alt="' + this.escapeHtml(post.title) + '">';
            if (post.rating > 0) {
                html += '<span class="edu-rating-badge">';
                html += '<span class="edu-rating-badge-icon">⭐</span>';
                html += post.rating.toFixed(1);
                html += '</span>';
            }
            html += '</div>';
            
            // Content
            html += '<div class="edu-card-content">';
            html += '<h3 class="edu-card-title">';
            html += '<a href="' + post.permalink + '">' + this.escapeHtml(post.title) + '</a>';
            html += '</h3>';
            
            // Rating
            if (post.rating > 0) {
                html += '<div class="edu-rating">' + this.renderStars(post.rating) + '</div>';
            }
            
            // Meta
            if (post.city || post.subjects.length > 0) {
                html += '<div class="edu-card-meta">';
                if (post.city) {
                    html += '<span class="edu-meta-item">';
                    html += '<span class="edu-meta-icon">📍</span>';
                    html += this.escapeHtml(post.city);
                    html += '</span>';
                }
                if (post.subjects.length > 0) {
                    html += '<span class="edu-meta-item">';
                    html += '<span class="edu-meta-icon">📚</span>';
                    html += this.escapeHtml(post.subjects.slice(0, 2).join('، '));
                    if (post.subjects.length > 2) {
                        html += ' +' + (post.subjects.length - 2);
                    }
                    html += '</span>';
                }
                html += '</div>';
            }
            
            // Excerpt
            if (post.excerpt) {
                html += '<p class="edu-excerpt">' + this.escapeHtml(post.excerpt) + '</p>';
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
        },
        
        /**
         * Render stars
         */
        renderStars: function(rating) {
            let html = '';
            const full = Math.floor(rating);
            const hasHalf = (rating - full) >= 0.5;
            const empty = 5 - full - (hasHalf ? 1 : 0);
            
            for (let i = 0; i < full; i++) {
                html += '<span class="edu-star edu-star-full">★</span>';
            }
            if (hasHalf) {
                html += '<span class="edu-star edu-star-half">★</span>';
            }
            for (let i = 0; i < empty; i++) {
                html += '<span class="edu-star edu-star-empty">☆</span>';
            }
            
            return html;
        },
        
        /**
         * Show loading
         */
        showLoading: function() {
            const html = `
                <div class="edu-loading">
                    <div class="edu-loading-spinner"></div>
                    <p class="edu-loading-text">در حال جستجو...</p>
                </div>
                <div class="edu-skeleton-grid">
                    ${this.renderSkeleton()}
                    ${this.renderSkeleton()}
                    ${this.renderSkeleton()}
                    ${this.renderSkeleton()}
                </div>
            `;
            this.cache.$results.html(html);
        },
        
        /**
         * Render skeleton
         */
        renderSkeleton: function() {
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
        },
        
        /**
         * Show no results
         */
        showNoResults: function() {
            const html = `
                <div class="edu-results-header">
                    <div class="edu-no-results-box edu-fade-in">
                        <div class="edu-no-results-icon">😕</div>
                        <h3 class="edu-no-results-title">موردی یافت نشد</h3>
                        <p class="edu-no-results-text">لطفاً فیلترهای مختلفی امتحان کنید یا عبارت دیگری جستجو کنید.</p>
                    </div>
                </div>
            `;
            this.cache.$results.html(html);
        },
        
        /**
         * Show error
         */
        showError: function(message) {
            const html = `
                <div class="edu-error-box">
                    <div class="edu-error-icon">⚠️</div>
                    <h3 class="edu-error-title">خطا</h3>
                    <p class="edu-error-text">${message}</p>
                </div>
            `;
            this.cache.$results.html(html);
        },
        
        /**
         * Reset filters
         */
        resetFilters: function() {
            this.cache.$searchInput.val('');
            this.cache.$filterType.val('all');
            this.cache.$filterCity.val('');
            this.cache.$filterSubject.val('');
            this.cache.$filterRating.val('');
            this.cache.$results.empty();
            
            if (window.history.pushState) {
                window.history.pushState({}, '', window.location.pathname);
            }
        },
        
        /**
         * Update URL
         */
        updateURL: function(query, type, city, subject, rating) {
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
        },
        
        /**
         * Load from URL
         */
        loadFromURL: function() {
            const params = new URLSearchParams(window.location.search);
            
            if (params.has('q')) {
                this.cache.$searchInput.val(params.get('q'));
            }
            if (params.has('type')) {
                this.cache.$filterType.val(params.get('type'));
            }
            if (params.has('city')) {
                this.cache.$filterCity.val(params.get('city'));
            }
            if (params.has('subject')) {
                this.cache.$filterSubject.val(params.get('subject'));
            }
            if (params.has('rating')) {
                this.cache.$filterRating.val(params.get('rating'));
            }
            
            if (params.toString()) {
                this.performSearch();
            }
        },
        
        /**
         * Escape HTML
         */
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        if ($('.edu-search-wrapper').length > 0) {
            EduSearch.init();
        }
    });
    
})(jQuery);
