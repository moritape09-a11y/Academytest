/**
 * جاوااسکریپت پلاگین
 */

jQuery(document).ready(function($) {
    
    // کپی شماره تلفن با کلیک
    $('.edu-phone, .edu-info-box a[href^="tel:"]').on('click', function(e) {
        var phone = $(this).text().trim();
        
        // کپی به کلیپ‌بورد
        if (navigator.clipboard && navigator.clipboard.writeText) {
            e.preventDefault();
            navigator.clipboard.writeText(phone).then(function() {
                // نمایش پیام
                var $msg = $('<div class="edu-copied">شماره کپی شد ✓</div>');
                $('body').append($msg);
                $msg.fadeIn(200);
                
                setTimeout(function() {
                    $msg.fadeOut(200, function() {
                        $(this).remove();
                    });
                }, 2000);
            });
        }
    });
    
    // اضافه کردن استایل پیام
    if (!$('#edu-copied-style').length) {
        $('<style id="edu-copied-style">')
            .text('.edu-copied { position: fixed; bottom: 20px; right: 20px; background: #10b981; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; display: none; }')
            .appendTo('head');
    }
    
});
