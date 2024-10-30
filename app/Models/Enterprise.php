<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Enterprise extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    use ActivityLogOptionsTrait;

    protected $fillable = [
        'user_id',
        'representative',
        'avatar',
        'phone',
        'address',
        'website',
        'description',
        'establish_date',
        'avg_document_rating',
        'registration_date',
        'registration_number',
        'organization_type',
        'is_active',
        'is_blacklist',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function industries()
    {
        return $this->belongsToMany(Industry::class);
    }

    public function bidBonds(){
        return $this->hasMany(BidBond::class);
    }

    protected function getModelName(): string
    {
        return 'Doanh nghiệp - Enterprise';
    }

    protected function employees()
    {
        return $this->hasMany(Employee::class);
    }

    protected function investorProjects() {
        return $this->hasMany(Project::class, 'investor_id');
    }

    protected function tendererProjects() {
        return $this->hasMany(Project::class, 'tenderer_id');
    }

    protected function biddingResults()
    {
        return $this->hasMany(BiddingResult::class);
    }

    protected function getLogAttributes(): array
    {
        return [
            'user_id',
            'representative',
            'industries',
            'avatar',
            'phone',
            'address',
            'website',
            'description',
            'establish_date',
            'avg_document_rating',
            'registration_date',
            'registration_number',
            'organization_type',
            'is_active',
            'is_blacklist',
        ];
    }

    public function getEnterpriseNameByUserId($userId)
    {
        $user = $this->user()->where('id', $userId)->first();
        return $user ? $user->name : '';
    }

    protected function getFieldName(): string
    {
        return $this->getEnterpriseNameByUserId($this->user_id);
    }
}
