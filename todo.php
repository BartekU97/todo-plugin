<?php
/*
Plugin Name: ToDo
Plugin URI: https://wordpress.org
Description: Simple ToDo list with shortcode
Version: 1.0
Author: Bartłomiej Urbanek
Author URI: https://www.linkedin.com/in/bart%C5%82omiej-urbanek-78b278171/
Text Domain: to-do-list

*/

if (!defined('ABSPATH')) exit;
define('VERSION', '1.0');
define('PLUGIN_NAME', 'ToDo');


register_activation_hook(__FILE__, ['todo', 'prepareDb']);
register_uninstall_hook(__FILE__, ['todo', 'clear']);

if (!class_exists('todo')) {
    class todo
    {

        private $version;
        private $wpdb;
        private $table;
        private $plugin_name;

        function __construct()
        {
            global $wpdb;
            $this->wpdb = $wpdb;
            $this->table = $wpdb->prefix . 'todo';
            $this->version = VERSION;
            $this->plugin_name = PLUGIN_NAME;

            add_shortcode('todo', [$this, 'toDoList']);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
            add_action('wp_ajax_addTaskToDb', [$this, 'addTaskToDb']);
            add_action('wp_ajax_nopriv_addTaskToDb', [$this, 'addTaskToDb']);

            add_action('wp_ajax_updateAllTasks', [$this, 'updateAllTasks']);
            add_action('wp_ajax_nopriv_updateAllTasks', [$this, 'updateAllTasks']);

            add_action('wp_ajax_updateTask', [$this, 'updateTask']);
            add_action('wp_ajax_nopriv_updateTask', [$this, 'updateTask']);

            add_action('wp_ajax_removeTaskFromDb', [$this, 'removeTaskFromDb']);
            add_action('wp_ajax_nopriv_removeTaskFromDb', [$this, 'removeTaskFromDb']);
        }

        public function clear()
        {
            global $wpdb;
            $table = $wpdb->prefix . 'todo';
            $sql = "DROP TABLE IF EXISTS {$table}";
            $wpdb->query($sql);
        }

        public function prepareDb()
        {
            global $wpdb;
            $table = $wpdb->prefix . 'todo';
            $collate = $wpdb->collate;
            if ($wpdb->get_var("SHOW TABLEs LIKE '%{$table}%") !== $table) {

                $sql = "CREATE TABLE {$table} (";
                $sql .= "`id` int(11) NOT NULL,";
                $sql .= "`elem_id` int (11) NOT NULL,";
                $sql .= "`checked` boolean NOT NULL,";
                $sql .= "`value` text NOT NULL";
                $sql .= ") COLLATE {$collate}";
                require(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            } else {
                echo "Table exist in database";
            }

        }

        public function toDoList($attr)
        {
            if(isset($attr['id'])) {
                $list_id = (int)$attr['id'];
            }else {
                $list_id = 12455;
            }
            $sql = "SELECT * FROM {$this->table} WHERE id = {$list_id}";
            $result = $this->wpdb->get_results($sql, ARRAY_A);
            ob_start();
            include(plugin_dir_path(__FILE__) . 'partials/todo.php');
            return ob_get_clean();
        }

        public function enqueue_assets()
        {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'assets/css/todo.css', [], $this->version, 'all');
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'assets/js/todo.js', ['jquery'], $this->version, true);
        }

        public function addTaskToDb()
        {
            $form_id = (int)$_POST['form_id'];
            $task_id = (int)$_POST['task_id'];
            $checked = (int)$_POST['checked'];
            $value = serialize($_POST['value']);

            $this->wpdb->insert(
                $this->table,
                [
                    'id' => $form_id,
                    'elem_id' => $task_id,
                    'checked' => $checked,
                    'value' => $value
                ]
            );
        }

        public function removeTaskFromDb()
        {
            $form_id = (int)$_POST['form_id'];
            $task_id = (int)$_POST['task_id'];

            $this->wpdb->delete(
                $this->table,
                [
                    'id' => $form_id,
                    'elem_id' => $task_id,
                ]
            );
        }

        public function updateAllTasks()
        {
            $form_id = (int)$_POST['form_id'];
            $checked = (int)$_POST['checked'];

            $this->wpdb->update(
                $this->table,
                [
                    'checked' => $checked,
                ],
                [
                    'id' => $form_id,
                ]
            );
        }

        public function updateTask()
        {
            $form_id = (int)$_POST['form_id'];
            $task_id = (int)$_POST['task_id'];
            $checked = (int)$_POST['checked'];
            $value = serialize($_POST['value']);

            $this->wpdb->update(
                $this->table,
                [
                    'checked' => $checked,
                    'value' => $value,
                ],
                [
                    'id' => $form_id,
                    'elem_id' => $task_id
                ]
            );
        }
    }
}

$todo = new todo();

