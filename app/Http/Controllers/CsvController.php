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
                            /*
                              ["CUITCliente", "RazonSocial", "NroCliente", "YTD", "tier"];
                             *                              */
                            $dCliente = [
                                'CUITCliente' => $data[5],
                                'RazonSocial' => $data[6],
                                'NroCliente' => $data[7],
                            ];
                            $x = Cliente::where('CUITCliente', $data[5])->first();
                            $y = ImportVenta::where('TipoComprobante', $data[1])
                                    ->where('NumeroComprobante', $data[3])
                                    ->first();

                            if ($x && $x->NroCliente != $data[7]) {
                                echo 'Error: El CUIT ' . $data[5] . ' ya está registrado en otro cliente con NroCliente(' . $data[7] . ')diferente.';
                                echo 'Ya introducidos: ' . $x->CUITCliente . ', NroCliente: ' . $x->NroCliente;
                            } elseif ($y) {
                                echo 'Error: El comprobante ' . $data[1] . ' - ' . $data[3] . ' ya existe.';
                            } elseif ($dVenta['ImporteVenta'] == 0) {
                                echo 'Error: El importe de la venta no puede ser cero o nulo.';
                            } elseif (Carbon::createFromFormat('d/m/Y', $dVenta['Fecha'])->isFuture()) {
                                echo 'Error: La fecha del comprobante no puede ser futura.';
                            } else {
                                try {
                                    DB::beginTransaction();
                                    Cliente::updateOrInsert($dCliente);
                                    ImportVenta::create($dVenta);
                                    $this->updateStats();

                                    DB::commit();
                                    echo 'Se actualizaron/cargaron los registros correctamente' . PHP_EOL;
                                } catch (\Exception $e) {
                                    DB::rollBack();
                                    echo 'Error al procesar datos. Consulta los registros para más detalles.' . PHP_EOL;
                                    throw new \Exception('Error al procesar datos. Detalles: ' . $e->getMessage());
                                }
                            }
                            //ggg
                        } catch (\Exception $e) {
                            DB::rollBack();
                            throw new \Exception('Error al procesar datos. Detalles: ' . $e->getMessage());
                        }
                    } else {
                        echo 'Archivo con columnas o valores inválidos';
                    }
                }
            }
        } catch (Exception $ex) {
            echo 'e';
        }
    }

    private function DataValidation($arrayVentas, $arrayCliente) {
        $x = Cliente::where('CUITCliente', $arrayCliente[5])->first();
        if ($x) {
            echo 'Cliente ya existente!';
            return false;
        }
        $y = ImportVenta::where('TipoComprobante', $arrayVentas[1])->where('NumeroComprobante', $arrayVentas[3])->first();
        if ($y) {
            echo 'El comprobante ya existe, TIPO y NUMERO DE COMPROBANTE';
            return false;
        }

        if ($arrayVentas[4] === 0) {
            echo 'El importe de ventas no puede ser 0!!!';
            return false;
        }

        if (Carbon::createFromFormat('d/m/Y', $arrayVentas[0])->isFuture()) {
            echo 'La fecha de la venta es futura';
            return false;
        }

        return true;
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
