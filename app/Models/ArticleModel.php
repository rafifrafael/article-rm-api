<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'articles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['title', 'slug', 'author_id', 'content', 'image', 'category_id', 'views', 'published_on'];

    public function getArticleById($id)
    {
        return $this->select('articles.*, users.username as author_name, categories.name as category_name') // Select desired fields along with category name
            ->join('users', 'users.id = articles.author_id', 'left') // Left join with user table
            ->join('categories', 'categories.id = articles.category_id', 'left') // Left join with categories table
            ->where('articles.id', $id) // Match based on the article's ID
            ->first(); // Since we're expecting only one result.
    }


    public function getAuthors()
    {
        return $this->select('articles.*, users.username as author_name, categories.name as category_name') // Select desired fields
            ->join('users', 'users.id = articles.author_id', 'left') // Left join with user table
            ->join('categories', 'categories.id = articles.category_id', 'left')
            ->findAll();
    }

    public function getTotalArticlesByAuthor($author_id)
    {
        return $this->where('author_id', $author_id)->countAllResults();
    }

    public function getLatestArticleByAuthor($author_id)
    {
        return $this->select('articles.*, categories.name as category_name')
            ->where('author_id', $author_id)
            ->join('categories', 'categories.id = articles.category_id', 'left')
            ->orderBy('id', 'DESC') // or whatever column represents the published date
            ->first();
    }

    public function getAllArticleByAuthor($author_id)
    {
        return $this->select('articles.*, users.username as author_name, categories.name as category_name') // Select desired fields
            ->where('author_id', $author_id)
            ->join('users', 'users.id = articles.author_id', 'left')
            ->join('categories', 'categories.id = articles.category_id', 'left')
            ->findAll();
    }

    public function getArticlesByCategory($category_id)
    {
        return $this->select('articles.*, users.username as author_name, categories.name as category_name')
            ->where('category_id', $category_id)
            ->join('users', 'users.id = articles.author_id', 'left')
            ->join('categories', 'categories.id = articles.category_id', 'left')
            ->findAll();
    }



    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
