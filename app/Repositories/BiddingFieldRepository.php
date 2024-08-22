<?php

namespace App\Repositories;

class BiddingFieldRepository extends BaseRepository
{
    // Biến tĩnh để lưu trạng thái kiểm tra
    protected static $specialRecordCreated = false;

    /**
     * Initialize the repository and create the special record if needed.
     */
    public function __construct()
    {
        parent::__construct();

        // Chỉ chạy phương thức nếu chưa được chạy trước đó
        if (!self::$specialRecordCreated) {
            $this->createSpecialRecordIfNotExists();
            self::$specialRecordCreated = true;
        }
    }

    public function getModel()
    {
        return \App\Models\BiddingField::class;
    }

    /**
     * Create the special parent record if it does not exist.
     */
    private function createSpecialRecordIfNotExists()
    {
        $exists = $this->model->where('id', 1)->exists();

        if (!$exists) {
            $this->model->create([
                'id' => 1,
                'name' => 'Chưa phân loại',
                'description' => 'Danh mục "Chưa phân loại" đại diện cho các mục chưa được phân loại vào nhóm cụ thể nào. Danh mục đặc biệt không thể xóa.',
                'code' => 0,
                'is_active' => true,
            ]);
        }
    }


    public function filter($data)
    {
        $query = $this->model->query();
        // Loại bỏ bản ghi có id = 1
        $query->where('id', '!=', 1);

        if (isset($data['name'])) {
            $query->where('name', 'like', '%'.$data['name'].'%');
        }

        if (isset($data['code'])) {
            $query->where('code', $data['code']);
        }

        if (isset($data['parent_id'])) {
            $query->where('parent_id', $data['parent_id']);
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }


    public function getAllBiddingFieldIds()
    {
        return $this->model
            ->where('is_active', true)
            ->get(['id', 'name', 'parent_id'])
            ->toArray();
    }


    public function findBiddingFieldById($id)
    {
        // Lấy mô hình từ instance của Repository
        $biddingField = $this->model->with(['children', 'parent'])->find($id);

        if ($biddingField) {
            // Chuyển đổi danh sách con thành mảng
            if ($biddingField->children) {
                $biddingField->children = $biddingField->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                    ];
                });
            }
        }

        return $biddingField;
    }

    public function delete($id)
    {
        $biddingField = $this->model->find($id);

        if ($biddingField->id == 1) {
            return false;
        }

        // Cập nhật các bản ghi khác có `parent_id` trùng với ID của bản ghi cần xóa
        $this->model->where('parent_id', $biddingField->id)
            ->update(['parent_id' => 1]); // Chuyển về danh mục "Chưa phân loại"

        return $biddingField->delete();
    }


}
