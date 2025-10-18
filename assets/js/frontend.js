/**
 * جاوااسکریپت فرانت‌اند پلاگین
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // فیلتر AJAX
        var $filterForm = $('.ed-search-form form');
        var $filterButton = $filterForm.find('button[type="submit"]');
        var isLoading = false;
        
        if ($filterForm.length) {
            $filterForm.on('submit', function(e) {
                // اگر می‌خواهید فیلتر با AJAX کار کند، کامنت زیر را باز کنید
                // e.preventDefault();
                // handleAjaxFilter();
            });
        }
        
        function handleAjaxFilter() {
            if (isLoading) return;
            
            isLoading = true;
            $filterButton.prop('disabled', true).html('<span class="ed-loading"></span>');
            
            var formData = {
                action: 'ed_filter',
                nonce: edData.nonce,
                post_type: $filterForm.find('[name="post_type"]').val(),
                city: $filterForm.find('[name="city"]').val(),
                subject: $filterForm.find('[name="subject"]').val(),
                specialty: $filterForm.find('[name="specialty"]').val(),
                paged: 1
            };
            
            $.ajax({
                url: edData.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('.ed-grid').html(response.data.html);
                        
                        // Smooth scroll به نتایج
                        $('html, body').animate({
                            scrollTop: $('.ed-grid').offset().top - 100
                        }, 500);
                    }
                },
                error: function() {
                    alert('خطایی رخ داد. لطفا دوباره تلاش کنید.');
                },
                complete: function() {
                    isLoading = false;
                    $filterButton.prop('disabled', false).html('جستجو');
                }
            });
        }
        
        // انیمیشن ظاهر شدن کارت‌ها
        function animateCards() {
            $('.ed-card').each(function(index) {
                var $card = $(this);
                setTimeout(function() {
                    $card.css({
                        'opacity': '0',
                        'transform': 'translateY(20px)'
                    }).animate({
                        'opacity': '1'
                    }, 400).css({
                        'transform': 'translateY(0)'
                    });
                }, index * 50);
            });
        }
        
        // اجرای انیمیشن در صورت وجود کارت‌ها
        if ($('.ed-card').length) {
            animateCards();
        }
        
        // Lazy Loading برای تصاویر
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });
            
            $('.ed-card-image img[data-src]').each(function() {
                imageObserver.observe(this);
            });
        }
        
        // اضافه کردن افکت hover به تگ‌ها
        $('.ed-tag').on('mouseenter', function() {
            $(this).css('transform', 'scale(1.05)');
        }).on('mouseleave', function() {
            $(this).css('transform', 'scale(1)');
        });
        
        // کپی کردن شماره تلفن با کلیک
        $('.ed-card-phone, .ed-info-box a[href^="tel:"]').on('click', function(e) {
            var phoneNumber = $(this).text().trim();
            
            // کپی به کلیپ‌بورد
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(phoneNumber).then(function() {
                    showNotification('شماره تلفن کپی شد');
                });
            }
        });
        
        // نمایش اعلان
        function showNotification(message) {
            var $notification = $('<div class="ed-notification">' + message + '</div>');
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.addClass('show');
            }, 10);
            
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 2000);
        }
        
        // اضافه کردن استایل اعلان به صفحه
        if (!$('#ed-notification-style').length) {
            $('<style id="ed-notification-style">')
                .text('.ed-notification { position: fixed; bottom: 20px; right: 20px; background: #10b981; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); opacity: 0; transform: translateY(20px); transition: all 0.3s ease; z-index: 9999; } .ed-notification.show { opacity: 1; transform: translateY(0); }')
                .appendTo('head');
        }
        
        // افکت parallax برای تصاویر
        $(window).on('scroll', function() {
            var scrolled = $(window).scrollTop();
            $('.ed-card-image img').each(function() {
                var $img = $(this);
                var offsetTop = $img.offset().top;
                var windowHeight = $(window).height();
                
                if (offsetTop < scrolled + windowHeight && offsetTop + $img.height() > scrolled) {
                    var yPos = -(scrolled - offsetTop) * 0.1;
                    $img.css('transform', 'translateY(' + yPos + 'px) scale(1.1)');
                }
            });
        });
        
        // فیلتر سریع
        $('.ed-quick-filter').on('change', function() {
            var filterValue = $(this).val().toLowerCase();
            
            $('.ed-card').each(function() {
                var $card = $(this);
                var cardText = $card.text().toLowerCase();
                
                if (filterValue === '' || cardText.indexOf(filterValue) > -1) {
                    $card.fadeIn(300);
                } else {
                    $card.fadeOut(300);
                }
            });
        });
        
        // Smooth scroll برای لینک‌های داخلی
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 600);
            }
        });
        
    });
    
})(jQuery);
