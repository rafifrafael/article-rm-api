<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Article extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\ArticleModel';
    protected $format    = 'json';

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        return $this->respond($this->model->getAuthors());
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $data = $this->model->getArticleById($id);
        if ($data) {
            return $this->respond($data);
        }
        return $this->failNotFound('Artikel tidak ditemukan dengan ID ' . $id);
    }



    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        // Typically not used with APIs, can be removed if not needed
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $validation = \Config\Services::validation();
        $rules = [
            'image' => [
                'uploaded[image]',
                'mime_in[image,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[image,4096]', // 4MB
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->fail($validation->getErrors());
        }

        $file = $this->request->getFile('image');
        $fileName = "";

        if ($file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $path = FCPATH . 'uploads';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $file->move($path, $fileName);
        } else {
            return $this->fail($file->getErrorString() . ' (' . $file->getError() . ')');
        }

        // Fetch all POST data
        $data = $this->request->getPost();

        // Add the image filename to the data to be inserted into the DB
        $data['image'] = $fileName;

        // Insert the data into the database
        if ($this->model->insert($data)) {
            return $this->respondCreated($data, 'Artikel berhasil dibuat');
        }

        return $this->failServerError('Terjadi kesalahan saat mencoba membuat artikel');
    }


    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        // Typically not used with APIs, can be removed if not needed
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $validation = \Config\Services::validation();

        // Get non-file post data
        $data = $this->request->getPost();

        // Fetch existing article data
        $existingArticle = $this->model->find($id);

        // Check for uploaded image and handle it
        $image = $this->request->getFile('image');

        if ($image && !$image->hasMoved() && $image->isValid()) {
            $imageRules = [
                'image' => [
                    'uploaded[image]',
                    'mime_in[image,image/jpg,image/jpeg,image/gif,image/png]',
                    'max_size[image,4096]' // 4MB
                ],
            ];

            if (!$this->validate($imageRules)) {
                return $this->fail($validation->getErrors());
            }

            // Define a unique name for the file and move it
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/', $newName);

            // If the existing image filename doesn't match the new one, delete the old image
            if ($existingArticle && $existingArticle['image'] !== $newName) {
                $oldImagePath = FCPATH . 'uploads/' . $existingArticle['image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Update the 'image' field in data with the new file name
            $data['image'] = $newName;
        }

        if ($this->model->update($id, $data)) {
            return $this->respondUpdated($data, 'Artikel berhasil diperbarui');
        }

        $errors = $this->model->errors();
        if ($errors) {
            return $this->fail($errors);
        }

        return $this->failServerError('Terjadi kesalahan saat mencoba memperbarui artikel');
    }


    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        if ($id == null) {
            return $this->failValidationError('No ID provided.');
        }

        // Fetch the article data by its ID to get the associated image filename
        $article = $this->model->find($id);

        if (!$article) {
            return $this->failNotFound('Article not found.');
        }

        // Check if the article and the associated image exists
        if (isset($article['image']) && !empty($article['image'])) {
            $imagePath = FCPATH . 'uploads/' . $article['image'];

            if (is_file($imagePath)) {
                unlink($imagePath);  // Delete the file from the server
            }
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['id' => $id], 'Article deleted successfully.');
        }

        return $this->failServerError('There was a problem deleting the article.');
    }


    public function getAuthorArticleDetails($author_id)
    {
        $articleModel = new \App\Models\ArticleModel();

        $totalArticles = $articleModel->getTotalArticlesByAuthor($author_id);
        $latestArticle = $articleModel->getLatestArticleByAuthor($author_id);
        $allArticle = $articleModel->getAllArticleByAuthor($author_id);

        return $this->respond([
            'total_articles' => $totalArticles,
            'latest_article' => $latestArticle,
            'all_article' => $allArticle,
        ]);
    }

    public function getArticleCategory($category_id)
    {
        $articleModel = new \App\Models\ArticleModel();

        $articles = $articleModel->getArticlesByCategory($category_id);

        if (!$articles) {
            // handle not found scenario
            return $this->failNotFound('No articles found for the given category.');
        }

        return $this->respond($articles);
    }


    public function viewArticle($id = null)
    {
        $articleModel = new \App\Models\ArticleModel();
        // Fetch the article by ID
        $article = $articleModel->getArticleById($id);

        if (!$article) {
            return $this->failNotFound('Article not found');
        }

        // Increment the views
        $article['views'] += 1;

        $this->model->save($article);

        // Return the article data
        return $this->respond(['message' => 'Views Updated', 'data' => $article]);
    }
}
