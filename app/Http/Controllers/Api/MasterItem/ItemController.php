<?php

namespace App\Http\Controllers\Api\MasterItem;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterItem\Item\DestroyRequest;
use App\Http\Requests\MasterItem\Item\IndexRequest;
use App\Http\Requests\MasterItem\Item\StoreRequest;
use App\Http\Requests\MasterItem\Item\UpdateRequest;
use Illuminate\Support\Facades\DB;

// Import models below
use App\Models\MasterItem\Item;
use App\Models\MasterItem\Stock;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\MasterItem\Item\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = Item::query();

        $query->when($input['name'] ?? false, function($q) use($input) {
            return $q->where('name', 'like', '%' . $input['name'] . '%');
        });

        $query->when($input['with'] ?? false, function($q) use($input) {
            $relations = explode(',', $input['with']);
            return $q->with($relations);
        });
        
        if ($input['sort'] ?? false) {
            switch($input['sort']) {
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                default:
                    break;        
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        if ($input['page'] ?? false) return json_success_response(200, '', $query->paginate(10));

        return json_success_response(200, '', $query->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MasterItem\Item\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            Item::create($input);            
            DB::commit();
            return json_success_response(201, 'Data berhasil dibuat.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\MasterItem\Item\UpdateRequest  $request
     * @param  \App\Models\MasterItem\Item  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Item $item)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            $item->update($input);
            DB::commit();
            return json_success_response(200, 'Data berhasil disimpan.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\MasterItem\Unit\Destroy  $request
     * @param  \App\Models\MasterItem\Item  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DestroyRequest $request, Item $item)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            $item->delete();
            DB::commit();
            return json_success_response(200, 'Data berhasil dihapus.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }
}
