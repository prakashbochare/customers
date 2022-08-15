<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Repayment;
use Config;
use App\Models\User;
use App\Models\Loan;

class RepaymentsController extends Controller {

    public function index($loanId) {
        $repaymentData = Repayment::all()->where('loan_id', '=', $loanId);
        $paidStatus = Config::get('customers.paidStatus');
        $users = User::pluck('name', 'id');
        $allRepaymentData = array();
        if (isset($repaymentData) && !empty($repaymentData)) {
            foreach ($repaymentData as $key => $repaymentDatas) {
                $allRepaymentData[$key]['id'] = $repaymentDatas['id'];
                $allRepaymentData[$key]['userId'] = $repaymentDatas['user_id'];
                $allRepaymentData[$key]['userName'] = $users[$repaymentDatas['user_id']];
                $allRepaymentData[$key]['loan_id'] = $repaymentDatas['loan_id'];
                $allRepaymentData[$key]['amount'] = $repaymentDatas['amount'];
                $allRepaymentData[$key]['emi_no'] = $repaymentDatas['emi_no'];
                $allRepaymentData[$key]['date'] = date('d-M-Y', strtotime($repaymentDatas['date']));
                $allRepaymentData[$key]['status'] = $repaymentDatas['status'];
                $allRepaymentData[$key]['statusName'] = $paidStatus[$repaymentDatas['status']];
            }
            $success['status'] = '1';
            $success['msg'] = 'List Found';
            $success['list'] = $allRepaymentData;
        } else {
            $success['status'] = '0';
            $success['msg'] = 'List Not Found';
            $success['list'] = $allLoanData;
        }
        return response()->json(['success' => $success]);
    }

    public function create($request) {
        if (isset($request) && !empty($request) && $request != null) {
            $term = $request->term;
            for ($i = 1; $i <= $term; $i++) {
                if ($i == 1) {
                    $date = date('M d, Y', strtotime($request->date));
                    $date = strtotime($date);
                    $date = strtotime("+7 day", $date);
                    $date = date('Y-m-d', $date);
                } else {
                    $j = $i - 1;
                    $repaymentLast = Repayment::where('loan_id', '=', $request->id)->where('emi_no', '=', $j)->first();
                    $date = date('M d, Y', strtotime($repaymentLast['date']));
                    $date = strtotime($date);
                    $date = strtotime("+7 day", $date);
                    $date = date('Y-m-d', $date);
                }
                $repayment = new Repayment();
                $repayment->user_id = $request->user_id;
                $repayment->loan_id = $request->id;
                $repayment->amount = ($request->amount / $term);
                $repayment->emi_no = $i;
                $repayment->date = $date;
                if ($repayment->save()) {
                    
                }
            }
        }
        return 1;
    }

    public function repaymentCustomer(Request $request) {
        $validator = $request->validate([
            'user_id' => 'required',
            'loan_id' => 'required',
            'amount' => 'required'
        ]);
        $repaymentDataFirst = Repayment::where('user_id', '=', $request->user_id)->where('loan_id', '=', $request->loan_id)->where('status', '=', 0)->where('amount', '<=', $request->amount)->first();
        if (isset($repaymentDataFirst) && !empty($repaymentDataFirst) && $repaymentDataFirst != null) {
            // $repaymentData = Repayment::all()->where('user_id', '=', $request->user_id)->where('loan_id', '=', $request->loan_id)->where('status', '=', 0);
            $repaymentDataLast = Repayment::where('user_id', '=', $request->user_id)->where('loan_id', '=', $request->loan_id)->where('status', '=', 0)->first();
            if (isset($repaymentDataLast) && !empty($repaymentDataLast) && $repaymentDataLast != null) {
                $id = $repaymentDataLast['id'];
                $repaymentUpdate = Repayment::find($id);
                $repaymentUpdate->status = 1;
                if ($repaymentUpdate->update()) {
                    
                }
                $repaymentDataUpdateStatus = Repayment::where('user_id', '=', $request->user_id)->where('loan_id', '=', $request->loan_id)->where('status', '=', 0)->first();
                if (isset($repaymentDataUpdateStatus) && !empty($repaymentDataUpdateStatus) && $repaymentDataUpdateStatus != null) {
                    
                } else {
                    $loanId = $request->loan_id;
                    $loan = Loan::find($loanId);
                    $loan->paid_loan_status = 1;
                    if ($loan->update()) {
                        
                    }
                }
                $success['status'] = '1';
                $success['msg'] = 'Payment paid successfully';
            }
        } else {
            $repaymentDataUpdateStatus = Repayment::where('user_id', '=', $request->user_id)->where('loan_id', '=', $request->loan_id)->where('status', '=', 0)->first();
            if (isset($repaymentDataUpdateStatus) && !empty($repaymentDataUpdateStatus) && $repaymentDataUpdateStatus != null) {
                $success['status'] = '0';
                $success['msg'] = 'Amount not valid. amount should be greater or equal to';
            } else {
                $repaymentDataFirst = Repayment::where('user_id', '=', $request->user_id)->where('loan_id', '=', $request->loan_id)->where('status', '=', 1)->where('amount', '<=', $request->amount)->first();
                if (isset($repaymentDataFirst) && !empty($repaymentDataFirst) && $repaymentDataFirst != null) {
                    $success['status'] = '0';
                    $success['msg'] = 'All payments are done';
                } else {
                    $success['status'] = '0';
                    $success['msg'] = 'Amount not valid. amount should be greater or equal to';
                }
            }
        }
        return response()->json(['success' => $success]);
    }

}
