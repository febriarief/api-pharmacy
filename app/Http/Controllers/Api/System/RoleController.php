<?php

namespace App\Http\Controllers\Api\System;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Role\IndexRequest;
use App\Http\Requests\System\Role\StoreRequest;
use App\Http\Requests\System\Role\UpdateRequest;
use App\Http\Requests\System\Role\DestroyRequest;
use Illuminate\Support\Facades\DB;

// Import models below
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\System\Role\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = Role::query();

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
     * @param  \App\Http\Requests\System\Role\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            
            $input['guard_name'] = 'api';
            $role = Role::create($input);

            $permissions = $input['permissions'] ?? [];
            $role->syncPermissions($permissions);

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
     * @param  \App\Http\Requests\System\Role\UpdateRequest  $request
     * @param  \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Role $role)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            
            $input['guard_name'] = 'api';
            $role->update($input);

            $permissions = $input['permissions'] ?? [];
            $role->syncPermissions($permissions);

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
     * @param  \App\Http\Requests\System\Role\DestroyRequest  $request
     * @param  \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Role $role)
    {
        try {
            DB::beginTransaction();
            $role->delete();
            DB::commit();
            return json_success_response(200, 'Data berhasil dihapus.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }
}
