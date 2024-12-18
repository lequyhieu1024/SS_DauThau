<?php

namespace App\Repositories;


use App\Enums\ProjectStatus;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = app()->make($this->getModel());
    }

    abstract public function getModel();

    public function getNameAndIds()
    {
        return $this->model->select('id', 'name')->orderBy('id', 'DESC')->get();
    }

    public function getNameAndIdTreeSelect() {
        return $this->model->select('id', 'name')->get();
    }

    public function getAll($data)
    {
        return $this->model->paginate($data['size'] ?? 10);
    }

    public function getAllNotPaginate()
    {
        return $this->model->orderBy('id', 'DESC')->get();
    }

    public function getNameAndIdsActive() {
        return $this->model->select('id', 'name')->where('is_active', 1)->orderBy('id', 'DESC')->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function getOneBy($field, $value)
    {
        return $this->model->where($field,$value)->first();
    }

    public function getBy($field, $value)
    {
        return $this->model->where($field, $value)->get();
    }

    public function findWhereIn($field, array $values)
    {
        return $this->model->whereIn($field, $values)->get();
    }

    public function findWhereInModel($field, array $values)
    {
        return $this->model->whereIn($field, $values);
    }

    public function firstWhere($column, $value) {
        return $this->model->where($column, $value)->first();
    }


    public function update(array $data, $id)
    {
        $student = $this->model->findOrFail($id);
        return $student->update($data);
    }

    public function toggleStatus($id)
    {
        $model = $this->model->findOrFail($id);

        $model->update(['is_active' => !$model->is_active]);

        return $model;
    }

    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function deletes($ids)
    {
        if (is_array($ids)) {
            return $this->model->destroy($ids);
        }
        return $this->model->destroy([$ids]);
    }
}
