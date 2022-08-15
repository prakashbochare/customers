<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Config;
use App\Http\Controllers\RepaymentsController;

class LoansController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $loanData = Loan::all();
        $loanStatus = Config::get('customers.loanStatus');
        $paidStatus = Config::get('customers.paidStatus');
        $users = User::pluck('name', 'id');
        $allLoanData = array();
        if (isset($loanData) && !empty($loanData)) {
            foreach ($loanData as $key => $loanDatas) {
                $allLoanData[$key]['id'] = $loanDatas['id'];
                $allLoanData[$key]['userId'] = $loanDatas['user_id'];
                $allLoanData[$key]['userName'] = $users[$loanDatas['user_id']];
                $allLoanData[$key]['date'] = date('d-M-Y', strtotime($loanDatas['date']));
                $allLoanData[$key]['amount'] = $loanDatas['amount'];
                $allLoanData[$key]['term'] = $loanDatas['term'];
                $allLoanData[$key]['status'] = $loanDatas['status'];
                $allLoanData[$key]['statusName'] = $loanStatus[$loanDatas['status']];
                $allLoanData[$key]['paidLoanStatus'] = $loanDatas['paid_loan_status'];
                $allLoanData[$key]['paidLoanStatusName'] = $paidStatus[$loanDatas['paid_loan_status']];
            }
            $success['status'] = '1';
            $success['msg'] = 'List Found';
            $success['list'] = $allLoanData;
        } else {
            $success['status'] = '0';
            $success['msg'] = 'List Not Found';
            $success['list'] = $allLoanData;
        }
        return response()->json(['success' => $success]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $validator = $request->validate([
            'user_id' => 'required',
            'amount' => 'required',
            'term' => 'required'
        ]);
        $userId = $request->user_id;
        $user = User::find($userId);
        if (isset($user) && !empty($user) && $user != null) {
            $loanRequest = $request->all();
            $loanRequest['date'] = date('Y-m-d');
            if (Loan::create($loanRequest)) {
                $success['status'] = '1';
                $success['msg'] = 'Submit a loan request successfully';
            } else {
                $success['status'] = '0';
                $success['msg'] = 'Submit a loan request unsuccessfully';
            }
        } else {
            $success['status'] = '0';
            $success['msg'] = 'Unauthenticated user';
        }
        return response()->json(['success' => $success]);
    }

    public function aprovedLoanStatus(Request $request) {
        $validator = $request->validate([
            'id' => 'required',
            'status' => 'required'
        ]);
        $id = $request->id;
        $loan = Loan::find($id);
        if (isset($loan) && !empty($loan) && $loan != null) {
            if ($loan->status == 0) {
                $success['status'] = '0';
                $success['msg'] = 'Loan Aproved Already';
            } else {
                $loan->status = $request->status;
                if ($loan->update()) {
                    $repayment = new RepaymentsController;
                    $repayment->create($loan);
                    $success['status'] = '1';
                    $success['msg'] = 'Aproved Loan successfully';
                } else {
                    $success['status'] = '0';
                    $success['msg'] = 'Opps something went wrong';
                }
            }
        } else {
            $success['status'] = '0';
            $success['msg'] = 'Please sent correct loan id';
        }
        return response()->json(['success' => $success]);
    }

    public function customerViewLoan($userId) {
        $loanData = Loan::all()->where('user_id', '=', $userId);
        $loanStatus = Config::get('customers.loanStatus');
        $paidStatus = Config::get('customers.paidStatus');
        $users = User::pluck('name', 'id');
        $allLoanData = array();
        if (isset($loanData) && !empty($loanData)) {
            foreach ($loanData as $key => $loanDatas) {
                $allLoanData[$key]['id'] = $loanDatas['id'];
                $allLoanData[$key]['userId'] = $loanDatas['user_id'];
                $allLoanData[$key]['userName'] = $users[$loanDatas['user_id']];
                $allLoanData[$key]['date'] = date('d-M-Y', strtotime($loanDatas['date']));
                $allLoanData[$key]['amount'] = $loanDatas['amount'];
                $allLoanData[$key]['term'] = $loanDatas['term'];
                $allLoanData[$key]['status'] = $loanDatas['status'];
                $allLoanData[$key]['statusName'] = $loanStatus[$loanDatas['status']];
                $allLoanData[$key]['paidLoanStatus'] = $loanDatas['paid_loan_status'];
                $allLoanData[$key]['paidLoanStatusName'] = $paidStatus[$loanDatas['paid_loan_status']];
            }
            $success['status'] = '1';
            $success['msg'] = 'List Found';
            $success['list'] = $allLoanData;
        } else {
            $success['status'] = '0';
            $success['msg'] = 'List Not Found';
            $success['list'] = $allLoanData;
        }
        return response()->json(['success' => $success]);
    }

}
