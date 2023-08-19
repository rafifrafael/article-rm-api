<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Category extends ResourceController
{

    use ResponseTrait;

    protected $modelName = 'App\Models\CategoryModel';
    protected $format    = 'json';
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $category = $this->model->find($id);
        if ($category) {
            return $this->respond($category);
        }
        return $this->failNotFound('No category found with ID ' . $id);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $category = $this->request->getPost();
        if ($this->model->insert($category)) {
            return $this->respondCreated($category, 'Category created successfully');
        }
        return $this->failServerError('Failed to create category');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $data = $this->request->getPost();
        if ($this->model->update($id, $data)) {
            return $this->respondUpdated($data, 'Category updated successfully');
        }
        return $this->failServerError('Failed to update category');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['id' => $id], 'Category deleted successfully');
        }
        return $this->failServerError('Failed to delete category');
    }
}
