<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Introduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'introduction',
        'is_use'
    ];
    //   // Kích hoạt bản ghi
    //   public function activate()
    //   {
    //       $this->is_use = true;
    //       $this->save();
    //   }
  
    //   // Vô hiệu hóa bản ghi
    //   public function deactivate()
    //   {
    //       $this->is_use = false;
    //       $this->save();
    //   }
  
    //   // Scope để lấy các bản ghi đang hoạt động
    //   public function scopeActive($query)
    //   {
    //       return $query->where('is_use', true);
    //   }
  
    //   // Phương thức để định dạng bản giới thiệu
    //   public function getFormattedIntroductionAttribute()
    //   {
    //       return ucfirst($this->introduction);
    //   }
    protected function getModelName(): string
    {
        return 'Giới thiệu - Introduction';
    }

    protected function getLogAttributes(): array
    {
        return [
            'introduction',
            'is_use'
        ];
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }
  }