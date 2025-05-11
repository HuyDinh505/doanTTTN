<?php

namespace App\Http\Controllers;

use App\Models\DatVe;
use Carbon\Carbon;
use Date;
use GuzzleHttp\Client;
use Http;
use Illuminate\Http\Request;
use Log;

class MoMoPaymentController extends Controller
{
    protected $accessKey = 'F8BBA842ECF85';
    protected $secretKey = 'K951B6PE1waDMi640xX08PD3vg6EkVlz';


    /**
     * @OA\Post(
     *     path="/api/create-payment",
     *     summary="Tạo yêu cầu thanh toán MoMo",
     *     tags={"Thanh toán MoMo"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "orderId"},
     *             @OA\Property(property="amount", type="number", format="float", example=100000),
     *             @OA\Property(property="orderId", type="string", example="VE123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công, trả về thông tin thanh toán",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Payment processed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi trong việc tạo yêu cầu thanh toán",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Không thể tạo yêu cầu thanh toán")
     *         )
     *     )
     * )
     */
    public function createPayment(Request $request)
    {
        $orderInfo = 'pay with MoMo';
        $partnerCode = 'MOMO';
        // $redirectUrl = 'https://datvexemphim-psi.vercel.app/';
        // $ipnUrl = 'https://be-da-web-nam4.onrender.com/api/callback';
        $redirectUrl = 'http://localhost:5173/';
        $ipnUrl = 'https://1c92-115-76-51-135.ngrok-free.app/api/callback';
        $requestType = "payWithMethod";
        $amount = $request->amount;
        $orderId = $request->orderId;
        $requestId = $orderId;
        $extraData = '';
        $orderGroupId = '';
        $autoCapture = true;
        $lang = 'vi';
        $rawSignature = "accessKey=" . $this->accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);
        $requestBody = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'Test',
            'storeId' => 'MomoTestStore',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => $lang,
            'requestType' => $requestType,
            'autoCapture' => $autoCapture,
            'extraData' => $extraData,
            'orderGroupId' => $orderGroupId,
            'signature' => $signature
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://test-payment.momo.vn/v2/gateway/api/create', $requestBody);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Không thể tạo yêu cầu thanh toán'], 500);
    }

    /**
     * @OA\Post(
     *     path="/api/callback",
     *     summary="Callback MoMo khi thanh toán thành công hoặc thất bại",
     *     tags={"Thanh toán MoMo"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"resultCode", "orderId"},
     *             @OA\Property(property="resultCode", type="string", example="0"),
     *             @OA\Property(property="orderId", type="string", example="VE123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thanh toán thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Payment processed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Thanh toán thất bại",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="fail")
     *         )
     *     )
     * )
     */

    public function callback(Request $request)
    {
        if ($request->resultCode == '0') {
            $orderId = $request->orderId;

            $datve = DatVe::where('ma_ve', $orderId)->first();
            if ($datve) {
                $datve->trang_thai = 'Đã thanh toán';
                $datve->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Payment processed successfully']);
        }

        return response()->json(['status' => 'fail'], 400);
    }

    /**
     * @OA\Post(
     *     path="/api/checkTrasaction",
     *     summary="Thông báo IPN từ MoMo về trạng thái thanh toán",
     *     tags={"Thanh toán MoMo"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"orderId"},
     *             @OA\Property(property="orderId", type="string", example="VE123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thanh toán đã được xác nhận",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Payment processed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Thanh toán không thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="fail"),
     *             @OA\Property(property="message", type="string", example="Payment failed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Không thể kiểm tra trạng thái giao dịch",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unable to check transaction status")
     *         )
     *     )
     * )
     */
    public function ipn(Request $request)
    {
        $orderId = $request->input('orderId');
        $requestId = $orderId;

        if (empty($orderId)) {
            return response()->json([
                'error' => 'Missing orderId'
            ], 400);
        }

        $rawSignature = "accessKey={$this->accessKey}" . "&orderId={$orderId}" . "&partnerCode=MOMO" .
            "&requestId={$requestId}";
        $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);
        $requestBody = [
            'partnerCode' => 'MOMO',
            'requestId' => $orderId,
            'orderId' => $orderId,
            'signature' => $signature,
            'lang' => 'vi'
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://test-payment.momo.vn/v2/gateway/api/query', $requestBody);


        if ($response->successful()) {
            $data = $response->json();
            if ($data['resultCode'] == '0') {

                $datve = DatVe::where('ma_ve', $orderId)->first();
                if ($datve) {
                    $datve->trang_thai = 'Đã thanh toán';
                    $datve->save();
                }
                return response()->json(['status' => 'success', 'message' => 'Payment processed successfully']);
            } else {

                return response()->json(['status' => 'fail', 'message' => 'Payment failed'], 400);
            }
        }
        return response()->json(['error' => 'Unable to check transaction status'], 500);
    }
}
