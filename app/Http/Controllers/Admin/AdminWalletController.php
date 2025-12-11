<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentDetail;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminWalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display wallet dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $result = $this->walletService->getWalletDetails($user);

        if (! $result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        $data = $result['data'];

        // Get paginated transactions for the view
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->with('paymentDetail')
            ->orderByDesc('created_at')
            ->paginate(15);

        $paymentDetails = PaymentDetail::where('user_id', $user->id)->get();

        return view('pages.admin.wallet.index', [
            'wallet' => $data['wallet'],
            'transactions' => $transactions,
            'paymentDetails' => $paymentDetails,
        ]);
    }

    /**
     * Show add money form
     */
    public function showAddMoneyForm()
    {
        $user = auth()->user();
        $paymentDetails = PaymentDetail::where('user_id', $user->id)->get();

        return view('pages.admin.wallet.add-money', [
            'paymentDetails' => $paymentDetails,
            'currentBalance' => (float) $user->wallet_balance,
        ]);
    }

    /**
     * Process add money transaction
     */
    public function addMoney(Request $request)
    {
        $user = auth()->user();

        // Validate request
        $validator = Validator::make($request->all(), [

            // AMOUNT
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:100000',
                'regex:/^\d+(\.\d{1,2})?$/', // allows max 2 decimals
            ],

            // OPTIONAL REASON
            'reason' => [
                'nullable',
                'string',
                'max:100',
                // prevents only symbols
                'regex:/^[A-Za-z0-9 ,.\-()]+$/',
            ],

            // Payment type
            'pay_type' => 'required|in:bank,upi',

            // BANK DETAILS (strict)
            'pay_bank_name' => [
                'required_if:pay_type,bank',
                'nullable',
                'string',
                'max:150',
                'regex:/^[A-Za-z ]+$/', // alphabets + spaces
            ],

            'pay_bank_account_number' => [
                'required_if:pay_type,bank',
                'nullable',
                'digits_between:6,18',
                'regex:/^[0-9]+$/',
            ],

            'pay_bank_ifsc' => [
                'required_if:pay_type,bank',
                'nullable',
                'string',
                'max:11',
                // official IFSC format
                'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            ],

            // UPI DETAILS (strict)
            'pay_upi_id' => [
                'required_if:pay_type,upi',
                'nullable',
                'string',
                'max:100',
                // valid UPI pattern
                'regex:/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/',
            ],

            // ACCOUNT HOLDER NAME (no numbers)
            'account_holder_name' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^[A-Za-z ]+$/',
            ],

        ], [

            // Custom error messages
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be at least ₹1.',
            'amount.max' => 'Amount cannot exceed ₹100,000.',
            'amount.regex' => 'Amount can have a maximum of 2 decimal places.',

            'reason.regex' => 'Reason may contain only letters, numbers, spaces, commas, dashes, periods.',

            'pay_type.required' => 'Payment type is required.',

            'pay_bank_name.required_if' => 'Bank name is required for bank transfers.',
            'pay_bank_name.regex' => 'Bank name must contain only alphabets.',

            'pay_bank_account_number.required_if' => 'Account number is required for bank transfers.',
            'pay_bank_account_number.digits_between' => 'Account number must be between 6 and 18 digits.',
            'pay_bank_account_number.regex' => 'Account number must contain only numbers.',

            'pay_bank_ifsc.required_if' => 'IFSC code is required for bank transfers.',
            'pay_bank_ifsc.regex' => 'Invalid IFSC code format. Example: HDFC0001234.',

            'pay_upi_id.required_if' => 'UPI ID is required for UPI payments.',
            'pay_upi_id.regex' => 'Invalid UPI ID format. Example: name@bank.',

            'account_holder_name.regex' => 'Account holder name must contain only alphabets.',

        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['type'] = 'in';

        $result = $this->walletService->processTransaction($user, $data);

        if ($result['success']) {
            return redirect()->route('admin.wallet.index')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->with('error', $result['message'])
            ->withInput();
    }

    /**
     * Show withdraw form
     */
    public function showWithdrawForm()
    {
        $user = auth()->user();
        $paymentDetails = PaymentDetail::where('user_id', $user->id)->get();

        return view('pages.admin.wallet.withdraw', [
            'paymentDetails' => $paymentDetails,
            'currentBalance' => (float) $user->wallet_balance,
        ]);
    }

    /**
     * Process withdrawal
     */
    public function withdraw(Request $request)
    {
        $user = auth()->user();

        // Validate request
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1|max:100000',
            'reason' => 'nullable|string|max:100',
            'payment_detail_id' => 'required|exists:payment_details,id',
        ], [
            'amount.required' => 'Amount is required.',
            'amount.min' => 'Amount must be at least ₹1.',
            'amount.max' => 'Amount cannot exceed ₹100,000.',
            'payment_detail_id.required' => 'Please select a payment method.',
            'payment_detail_id.exists' => 'Selected payment method is invalid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['type'] = 'out';

        $result = $this->walletService->processTransaction($user, $data);

        if ($result['success']) {
            return redirect()->route('admin.wallet.index')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->with('error', $result['message'])
            ->withInput();
    }

    /**
     * Show payment details management
     */
    public function paymentDetails()
    {
        $user = auth()->user();
        $paymentDetails = PaymentDetail::where('user_id', $user->id)->get();

        return view('pages.admin.wallet.payment-details', [
            'paymentDetails' => $paymentDetails,
        ]);
    }

    /**
     * Show add payment detail form
     */
    public function showAddPaymentDetailForm()
    {
        return view('pages.admin.wallet.add-payment-detail');
    }

    /**
     * Store new payment detail
     */
    public function storePaymentDetail(Request $request)
    {
        $user = auth()->user();

        // Validate request
        $validator = Validator::make($request->all(), [

            // Payment Type
            'pay_type' => 'required|in:bank,upi',

            // BANK DETAILS (Visible only when pay_type = bank)
            'pay_bank_name' => [
                'required_if:pay_type,bank',
                'nullable',
                'string',
                'max:150',
                'regex:/^[A-Za-z ]+$/',    // only alphabets + spaces
            ],

            'pay_bank_account_number' => [
                'required_if:pay_type,bank',
                'nullable',
                'digits_between:6,18',      // safer than string (prevents chars)
                'regex:/^[0-9]+$/',         // strictly numbers only
            ],

            'pay_bank_ifsc' => [
                'required_if:pay_type,bank',
                'nullable',
                'string',
                'max:11',
                // IFSC format: 4 letters + 0 + 6 digits (RBIS0XXXXXX)
                'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            ],

            // UPI DETAILS (Visible only when pay_type = upi)
            'pay_upi_id' => [
                'required_if:pay_type,upi',
                'nullable',
                'string',
                'max:100',
                // UPI ID format: name@bank (supports ., -, _ )
                'regex:/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/',
            ],

            // ACCOUNT HOLDER NAME (Strict - no numbers)
            'account_holder_name' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^[A-Za-z ]+$/',    // strict alphabets + spaces only
            ],

        ], [

            // Custom Messages
            'pay_type.required' => 'Payment type is required.',

            'pay_bank_name.required_if' => 'Bank name is required for bank transfers.',
            'pay_bank_name.regex' => 'Bank name must contain only alphabets.',

            'pay_bank_account_number.required_if' => 'Account number is required for bank transfers.',
            'pay_bank_account_number.digits_between' => 'Account number must be between 6 and 18 digits.',
            'pay_bank_account_number.regex' => 'Account number must contain only numbers.',

            'pay_bank_ifsc.required_if' => 'IFSC code is required for bank transfers.',
            'pay_bank_ifsc.regex' => 'Invalid IFSC format. Example: HDFC0001234.',

            'pay_upi_id.required_if' => 'UPI ID is required for UPI payments.',
            'pay_upi_id.regex' => 'Invalid UPI ID format. Example: name@bank.',

            'account_holder_name.regex' => 'Account holder name must contain only alphabets.',

        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->walletService->addPaymentDetail($user, $validator->validated());

        if ($result['success']) {
            return redirect()->route('admin.wallet.payment-details')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->with('error', $result['message'])
            ->withInput();
    }

    /**
     * Delete payment detail
     */
    public function deletePaymentDetail(Request $request, $id)
    {
        $user = auth()->user();
        $result = $this->walletService->deletePaymentDetail($user, (int) $id);

        if ($result['success']) {
            return redirect()->route('admin.wallet.payment-details')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->with('error', $result['message']);
    }

    /**
     * Get transaction history (AJAX)
     */
    public function transactionHistory(Request $request)
    {
        $user = auth()->user();

        $filters = [
            'type' => $request->get('type'),
            'status' => $request->get('status'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'per_page' => $request->get('per_page', 15),
        ];

        $result = $this->walletService->getTransactionHistory($user, $filters);

        if ($request->ajax()) {
            return response()->json($result);
        }

        return view('pages.admin.wallet.transaction-history', [
            'transactions' => $result['data']['transactions'],
            'filters' => $filters,
        ]);
    }
}
