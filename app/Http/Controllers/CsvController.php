<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImportVenta;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CsvController extends Controller {

    public function procesarCSV(Request $request) {
        //$f_ext = pathinfo($file, PATHINFO_EXTENSION);
        if ($request->hasFile('fileCSV')) {
            $file = $request->file('fileCSV');
            $this->impData($file);
        } else {
            echo 'Error al subir el archivo';
        }
    }

    private function impData($file) {
        $msg = 'Mensaje Default';
        if ($this->checkCSV($file)) {
            try {
                //if ($this->checkCSV($file)) {
                if (($handle = fopen($file, 'r')) !== false) {
                    fgetcsv($handle, 1000, ';');
                    while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                        if (count($data) === 8) {
                            try {
                                $dVenta = [
                                    'Fecha' => strtolower($data[0]),
                                    'TipoComprobante' => strtoupper($data[1]),
                                    'PuntoVenta' => $data[2],
                                    'NumeroComprobante' => $data[3],
                                    'ImporteVenta' => $data[4],
                                    'CUITCliente' => $data[5],
                                ];
                                $dCliente = [
                                    'CUITCliente' => $data[5],
                                    'RazonSocial' => $data[6],
                                    'NroCliente' => $data[7],
                                ];
                                $x = Cliente::where('CUITCliente', $data[5])->first();
                                $y = ImportVenta::where('TipoComprobante', $data[1])
                                        ->where('NumeroComprobante', $data[3])
                                        ->first();
                                //intenté hacer una funcion aparte pero no llegaba con el tiempo
                                if ($x && $x->NroCliente != $data[7]) {
                                    $msg = 'Error: El CUIT ' . $data[5] . ' ya está registrado en otro cliente con NroCliente(' . $data[7] . ')diferente.';
                                    $msg = 'Ya introducidos: ' . $x->CUITCliente . ', NroCliente: ' . $x->NroCliente;
                                } elseif ($y) {
                                    $msg = 'Error: El comprobante ' . $data[1] . ' - ' . $data[3] . ' ya existe.';
                                } elseif ($dVenta['ImporteVenta'] == 0) {
                                    $msg = 'Error: El importe de la venta no puede ser cero o nulo.';
                                } elseif (Carbon::createFromFormat('d/m/Y', $dVenta['Fecha'])->isFuture()) {
                                    $msg = 'Error: La fecha del comprobante no puede ser futura.';
                                } else {
                                    DB::beginTransaction();
                                    Cliente::updateOrInsert($dCliente);
                                    ImportVenta::create($dVenta);
                                    $this->updateStats();
                                    DB::commit();
                                    $msg = 'Datos cargados con éxito. ';
                                    echo $msg;
                                }
                                echo $msg;
                            } catch (\Exception $e) {
                                DB::rollBack();
                                throw new \Exception('Error al procesar datos. Detalles: ' . $e->getMessage());
                            }
                        } else {
                            $msg = 'Número incorrecto de columnas! ';
                            echo $msg;
                        }
                    }
                }
            } catch (Exception $ex) {
                throw new \Exception('Error al procesar datos. Detalles: ' . $e->getMessage());
            }
        } else {
            $msg = 'No es un archivo CSV ';
            echo $msg;
        }
    }

    private function updateStats() {
        $anoPasado = date('d/m/Y', strtotime('-1 year'));

        $ventasAcumuladas = DB::table('import_ventas')
                ->select('CUITCliente', DB::raw('SUM(ImporteVenta) as ventas_acumuladas'))
                ->whereBetween(DB::raw('STR_TO_DATE(Fecha, "%d/%m/%Y")'), [
                    Carbon::createFromFormat('d/m/Y', $anoPasado)->format('Y-m-d'),
                    Carbon::now()->format('Y-m-d'),
                ])
                ->groupBy('CUITCliente')
                ->get();
        foreach ($ventasAcumuladas as $venta) {
            $cuitCliente = $venta->CUITCliente;
            $vAcumuladaas = $venta->ventas_acumuladas;
            $cliente = DB::table('clientes')->wheres('CUITCliente', $cuitCliente);
            if ($cliente) {
                DB::table('clientes')->where('CUITCliente', $cuitCliente)->update(['YTD' => $vAcumuladaas]);
            } else {
                DB::table('clientes')->insert([
                    'CUITCliente' => $cuitCliente,
                    'YTD' => $ventasAcumuladas,
                ]);
            }
            /* Tier 1: Ventas de hasta 1M, Tier 2: Ventas entre 1M y 3M y tier 3: Ventas de más de 3M */
        }
        $tierClientes = DB::table('clientes')->select('CUITCliente', 'YTD')->get();

        foreach ($tierClientes as $tier) {
            $cuitCliente = $tier->CUITCliente;
            $YTDcliente = $tier->YTD;

            // Aquí también hay un error en la consulta, deberías usar ->where en lugar de ->wheres
            $cliente = DB::table('clientes')->where('CUITCliente', $cuitCliente);

            if ($YTDcliente <= 1000) {
                $cliente->update(['tier' => 1]);
            } elseif ($YTDcliente <= 3000 && $YTDcliente > 1000) {
                $cliente->update(['tier' => 2]);
            } elseif ($YTDcliente > 3000) {
                $cliente->update(['tier' => 3]);
            } else {
                $cliente->update(['tier' => 0]);
            }
        }
    }

    private function checkCSV($file) {
        //tomo la ruta del archivo ex:
        /*
         * miArchivo.csv 
         * pido el PATH_EXTENSION
         * la extensión
         * y por ultimo checkeo que sea 'csv'
         */
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (strtolower($ext) === 'csv') {
            return true;
        } else {
            return false;
        }
    }

}
