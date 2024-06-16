<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Repositories\Interfaces\ProductInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;
    protected $productRepository;

    function __construct(ProductInterface $productInterface)
    {
        $this->productRepository = $productInterface;
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $perPage = 10;

        $filters = $request->query('filters', []); // Get filters from query string (array)
        $user = $request->user();

        $products = $this->productRepository->searchFilter($filters, $user);
        $countProduct = $products->count();

        $currentPage = $request->get('page', 1);
        $paginator = $products->paginate($perPage, ['*'], 'page', $currentPage, $countProduct)->withQueryString();

        return $this->sendResponse($paginator, 'List products');
    }


    /**
     * Store a newly product.
     */
    public function store(CreateProductRequest $request)
    {
        $user = $request->user();
        $data = $request->only(['name', 'description', 'amount', 'price', 'store_id']);
        // $data['user_id'] = $user->id;

        $product = Product::create($data);

        return $this->sendResponse($product, 'Product created successfully', 201);
    }

    /**
     * Get info product
     */
    public function show(string $id, Request $request)
    {
        $user = $request->user();
        $product = $this->productRepository->findByUser($id, $user);

        if (!$product) {
            return $this->sendError('Product not found', [], 404);
        }

        $product->makeHidden('laravel_through_key');

        return $this->sendResponse($product, 'Success');
    }


    /**
     * Update the product.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $user = $request->user();
        $product = $this->productRepository->findByUser($id, $user);
        $data = $request->only(['name', 'description', 'amount', 'price', 'store_id']);

        if (!$product) {
            return $this->sendError('Product not found', [], 404);
        }

        $product->fill($data);
        $product->save();
        $product->makeHidden('laravel_through_key');

        return $this->sendResponse($product, 'Product updated successfully');
    }

    /**
     * Remove the product
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $product = $this->productRepository->findByUser($id, $user);

        if (!$product) {
            return $this->sendError('Product not found', [], 404);
        }

        $product->delete();
        $product->makeHidden('laravel_through_key');

        return $this->sendResponse($product, 'Product deleted successfully');
    }
}
