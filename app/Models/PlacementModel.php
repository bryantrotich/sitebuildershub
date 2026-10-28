<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class PlacementModel extends Model
{
    use HasFactory, HasUuids;
    
    protected $appends = [
        'custom',
        'currency_price'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advert_placements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'custom',
        'name',
        'price',
        'section',
    ];
    

    protected $withCount = ['promotions'];

    public function getCurrencyPriceAttribute(){
        return $this->company->currency." ".$this->price;
    } 
    
    public function getCustomAttribute($value){
        return empty($this->attributes['custom']) ? [ 'height' => 0, 'width' => 0 ] : json_decode($this->attributes['custom']);
    } 


    /**
     * Only placements with an advert running today, loading just those adverts.
     *
     * Constraining the eager load matters: without it every promotion on the
     * placement (expired, upcoming or unpaid) would be returned with it.
     */
    public function scopeFilterPromotion($query, $active=true){
        $current = fn ($sub_query) => $sub_query->current();

        return $query->whereHas('promotions', $current)->with(['promotions' => $current]);
    }   
    
    /**
     * Get the company that the placement belongs to.
     *
     * This relationship is defined by the `company_id` foreign key on the `subscriptions` table,
     * which references the `id` column on the `companies` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CompanyModel>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(
            related: Company::class,
            foreignKey: 'company_id'
        );
    }    

    public function promotions(): HasMany
    {
        return $this->hasMany(
            related: PromotionModel::class,
            foreignKey: 'placement_id'
        );
    }       

    public function postings(): MorphToMany
    {
        return $this->morphedByMany(
            PostingModel::class, 
            'sourceable',
            'invoices',
            'sourceable_id',
            'id',
            'id',
            'id',
        );
    }
}
