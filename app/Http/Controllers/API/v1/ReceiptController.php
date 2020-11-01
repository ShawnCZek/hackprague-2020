<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Receipt\StoreReceiptRequest;
use App\Models\Company;
use App\Models\Product;
use App\Models\Receipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * Here should be an additional validation of PKP, FIK, and BKP connected
     * to the EET system. Unfortunately, that is not possible right now.
     *
     * @param  StoreReceiptRequest  $request
     * @return JsonResponse
     */
    public function store(StoreReceiptRequest $request): JsonResponse
    {
        $receipt = new Receipt($request->only(['hash', 'custom_text', 'pkp', 'fik', 'bkp']));
        $receipt->setAttribute('paid_at', $request->input('time'));
        $receipt->setAttribute('company_id', Company::whereCode($request->input('company'))->first()->getKey());
        $receipt->save();

        $products = Product::whereIn('code', Arr::pluck($request->input('products'), 'code'))->get();
        foreach ($request->input('products') as $data) {
            /** @var Product $product */
            $product = $products->where('code', $data['code'])->first();
            $receipt->attachProduct($product, $data['quantity'], $data['vat']);
        }

        return response()->json([
            'error' => false,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Receipt  $receipt
     * @return JsonResponse
     */
    public function show(Receipt $receipt): JsonResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Receipt  $receipt
     * @return JsonResponse
     */
    public function update(Request $request, Receipt $receipt): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Receipt  $receipt
     * @return JsonResponse
     */
    public function destroy(Receipt $receipt): JsonResponse
    {
        //
    }
}