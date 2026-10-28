<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PromotionModel extends Model
{
    use HasFactory, HasUuids;

    protected $appends = [
        'active',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promotions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'amount',
        'image',
        'date_from',
        'date_to',
        'placement_id',
        'posting_id',
    ]; 

        /**
     * Accessor to convert the stored images from JSON to a collection.
     * 
     * The images are stored in the database as a JSON string. This accessor
     * will convert the JSON string to a collection of image paths.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getImageAttribute()
    {
        return asset('storage/images/' . $this->attributes['image']);
    }

    public function getActiveAttribute(){
        return !is_null($this->transaction) && $this->transaction->status == 200 ? true : false;
    }    
    
    /**
     * Get all transactions for the order.
     */
    public function invoices(): MorphOne
    {
        return $this->morphOne(InvoiceModel::class, 'sourcable');
    }  
    
    /**
     * Get the company that the placement belongs to.
     *
     * This relationship is defined by the `company_id` foreign key on the `subscriptions` table,
     * which references the `id` column on the `companies` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\PlacementModel>
     */
    public function placement(): BelongsTo
    {
        return $this->belongsTo(
            related: PlacementModel::class,
            foreignKey: 'placement_id'
        );
    }   

    /**
     * Get the company that the placement belongs to.
     *
     * This relationship is defined by the `company_id` foreign key on the `subscriptions` table,
     * which references the `id` column on the `companies` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\PostingModel>
     */
    public function posting(): BelongsTo
    {
        return $this->belongsTo(
            related: PostingModel::class,
            foreignKey: 'posting_id'
        );
    }   

    /**
     * Scope to paid promotions whose date range includes today.
     */
    public function scopeCurrent($query)
    {
        $today = now()->format('Y-m-d');

        return $query->whereHas('invoice', fn ($sub_query) => $sub_query->where('status','paid'))
            ->whereDate('date_from','<=',$today)
            ->whereDate('date_to','>=',$today);
    }

    /**
     * Get the transaction that the promotion belongs to.
     *
     * This relationship is defined by the `targatable_id` foreign key on the `promotions` table,
     * which references the `id` column on the `transactions` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\TransactionModel>
     */
    public function invoice(): MorphOne
    {
        // The morphFrom relationship is used to define the relationship between the promotion
        // and the transaction that it belongs to. The morphFrom relationship is a polymorphic
        // relationship that is used to define a relationship between two models that are
        // not directly related. In this case, the promotion model is related to the
        // transaction model using the `targatable_id` foreign key, which references the
        // `id` column on the `transactions` table.
        return $this->morphOne(InvoiceModel::class,'targetable');
    }       
}
