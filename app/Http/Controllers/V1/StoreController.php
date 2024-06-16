<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CreateStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Models\Store;
use App\Repositories\Interfaces\StoreInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    use ApiResponseTrait;
    protected $storeRepository;

    function __construct(StoreInterface $storeInterface)
    {
        $this->storeRepository = $storeInterface;
    }

    /**
     * Display a listing of the stores.
     */
    public function index(Request $request)
    {
        $perPage = 10;

        $filters = $request->query('filters', []); // Get filters from query string (array)
        $user = $request->user();

        $stores = $this->storeRepository->searchFilter($filters, $user);
        $countStore = $stores->count();

        $currentPage = $request->get('page', 1);
        $paginator = $stores->paginate($perPage, ['*'], 'page', $currentPage, $countStore)->withQueryString();

        return $this->sendResponse($paginator, 'List stores');
    }


    /**
     * Store a newly created store.
     */
    public function store(CreateStoreRequest $request)
    {
        $user = $request->user();
        $data = $request->only(['name', 'description']);
        $data['user_id'] = $user->id;

        $store =  Store::create($data);

        return $this->sendResponse($store, 'Store created successfully');
    }

    /**
     * Get info store.
     */
    public function show(string $id, Request $request)
    {
        $user = $request->user();
        $store = $this->storeRepository->findByUser($id, $user);

        if (!$store) {
            return $this->sendError('Store not found', [], 404);
        }

        return $this->sendResponse($store, 'Success');
    }


    /**
     * Update the store.
     */
    public function update(UpdateStoreRequest $request, string $id)
    {
        $user = $request->user();
        $store = $this->storeRepository->findByUser($id, $user);
        $data = $request->only(['name', 'description']);

        if (!$store) {
            return $this->sendError('Store not found', [], 404);
        }

        $store->fill($data);
        $store->save();

        return $this->sendResponse($store, 'Store updated successfully');
    }

    /**
     * Remove the stores and product in store.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $store = $this->storeRepository->findByUser($id, $user);

        if (!$store) {
            return $this->sendError('Store not found', [], 404);
        }

        $store->delete();

        return $this->sendResponse($store, 'Store deleted successfully and all products in this store have been deleted');
    }

    
}
