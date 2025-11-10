// Task Manager - JavaScript with AJAX functionality

$(document).ready(function() {
    
    // AJAX: Toggle Task Status
    $('.status-toggle').on('change', function() {
        const taskId = $(this).data('task-id');
        const isCompleted = $(this).is(':checked');
        const $row = $(this).closest('tr');
        
        // Show loading state
        $(this).prop('disabled', true);
        
        $.ajax({
            url: 'ajax/update_status.php',
            method: 'POST',
            data: {
                task_id: taskId,
                status: isCompleted ? 'completed' : 'pending'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update UI
                    const $titleCell = $row.find('td:eq(1)');
                    const $descCell = $row.find('td:eq(2)');
                    const $label = $row.find('.form-check-label');
                    
                    if (isCompleted) {
                        $titleCell.addClass('text-decoration-line-through text-muted');
                        $descCell.addClass('text-decoration-line-through text-muted');
                        $label.text('Completed');
                        
                        // Success animation
                        $row.addClass('table-success');
                        setTimeout(function() {
                            $row.removeClass('table-success');
                        }, 1000);
                    } else {
                        $titleCell.removeClass('text-decoration-line-through text-muted');
                        $descCell.removeClass('text-decoration-line-through text-muted');
                        $label.text('Pending');
                    }
                    
                    // Update statistics
                    updateStatistics();
                } else {
                    alert('Error updating task status');
                    // Revert checkbox
                    $(this).prop('checked', !isCompleted);
                }
            }.bind(this),
            error: function() {
                alert('Connection error. Please try again.');
                // Revert checkbox
                $(this).prop('checked', !isCompleted);
            }.bind(this),
            complete: function() {
                $(this).prop('disabled', false);
            }.bind(this)
        });
    });
    
    // Update statistics with AJAX
    function updateStatistics() {
        $.ajax({
            url: 'ajax/get_statistics.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update stat cards
                    $('.bg-primary h2').text(response.total);
                    $('.bg-success h2').text(response.completed);
                    $('.bg-warning h2').text(response.pending);
                }
            }
        });
    }
    
    // Form validation
    $('form').on('submit', function(e) {
        // Check for reCAPTCHA in form
        const recaptchaResponse = grecaptcha.getResponse();
        const hasRecaptcha = $(this).find('.g-recaptcha').length > 0;
        
        if (hasRecaptcha && !recaptchaResponse) {
            e.preventDefault();
            alert('Please complete the reCAPTCHA verification');
            return false;
        }
        
        // Validate task title for add task form
        const title = $('#title').val().trim();
        if ($(this).attr('action') === 'add_task.php' && title === '') {
            e.preventDefault();
            alert('Please enter a task title');
            $('#title').focus();
            return false;
        }
    });
    
    // Auto-hide success messages
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
    
    // Add smooth scrolling
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
});

