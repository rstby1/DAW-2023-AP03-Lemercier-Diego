<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ImportVenta;
use App\Models\Cliente;

class ClienteController extends Controller {

    public function buscarCliente(Request $request) {
        $msg = "";
        //yo pido un request $request
        //y aca lo asigno según el input

        $cuit = $request->input('cuit');
        /*
          $nro = $request->input('nro');
          $razonsocial = $request->input('razonsocial');
         */
        $cliente = Cliente::where('CUITCliente', $cuit)
                //->orWhere('NroCliente', $nro)
                //->orWhere('RazonSocial', $razonsocial)
                ->first();
        if ($cliente) {
            $this->modificarCliente($request);
        } else {
            $msg = 'El cliente no existe.';
        }
        return redirect("/modificarCliente?msg=" . urlencode($msg));
    }

    public function modificarCliente(Request $r) {
        $msg = "";
        $cuit = $r->input('cuit');
        $cliente = Cliente::where('CUITCliente', $cuit)
                //->orWhere('NroCliente', $nro)
                //->orWhere('RazonSocial', $razonsocial)
                ->first();
        $nro = $r->input('nro');
        $rs = $r->input('razonsocial');
        if ($cliente) {
            DB::beginTransaction();
            $d = [
            'CUITCliente' =>$cuit,
            'RazonSocial' =>$rs,
            'NroCliente' =>$nro,
            ];
            $cliente->save($d);
            DB::commit();
            $msg = "El cliente se actualizó con éxito";
        } else {
            $msg = "El cliente no existe, o los datos introducidos no son válidos";
        }
        return redirect("/modificarCliente?msg=" . urlencode($msg));
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
