<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleRepository extends BaseRepository
{
    public function getModel()
    {
        return \Spatie\Permission\Models\Role::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();
        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }
        return $query->paginate($data['size'] ?? 10);
    }

    public function getPermissions()
    {
        return Permission::get()->groupBy('section');
    }

    public function getNameById($ids)
    {
        return $this->model->whereIn('id', $ids)->pluck('name');
    }
    public function createRole(array $data)
    {
        $data['guard_name'] = 'api';
        $role = $this->model::create($data);
        if ($data['permissions']) {
            $role->permissions()->attach($data['permissions']);
        }
        return $role;
    }
    public function showRole($id)
    {
        return $this->model->with('permissions')->findOrFail($id);
    }
    public function deleteRole($id)
    {
        $role = $this->model->findOrFail($id);
        $check = $role->users()->count();
        if ($check > 0) {
            return false;
        }
        $role->delete();
        return true;
    }
    public function updateRole(array $data, $id)
    {
        $role = $this->model->findOrFail($id);
        $role->update($data);
        if ($data['permissions']) {
            $role->permissions()->sync($data['permissions']);
        }
        return $role;
    }
}
