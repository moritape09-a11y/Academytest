/**
 * جاوااسکریپت پنل مدیریت
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // مدیریت انتخاب تصویر شاخص
        var mediaUploader;
        
        $('.ed-upload-image-button').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var imageId = button.next('.ed-image-id');
            var preview = button.siblings('.ed-image-preview');
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'انتخاب تصویر',
                button: {
                    text: 'انتخاب'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                imageId.val(attachment.id);
                preview.html('<img src="' + attachment.url + '" style="max-width: 200px;">');
            });
            
            mediaUploader.open();
        });
        
        // حذف تصویر
        $('.ed-remove-image-button').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            button.siblings('.ed-image-id').val('');
            button.siblings('.ed-image-preview').html('');
        });
        
        // اعتبارسنجی فرم
        $('form#post').on('submit', function() {
            var isValid = true;
            var errorMessage = '';
            
            // بررسی فیلدهای ضروری
            var title = $('#title').val().trim();
            if (!title) {
                errorMessage += 'لطفا عنوان را وارد کنید.\n';
                isValid = false;
            }
            
            // بررسی شماره تلفن
            var phone = $('#ed_phone').val().trim();
            if (phone && !isValidPhone(phone)) {
                errorMessage += 'فرمت شماره تلفن صحیح نیست.\n';
                isValid = false;
            }
            
            // بررسی ایمیل
            var email = $('#ed_email').val().trim();
            if (email && !isValidEmail(email)) {
                errorMessage += 'فرمت ایمیل صحیح نیست.\n';
                isValid = false;
            }
            
            if (!isValid) {
                alert(errorMessage);
                return false;
            }
            
            return true;
        });
        
        // اعتبارسنجی شماره تلفن
        function isValidPhone(phone) {
            var pattern = /^[\d\-\+\(\)\s]+$/;
            return pattern.test(phone);
        }
        
        // اعتبارسنجی ایمیل
        function isValidEmail(email) {
            var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return pattern.test(email);
        }
        
        // نمایش/مخفی کردن فیلدهای مرتبط
        $('.ed-conditional-field').each(function() {
            var $field = $(this);
            var condition = $field.data('condition');
            var conditionValue = $field.data('condition-value');
            
            $('#' + condition).on('change', function() {
                if ($(this).val() === conditionValue) {
                    $field.slideDown();
                } else {
                    $field.slideUp();
                }
            }).trigger('change');
        });
        
        // جستجوی زنده در لیست موسسات
        $('#ed-institution-search').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            
            $('.ed-institutions-list label').each(function() {
                var text = $(this).text().toLowerCase();
                if (text.indexOf(searchTerm) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // انتخاب/لغو انتخاب همه موسسات
        $('#ed-select-all-institutions').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.ed-institutions-list input[type="checkbox"]:visible').prop('checked', isChecked);
        });
        
        // شمارنده کاراکتر
        $('.ed-character-counter').each(function() {
            var $textarea = $(this);
            var maxLength = $textarea.attr('maxlength') || 500;
            var $counter = $('<div class="ed-counter" style="text-align: left; color: #666; font-size: 12px; margin-top: 5px;">0 / ' + maxLength + '</div>');
            $textarea.after($counter);
            
            $textarea.on('input', function() {
                var length = $(this).val().length;
                $counter.text(length + ' / ' + maxLength);
                
                if (length > maxLength * 0.9) {
                    $counter.css('color', '#d97706');
                } else {
                    $counter.css('color', '#666');
                }
            });
        });
        
        // پیش‌نمایش آنلاین
        $('#ed_website').on('blur', function() {
            var url = $(this).val();
            if (url && isValidUrl(url)) {
                var $preview = $('#ed-website-preview');
                if (!$preview.length) {
                    $preview = $('<div id="ed-website-preview" style="margin-top: 10px;"><a href="' + url + '" target="_blank">مشاهده وبسایت</a></div>');
                    $(this).after($preview);
                } else {
                    $preview.find('a').attr('href', url);
                }
            }
        });
        
        // اعتبارسنجی URL
        function isValidUrl(url) {
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        }
        
        // تبدیل خودکار URL
        $('#ed_website').on('blur', function() {
            var value = $(this).val().trim();
            if (value && !value.match(/^https?:\/\//)) {
                $(this).val('https://' + value);
            }
        });
        
        // ذخیره خودکار پیش‌نویس
        var autoSaveTimer;
        $('.ed-meta-box input, .ed-meta-box textarea, .ed-meta-box select').on('change', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                if (typeof wp !== 'undefined' && wp.autosave) {
                    wp.autosave.server.triggerSave();
                }
            }, 2000);
        });
        
        // نمایش راهنما
        $('.ed-help-icon').on('click', function() {
            var helpText = $(this).data('help');
            alert(helpText);
        });
        
        // رنگ‌آمیزی ردیف‌ها در جداول
        $('.wp-list-table tbody tr').hover(
            function() {
                $(this).css('background-color', '#f0f9ff');
            },
            function() {
                $(this).css('background-color', '');
            }
        );
        
    });
    
})(jQuery);
