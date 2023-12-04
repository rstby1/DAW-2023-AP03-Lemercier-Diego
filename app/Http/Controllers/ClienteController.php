<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ImportVenta;
use App\Models\Cliente;

class ClienteController extends Controller {

    public function buscarCliente(Request $request) {
        $cuit = $request->input('cuit');
        $cliente = Cliente::where('CUITCliente', $cuit)->first();

        return view('modificarCliente', ['cliente' => $cliente]);
    }

    public function modificarCliente(Request $request, $CUITCliente) {
        //$cliente = Cliente::where('CUITCliente', $CUITCliente)->first();
        $cliente = Cliente::find($CUITCliente);
        if ($cliente) {
            DB::beginTransaction();
            $d = ['CUITCliente' => $request->input('cuit'),
                'RazonSocial' => $request->input('razonsocial'),
                'NroCliente' => $request->input('nro'),];
            $cliente->update($d);
            DB::commit();
            $msg = "El cliente se actualizó con éxito";
        } else {
            $msg = "El cliente no existe o los datos introducidos no son válidos";
        }
        return redirect('/?msg=' . urlencode($msg));
    }

    public function altaCliente(Request $request) {
        $msg = "";
        $cuit = $request->input('cuit');
        $nro = $request->input('nro');
        $razonsocial = $request->input('razonsocial');
        $cliente = Cliente::where('CUITCliente', $cuit)
                ->orWhere('NroCliente', $nro)
                ->orWhere('RazonSocial', $razonsocial)
                ->first();
        if ($cliente) {
            $msg = 'Un cliente ya posee el mismo CUIT o NroCliente!';
        } else {
            DB::beginTransaction();
            $datosCliente = [
                'CUITCliente' => $cuit,
                'RazonSocial' => $razonsocial,
                'NroCliente' => $nro,
            ];
            Cliente::create($datosCliente);
            DB::commit();
            $msg = 'El cliente se creó con éxito.';
        }
        return redirect("/crearCliente?msg=" . urlencode($msg));
    }

}
