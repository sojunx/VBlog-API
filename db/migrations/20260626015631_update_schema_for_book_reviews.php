<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateSchemaForBookReviews extends AbstractMigration {
    public function up(): void {
        // 1. Drop category_posts table first as it has foreign keys pointing to posts and categories
        if ($this->hasTable('category_posts')) {
            $this->table('category_posts')->drop()->save();
        }

        // 2. Create books table
        $booksTable = $this->table('books', ['signed' => false]);
        $booksTable
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('author', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('isbn', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('cover_image_url', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['isbn'], ['unique' => true])
            ->create();

        // 3. Create category_book table
        $categoryBookTable = $this->table('category_book', ['id' => false, 'primary_key' => ['category_id', 'book_id']]);
        $categoryBookTable
            ->addColumn('category_id', 'integer', ['signed' => false])
            ->addColumn('book_id', 'integer', ['signed' => false])
            ->addForeignKey('category_id', 'categories', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_category_book_category_id'
            ])
            ->addForeignKey('book_id', 'books', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_category_book_book_id'
            ])
            ->create();

        // 4. Update categories table (rename name to title, add slug)
        $categoriesTable = $this->table('categories');
        $categoriesTable
            ->renameColumn('name', 'title')
            ->addColumn('slug', 'string', ['limit' => 255, 'null' => false])
            ->addIndex(['slug'], ['unique' => true])
            ->update();

        // 5. Update posts table
        // Change id type to biginteger (BIGINT UNSIGNED) and add new columns
        $postsTable = $this->table('posts');
        $postsTable
            ->changeColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('book_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('summary', 'text', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 50, 'default' => 'draft', 'null' => false])
            ->addColumn('view_count', 'integer', ['default' => 0, 'null' => false])
            ->addColumn('published_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addIndex(['slug'], ['unique' => true])
            ->addForeignKey('book_id', 'books', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_posts_book_id'
            ])
            ->update();

        // 6. Create comments table
        $commentsTable = $this->table('comments', ['signed' => false]);
        $commentsTable
            ->addColumn('post_id', 'biginteger', ['signed' => false, 'null' => false])
            ->addColumn('user_id', 'string', ['limit' => 36, 'null' => true])
            ->addColumn('content', 'text', ['null' => false])
            ->addColumn('rating', 'integer', ['null' => true]) // Post rating, 1 to 5 stars
            ->addColumn('status', 'string', ['limit' => 50, 'default' => 'published', 'null' => false])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_comments_post_id'
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_comments_user_id'
            ])
            ->create();
    }

    public function down(): void {
        // 1. Drop comments table
        if ($this->hasTable('comments')) {
            $this->table('comments')->drop()->save();
        }

        // 2. Rollback posts table changes
        $postsTable = $this->table('posts');
        if ($postsTable->hasForeignKey('book_id')) {
            $postsTable->dropForeignKey('book_id')->save();
        }
        $postsTable
            ->removeIndex(['slug'])
            ->removeColumn('book_id')
            ->removeColumn('slug')
            ->removeColumn('summary')
            ->removeColumn('status')
            ->removeColumn('view_count')
            ->removeColumn('published_at')
            ->removeColumn('deleted_at')
            ->changeColumn('id', 'integer', ['identity' => true, 'signed' => false])
            ->update();

        // 3. Rollback categories table changes
        $categoriesTable = $this->table('categories');
        $categoriesTable
            ->removeIndex(['slug'])
            ->removeColumn('slug')
            ->renameColumn('title', 'name')
            ->update();

        // 4. Drop category_book table
        if ($this->hasTable('category_book')) {
            $this->table('category_book')->drop()->save();
        }

        // 5. Drop books table
        if ($this->hasTable('books')) {
            $this->table('books')->drop()->save();
        }

        // 6. Recreate category_posts table
        $categoryPostsTable = $this->table('category_posts', ['id' => false, 'primary_key' => ['category_id', 'post_id']]);
        $categoryPostsTable
            ->addColumn('category_id', 'integer', ['signed' => false])
            ->addColumn('post_id', 'integer', ['signed' => false])
            ->addForeignKey('category_id', 'categories', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_category_posts_category_id'
            ])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_category_posts_post_id'
            ])
            ->create();
    }
}
