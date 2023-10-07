<?php

namespace App\Http\Controllers\Api\MasterService;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterService\Service\DestroyRequest;
use App\Http\Requests\MasterService\Service\IndexRequest;
use App\Http\Requests\MasterService\Service\StoreRequest;
use App\Http\Requests\MasterService\Service\UpdateRequest;
use Illuminate\Support\Facades\DB;

// Import models below
use App\Models\MasterService\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\MasterService\Service\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = Service::query();

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
     * @param  \App\Http\Requests\MasterService\Service\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            Service::create($input);            
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
     * @param  \App\Http\Requests\MasterService\Service\UpdateRequest  $request
     * @param  \App\Models\MasterService\Service  $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Service $service)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            $service->update($input);
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
     * @param  \App\Http\Requests\MasterService\Service\Destroy  $request
     * @param  \App\Models\MasterService\Service  $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DestroyRequest $request, Service $service)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            $service->delete();
            DB::commit();
            return json_success_response(200, 'Data berhasil dihapus.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }
}
