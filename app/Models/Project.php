<?php

namespace App\Models;

use App\Models\FundingSource;
use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    use ActivityLogOptionsTrait;
    protected $fillable = [
        'funding_source_id',
        'tenderer_id',
        'investor_id',
        'staff_id',
        'selection_method_id',
        'parent_id',
        'decision_number_issued',
        'name',
        'is_domestic',
        'location',
        'amount',
        'total_amount',
        'description',
        'submission_method',
        'receiving_place',
        'bid_submission_start',
        'bid_submission_end',
        'bid_opening_date',
        'start_time',
        'end_time',
        'approve_at',
        'decision_number_approve',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(Project::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Project::class, 'parent_id');
    }
    public function fundingSource()
    {
        return $this->belongsTo(FundingSource::class, 'funding_source_id');
    }

    public function tenderer()
    {
        return $this->belongsTo(Enterprise::class, 'tenderer_id');
    }

    public function investor()
    {
        return $this->belongsTo(Enterprise::class, 'investor_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
    public function selectionMethod()
    {
        return $this->belongsTo(SelectionMethod::class, 'selection_method_id');
    }
    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'project_industry','project_id','industry_id');
    }

    public function procurementCategories(){
        return $this->belongsToMany(ProcurementCategory::class, 'project_procurement', 'project_id', 'procurement_id');
    }

    public function evaluationCriterias()
    {
        return $this->hasMany(EvaluationCriteria::class);
    }

    public function bidBond(){
        return $this->hasOne(BidBond::class);
    }

    public function feedbackComplaints(){
        return $this->hasMany(FeedbackComplaint::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function biddingResult()
    {
        return $this->hasOne(BiddingResult::class);
    }

    public function biddingDocument()
    {
        return $this->hasOne(BidDocument::class);
    }

    protected function getModelName(): string
    {
        return 'Dự án - Project';
    }

    public function attachments(){
        return $this->hasMany(Attachment::class);
    }
    public function evaluate()
    {
        return $this->hasOne(Evaluate::class);
    }
    protected function getLogAttributes(): array
    {
        return [
            'funding_source_id',
            'tenderer',
            'investor',
            'staff_id',
            'selection_method_id',
            'parent_id',
            'decision_number_issued',
            'name',
            'is_domestic',
            'location',
            'amount',
            'total_amount',
            'description',
            'submission_method',
            'receiving_place',
            'bid_submission_start',
            'bid_submission_end',
            'bid_opening_date',
            'start_time',
            'end_time',
            'approve_at',
            'decision_number_approve',
            'status',
        ];
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }
}
