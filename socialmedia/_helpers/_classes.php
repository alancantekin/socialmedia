<!-- the classes are structured the same as their tables in the database -->

<?php
    class UserModel { // table user
        public $id;
        public $username;
        public $email;
        public $password;
        public $date_created;
        public $date_modified;
    }

    class PostModel { // table post
        public $id;
        public $user_id;
        public $caption;
        public $image_name;
        public $image;
        public $date_created;
        public $date_modified;
    }

    class CommentModel { // table comment
        public $id;
        public $user_id;
        public $post_id;
        public $text;
        public $date_created;
    }
?>