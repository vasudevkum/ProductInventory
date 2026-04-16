<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ProductController extends Controller

{
    private $file;

public function __construct()
{
    $this->file = storage_path('Products.json'); 
}

    //  Helper: Read File
    private function readData()
    {
        return json_decode(file_get_contents($this->file), true);
    }

    // Helper: Write File
    private function writeData($data)
    {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    // API 1: CREATE PRODUCT
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $products = $this->readData();

        $newProduct = [
            'id' => count($products) + 1,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity
        ];

        $products[] = $newProduct;

        $this->writeData($products);

        return response()->json($newProduct, 201);
    }

    // API 2: GET PRODUCT
    public function show($id)
    {
        $products = $this->readData();

        foreach ($products as $product) {
            if ($product['id'] == $id) {
                return response()->json($product, 200);
            }
        }

        return response()->json(['message' => 'Product not found'], 404);
    }

    // API 3: UPDATE PRODUCT (PARTIAL)
    public function update(Request $request, $id)
    {
        $products = $this->readData();

        foreach ($products as &$product) {

            if ($product['id'] == $id) {

                $validator = Validator::make($request->all(), [
                    'name' => 'sometimes|string|max:255',
                    'description' => 'sometimes|string',
                    'price' => 'sometimes|numeric|min:0.01',
                    'quantity' => 'sometimes|integer|min:0'
                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                // Partial Update
                if ($request->has('name')) $product['name'] = $request->name;
                if ($request->has('description')) $product['description'] = $request->description;
                if ($request->has('price')) $product['price'] = $request->price;
                if ($request->has('quantity')) $product['quantity'] = $request->quantity;

                $this->writeData($products);

                return response()->json($product, 200);
            }
        }

        return response()->json(['message' => 'Product not found'], 404);
    }
}

