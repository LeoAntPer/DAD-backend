<?php

namespace App\Http\Controllers\api;


use App\Models\Vcard;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatisticResource;

class StatisticController extends Controller
{
    public function index()
    {
        $total_vcards = Vcard::withTrashed()->count();
        $total_active_vcards = Vcard::count();
        $total_blocked_vcards = Vcard::where('blocked', 1)->count();
        $total_transactions = Transaction::withTrashed()->where('payment_type', 'VCARD')->count() / 2;
        $total_transactions += Transaction::withTrashed()->where('payment_type', '<>', 'VCARD')->count();
        $total_transactions_by_group = Transaction::withTrashed()
        ->selectRaw('payment_type, MONTH(datetime) as mes, YEAR(datetime) as ano, count(*) as total')
        ->groupBy('mes', 'ano', 'payment_type')
        ->orderBy('ano', 'asc')
        ->orderBy('mes', 'asc')
        ->get();
        foreach ($total_transactions_by_group as $key => $value) {
            if ($value->payment_type == 'VCARD') {
                $value->total = $value->total / 2;
            }
        }
        $total_transactions_by_type = $total_transactions_by_group->groupBy('payment_type');
        foreach ($total_transactions_by_type as $key => $value) {
            $total_transactions_by_type[$key] = $value->sum('total');
        }
        $total_transactions_by_month = $total_transactions_by_group->groupBy('ano');
        foreach ($total_transactions_by_month as $key => $value) {
            $total_transactions_by_month[$key] = $value->groupBy('mes');
            foreach ($total_transactions_by_month[$key] as $key2 => $value) {
                $total_transactions_by_month[$key][$key2] = $value->sum('total');
            }
        }
        $novoArray = [];
        foreach ($total_transactions_by_month as $ano => $meses) {
            foreach ($meses as $mes => $valor) {
                $novoArray["$mes/$ano"] = $valor;
            }
        }
        $total_transactions_by_month = $novoArray;
        $total_balance = Vcard::sum('balance');

        return response()->json([
            "total_vcards" => $total_vcards,
            "total_active_vcards" => $total_active_vcards,
            "total_blocked_vcards" => $total_blocked_vcards,
            "total_transactions" => $total_transactions,
            "total_transactions_by_type" => $total_transactions_by_type,
            "total_transactions_by_month" => $total_transactions_by_month,
            "total_balance" => $total_balance,
        ]);
    }

    public function show($id)
    {
        $num_recived_transactions = Transaction::withTrashed()->where('pair_vcard', $id)->count();
        $total_received = Transaction::withTrashed()->where('pair_vcard', $id)->sum('value');
        $num_sent_transactions = Transaction::withTrashed()->where('vcard', $id)->count();
        $total_sent = Transaction::withTrashed()->where('vcard', $id)->sum('value');
        $balance_by_month = Transaction::selectRaw('MAX(id) as last_id, YEAR(datetime) as ano, MONTH(datetime) as mes')
            ->where('vcard', $id)
            ->whereOr('pair_vcard', $id)
            ->groupBy('ano', 'mes')
            ->orderBy('ano', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        $balance_by_month = $balance_by_month->groupBy('ano');
        foreach ($balance_by_month as $key => $value) {
            $balance_by_month[$key] = $value->groupBy('mes');
            foreach ($balance_by_month[$key] as $key2 => $value) {
                $balance_by_month[$key][$key2] = Transaction::select('new_balance')->where('id', $value[0]->last_id)->first()['new_balance'];
            }
        }
        $novoArray = [];
        foreach ($balance_by_month as $ano => $meses) {
            foreach ($meses as $mes => $valor) {
                $novoArray["$mes/$ano"] = $valor;
            }
        }
        $balance_by_month = $novoArray;

        return response()->json([
            "num_recived_transactions" => $num_recived_transactions,
            "total_received" => $total_received,
            "num_sent_transactions" => $num_sent_transactions,
            "total_sent" => $total_sent,
            "balance_by_month" => $balance_by_month,
        ]);
    }
}
