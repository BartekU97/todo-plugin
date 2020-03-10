<div class="todo" data-id="<?= $list_id ?>">
    <div class="todo__top">
        <div class="todo__row">
            <div class="todo__left">
                <input id="all" type="checkbox" class="todo__checkbox mark__all todo__checkbox--top" name="all"/>
                <label for="all"><span class="sr-only">Check all tasks</span></label>
            </div>
            <div class="todo__right">
                <label for="task" class="sr-only">Type task here...</label>
                <input id="task" name="task" type="text" autocomplete="off" autofocus
                       placeholder="Type some task here..." class="todo__input"/>
            </div>
        </div>
    </div>
    <div class="todo__content">
        <?php if (count($result) > 0): ?>
            <?php foreach ($result as $item): ?>
                <div class="todo__row" data-task-id="<?= $item['elem_id'] ?>">
                    <div class="todo__left">
                        <input <?= ($item['checked']) ? 'checked' : '' ?> id="item-<?= $item['elem_id'] ?>"
                                                                          type="checkbox" class="todo__checkbox"/>
                        <label for="item-<?= $item['elem_id'] ?>"><span class="sr-only">Task</span></label>
                    </div>
                    <div class="todo__right">
                        <div class="todo__task">
                            <div class="task__edit">
                                <?= unserialize($item['value']) ?>
                            </div>
                            <div class="task__remove"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif ?>
    </div>
</div>
<script>
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>