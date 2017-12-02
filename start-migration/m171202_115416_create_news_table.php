<?php

use yii\db\Migration;

/**
 * Handles the creation of table `news`.
 */
class m171202_115416_create_news_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(50)->notNull(),
            'first_name' => $this->string(50),
            'last_name' => $this->string(50),
            'mail' => $this->string(50),
            'password' => $this->string(),
            'authKey' => $this->string(),
            'accessToken' => $this->string(),
        ]);
        
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'category_id' => $this->integer(11)->notNull(),
            'date_create' => $this->integer(50)->notNull(),
            'body' => $this->text(),
            'frontpage' => $this->integer(2),
            'count' => $this->integer()->notNull(),
            'alias' => $this->string(),
            'user_id' => $this->integer(11)->notNull(),
        ]);
        
        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'parent_id' => $this->integer(11)->notNull(),
            'weight' => $this->integer(11)->notNull(),
            'description' => $this->text(),
            'alias' => $this->string(),
        ]);
        
        $this->createIndex(
            'user_id',
            'article',
            'user_id'
        );
        
        $this->addForeignKey(
            'FK_article_user',
            'article',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
        
        $this->createIndex(
            'category_id',
            'article',
            'category_id'
        );
        
        $this->addForeignKey(
            'FK_article_category',
            'article',
            'category_id',
            'category',
            'id',
            'CASCADE'
        );
        
        $this->createIndex(
            'parent_id',
            'category',
            'parent_id'
        );
        
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
        $this->dropTable('article'); 
        $this->dropTable('category');
		
		$this->dropIndex(
            'user_id',
            'article'
        );
        
        $this->dropForeignKey(
            'FK_article_user',
            'article'
        );
        
        $this->dropIndex(
            'category_id',
            'article'
        );
        
        $this->dropForeignKey(
            'FK_article_category',
            'article'
        );
        
        $this->dropIndex(
            'parent_id',
            'category'
        );

    }
}
