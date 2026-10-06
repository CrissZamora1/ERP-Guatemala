<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Categorías
        $catAbarrotes = Categoria::create(['nombre' => 'Abarrotes']);
        $catLimpieza = Categoria::create(['nombre' => 'Limpieza']);
        $catBebidas = Categoria::create(['nombre' => 'Bebidas']);

        // Proveedores
        $prov1 = Proveedor::create([
            'nombre' => 'Distribuidora Central S.A.',
            'nit' => '12345678-9',
            'direccion' => 'Zona 4, Ciudad de Guatemala',
            'telefono' => '22334455',
            'email' => 'ventas@distribuidoracentral.com',
            'contacto' => 'Juan Pérez',
            'tiempo_entrega_dias' => 3,
        ]);

        $prov2 = Proveedor::create([
            'nombre' => 'Comercial del Norte',
            'nit' => '98765432-1',
            'direccion' => 'Cobán, Alta Verapaz',
            'telefono' => '77889900',
            'email' => 'contacto@comercialnorte.com',
            'contacto' => 'María López',
            'tiempo_entrega_dias' => 5,
        ]);

        // Productos
        $productos = [
            ['sku' => 'ABR-001', 'nombre' => 'Arroz 1lb', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 4.50, 'precio' => 6.50],
            ['sku' => 'ABR-002', 'nombre' => 'Frijol 1lb', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 5.00, 'precio' => 7.25],
            ['sku' => 'ABR-003', 'nombre' => 'Azúcar 1lb', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 3.75, 'precio' => 5.50],
            ['sku' => 'LIM-001', 'nombre' => 'Shampoo 400ml', 'categoria_id' => $catLimpieza->id, 'proveedor_id' => $prov2->id, 'costo' => 18.00, 'precio' => 26.00],
            ['sku' => 'LIM-002', 'nombre' => 'Jabón de baño', 'categoria_id' => $catLimpieza->id, 'proveedor_id' => $prov2->id, 'costo' => 4.00, 'precio' => 6.00],
            ['sku' => 'BEB-001', 'nombre' => 'Gaseosa 2L', 'categoria_id' => $catBebidas->id, 'proveedor_id' => $prov1->id, 'costo' => 9.00, 'precio' => 13.00],
            ['sku' => 'BEB-002', 'nombre' => 'Agua pura 1L', 'categoria_id' => $catBebidas->id, 'proveedor_id' => $prov1->id, 'costo' => 3.00, 'precio' => 5.00],
        ];

        $productosCreados = [];
        foreach ($productos as $prod) {
            $productosCreados[] = Producto::create([
                ...$prod,
                'codigo_barras' => '750' . rand(1000000000, 9999999999),
                'activo' => true,
            ]);
        }

        // Inventario: stock inicial en cada una de las 4 sucursales
        $sucursales = Sucursal::all();

        foreach ($productosCreados as $producto) {
            foreach ($sucursales as $sucursal) {
                Inventario::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $sucursal->id,
                    'stock_actual' => rand(20, 100),
                    'stock_minimo' => 15,
                    'stock_maximo' => 150,
                ]);
            }
        }

        // Empleados (2 por sucursal)
        foreach ($sucursales as $i => $sucursal) {
            Empleado::create([
                'sucursal_id' => $sucursal->id,
                'nombre' => 'Encargado ' . $sucursal->nombre,
                'dpi' => '2500' . rand(10000000000, 99999999999),
                'nit' => rand(1000000, 9999999) . '-' . rand(0, 9),
                'puesto' => 'Encargado de tienda',
                'fecha_ingreso' => now()->subMonths(rand(3, 24)),
                'sueldo_base' => 3500,
                'activo' => true,
            ]);

            Empleado::create([
                'sucursal_id' => $sucursal->id,
                'nombre' => 'Cajero ' . $sucursal->nombre,
                'dpi' => '2500' . rand(10000000000, 99999999999),
                'nit' => rand(1000000, 9999999) . '-' . rand(0, 9),
                'puesto' => 'Cajero',
                'fecha_ingreso' => now()->subMonths(rand(1, 12)),
                'sueldo_base' => 2800,
                'activo' => true,
            ]);
        }

        // Usuarios de prueba: 1 encargado y 1 cajero en Sucursal 1
        $sucursal1 = $sucursales->first();

        User::create([
            'name' => 'Encargado Sucursal 1',
            'email' => 'encargado1@prueba.com',
            'password' => Hash::make('password'),
            'role' => 'encargado',
            'sucursal_id' => $sucursal1->id,
        ]);

        User::create([
            'name' => 'Cajero Sucursal 1',
            'email' => 'cajero1@prueba.com',
            'password' => Hash::make('password'),
            'role' => 'cajero',
            'sucursal_id' => $sucursal1->id,
        ]);
    }
}
