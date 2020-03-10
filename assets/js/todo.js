jQuery(document).ready(function () {
    const form_id = jQuery('.todo').data('id');
    addTask(form_id);
    markAll(form_id);
    updateTask(form_id);
    removeTask(form_id);
});

function addTask(form_id) {
    jQuery('.todo__input').keypress(function (e) {
        if (e.which === 13) {
            let value = jQuery(this).val();
            if (value === '') {
                alert('Blank input is not allowed');
            } else {
                let nextID = getMaxTaskID() + 1;
                let content = '<div class="todo__row" data-task-id="' + nextID + '">' +
                    '<div class="todo__left">' +
                    '<input id="item-' + nextID + '" type="checkbox" class="todo__checkbox">' +
                    '<label for="item-' + nextID + '"><span class="sr-only">Task</span></label>' +
                    '</div>' +
                    '<div class="todo__right">' +
                    '<div class="todo__task">' +
                    value +
                    '<div class="task__remove"></div>' +
                    '</div> ' +
                    '</div>' +
                    '</div>';
                jQuery(content).appendTo('.todo__content');
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'addTaskToDb',
                        form_id: form_id,
                        task_id: nextID,
                        checked: 0,
                        value: jQuery.trim(value)
                    },
                });
                jQuery(this).attr('value', '');
            }
        }
    });
}

function markAll(form_id) {
    jQuery(document).on('click', '.mark__all', function () {
        let checked = checkStatus(jQuery(this));
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'updateAllTasks',
                form_id: form_id,
                checked: checked
            }
        });
    });
}

function removeTask(form_id) {
    jQuery(document).on('click', '.task__remove', function () {
        if (confirm('Do you wanna remove this task?')) {
            let task_id = jQuery(this).closest('.todo__row').data('task-id');
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'removeTaskFromDb',
                    form_id: form_id,
                    task_id: task_id
                },
            });
            jQuery(this).closest('.todo__row').remove();
        }
    });
    return 0;
}

function updateTask(form_id) {
    jQuery(document).on('click', '.task__edit', function () {

        let parentRow = jQuery(this).closest('.todo__row');
        let task_id = parentRow.data('task-id');
        let checked = (parentRow.find('.todo__checkbox').prop('checked')) ? 1 : 0;

        let text = jQuery(this).text();
        let input = jQuery("<input>");
        input.val(jQuery.trim(text));
        jQuery(this).replaceWith(input);
        input.focus();

        jQuery(input).blur(function () {
            let inputText = input.val();
            let div = '<div class="task__edit">' + jQuery.trim(inputText) + '</div>';
            jQuery(this).replaceWith(div);
            if (jQuery.trim(inputText).length > 0) {
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'updateTask',
                        form_id: form_id,
                        task_id: task_id,
                        checked: checked,
                        value: jQuery.trim(inputText)
                    }
                })
            } else {
                window.location.reload();
            }
        });
    });

    jQuery(document).on('click', '.todo__content .todo__checkbox', function () {
        let checked = (jQuery(this).prop('checked')) ? 1 : 0;
        let task_id = jQuery(this).closest('.todo__row').data('task-id');
        let value = jQuery(this).closest('.todo__row');
        value = jQuery(value).find('.task__edit').text();
        if (value.length > 0) {
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'updateTask',
                    form_id: form_id,
                    task_id: task_id,
                    checked: checked,
                    value: jQuery.trim(value)
                }
            })
        } else {
            window.location.reload();
        }
    });
}

function checkStatus(element) {
    if (element.prop('checked') === true) {
        jQuery('.todo__content .todo__row').each(function () {
            jQuery(this).find('.todo__checkbox').prop('checked', true);
        });
        return 1;
    } else {
        jQuery('.todo__content .todo__row').each(function () {
            jQuery(this).find('.todo__checkbox').prop('checked', false);
        });
        return 0;
    }
}

function getMaxTaskID() {
    let idArray = [];
    jQuery('.todo__content .todo__row').each(function (e) {
        idArray[e] = jQuery(this).data('task-id');
    });
    if (idArray.length > 0) return Math.max.apply(Math, idArray);
    return 0;
}