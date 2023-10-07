<?php

namespace App\Http\Controllers\Api\System;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Permission\DestroyRequest;
use App\Http\Requests\System\Permission\IndexRequest;
use App\Http\Requests\System\Permission\StoreRequest;
use App\Http\Requests\System\Permission\UpdateRequest;
use Illuminate\Support\Facades\DB;

// Import models below
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\System\Permission\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = Permission::query();

        $query->when($input['name'] ?? false, function($q) use($input) {
            return $q->where('name', 'like', '%' . $input['name'] . '%');
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
     * @param  \App\Http\Requests\System\Permission\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            $input['guard_name'] = 'api';
            Permission::create($input);
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
     * @param  \App\Http\Requests\System\Permission\UpdateRequest  $request
     * @param  \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Permission $permission)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            $input['guard_name'] = 'api';
            $permission->update($input);
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
     * @param  \App\Http\Requests\System\Permission\DestroyRequest  $request
     * @param  \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Permission $permission)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            $permission->delete();
            DB::commit();
            return json_success_response(200, 'Data berhasil dihapus.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }

    /**
     * Get list of parsed data of permission
     *
     * @param  \App\Http\Requests\System\Permission\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function parsedPermission(IndexRequest $request)
    {
        $input = $request->validated();
        $model = Permission::orderBy('name', 'asc')->get();

        $permissions = [];

        foreach($model as $permission) {
            $explodedPermission = explode('.', $permission['name']);

            $title    = camel_to_human_case($explodedPermission[0]);
            $subtitle = camel_to_human_case($explodedPermission[1]);
            $id       = $permission['id'];
            $fullname = $permission['name'];
            $value    = $explodedPermission[2];

            if (!array_key_exists($title, $permissions)) {
                $permissions[$title] = [];
            }

            if (!array_key_exists($subtitle, $permissions[$title])) {
                $permissions[$title][$subtitle] = [];
            }

            if (!in_array($value, $permissions[$title][$subtitle])) {
                $permissions[$title][$subtitle][] = [
                    'id'        => $id,
                    'fullname'  => $fullname,
                    'value'     => $value
                ];
            }
        }


        return json_success_response(200, '', $permissions);
    }
}
