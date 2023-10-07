<?php

namespace App\Http\Controllers\Api\System;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\System\User\IndexRequest;
use App\Http\Requests\System\User\StoreRequest;
use App\Http\Requests\System\User\UpdateRequest;
use App\Http\Requests\System\User\DestroyRequest;
use Illuminate\Support\Facades\DB;

// Import models below
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\System\User\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = User::query();

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
     * @param  \App\Http\Requests\System\Permission\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $user->syncRoles($input['role']);

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
     * @param  \App\Http\Requests\System\User\UpdateRequest  $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, User $user)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();

            if ($input['password'] ?? false) {
                $input['password'] = bcrypt($input['password']);
            }

            $user->update($input);
            $user->syncRoles($input['role']);

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
     * @param  \App\Http\Requests\System\User\DestroyRequest  $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, User $user)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            $user->delete();
            DB::commit();
            return json_success_response(200, 'Data berhasil dihapus.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }
}
