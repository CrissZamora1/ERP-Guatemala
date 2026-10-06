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

class DemoDataSeeder20 extends Seeder
{
    public function run(): void
    {
        // Categorías
        $catAbarrotes = Categoria::firstOrCreate(['nombre' => 'Abarrotes']);
        $catLimpieza = Categoria::firstOrCreate(['nombre' => 'Limpieza']);
        $catBebidas = Categoria::firstOrCreate(['nombre' => 'Bebidas']);
        $catLacteos = Categoria::firstOrCreate(['nombre' => 'Lácteos']);
        $catSnacks = Categoria::firstOrCreate(['nombre' => 'Snacks']);

        // Proveedores
        $prov1 = Proveedor::firstOrCreate(
            ['nombre' => 'Distribuidora Central S.A.'],
            [
                'nit' => '12345678-9',
                'direccion' => 'Zona 4, Ciudad de Guatemala',
                'telefono' => '22334455',
                'email' => 'ventas@distribuidoracentral.com',
                'contacto' => 'Juan Pérez',
                'tiempo_entrega_dias' => 3,
            ]
        );

        $prov2 = Proveedor::firstOrCreate(
            ['nombre' => 'Comercial del Norte'],
            [
                'nit' => '98765432-1',
                'direccion' => 'Cobán, Alta Verapaz',
                'telefono' => '77889900',
                'email' => 'contacto@comercialnorte.com',
                'contacto' => 'María López',
                'tiempo_entrega_dias' => 5,
            ]
        );

        $prov3 = Proveedor::firstOrCreate(
            ['nombre' => 'Lácteos del Valle'],
            [
                'nit' => '45678912-3',
                'direccion' => 'Chimaltenango',
                'telefono' => '55667788',
                'email' => 'pedidos@lacteosdelvalle.com',
                'contacto' => 'Carlos Ruiz',
                'tiempo_entrega_dias' => 2,
            ]
        );

        // 20 productos
        $productos = [
            ['sku' => 'ABR-001', 'nombre' => 'Arroz 1lb', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 4.50, 'precio' => 6.50],
            ['sku' => 'ABR-002', 'nombre' => 'Frijol 1lb', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 5.00, 'precio' => 7.25],
            ['sku' => 'ABR-003', 'nombre' => 'Azúcar 1lb', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 3.75, 'precio' => 5.50],
            ['sku' => 'ABR-004', 'nombre' => 'Sal 1lb', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 2.00, 'precio' => 3.50],
            ['sku' => 'ABR-005', 'nombre' => 'Aceite 1L', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 15.00, 'precio' => 21.00],
            ['sku' => 'ABR-006', 'nombre' => 'Pasta 400g', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 6.00, 'precio' => 8.50],
            ['sku' => 'ABR-007', 'nombre' => 'Harina 1lb', 'categoria_id' => $catAbarrotes->id, 'proveedor_id' => $prov1->id, 'costo' => 4.25, 'precio' => 6.00],
            ['sku' => 'LIM-001', 'nombre' => 'Shampoo 400ml', 'categoria_id' => $catLimpieza->id, 'proveedor_id' => $prov2->id, 'costo' => 18.00, 'precio' => 26.00],
            ['sku' => 'LIM-002', 'nombre' => 'Jabón de baño', 'categoria_id' => $catLimpieza->id, 'proveedor_id' => $prov2->id, 'costo' => 4.00, 'precio' => 6.00],
            ['sku' => 'LIM-003', 'nombre' => 'Detergente 1kg', 'categoria_id' => $catLimpieza->id, 'proveedor_id' => $prov2->id, 'costo' => 12.00, 'precio' => 17.50],
            ['sku' => 'LIM-004', 'nombre' => 'Cloro 1L', 'categoria_id' => $catLimpieza->id, 'proveedor_id' => $prov2->id, 'costo' => 8.00, 'precio' => 12.00],
            ['sku' => 'LIM-005', 'nombre' => 'Papel higiénico 4un', 'categoria_id' => $catLimpieza->id, 'proveedor_id' => $prov2->id, 'costo' => 14.00, 'precio' => 20.00],
            ['sku' => 'BEB-001', 'nombre' => 'Gaseosa 2L', 'categoria_id' => $catBebidas->id, 'proveedor_id' => $prov1->id, 'costo' => 9.00, 'precio' => 13.00],
            ['sku' => 'BEB-002', 'nombre' => 'Agua pura 1L', 'categoria_id' => $catBebidas->id, 'proveedor_id' => $prov1->id, 'costo' => 3.00, 'precio' => 5.00],
            ['sku' => 'BEB-003', 'nombre' => 'Jugo de naranja 1L', 'categoria_id' => $catBebidas->id, 'proveedor_id' => $prov3->id, 'costo' => 7.50, 'precio' => 11.00],
            ['sku' => 'BEB-004', 'nombre' => 'Café molido 200g', 'categoria_id' => $catBebidas->id, 'proveedor_id' => $prov1->id, 'costo' => 16.00, 'precio' => 23.00],
            ['sku' => 'LAC-001', 'nombre' => 'Leche entera 1L', 'categoria_id' => $catLacteos->id, 'proveedor_id' => $prov3->id, 'costo' => 6.50, 'precio' => 9.50],
            ['sku' => 'LAC-002', 'nombre' => 'Queso fresco 1lb', 'categoria_id' => $catLacteos->id, 'proveedor_id' => $prov3->id, 'costo' => 14.00, 'precio' => 20.00],
            ['sku' => 'SNK-001', 'nombre' => 'Papas fritas 150g', 'categoria_id' => $catSnacks->id, 'proveedor_id' => $prov2->id, 'costo' => 5.50, 'precio' => 8.00],
            ['sku' => 'SNK-002', 'nombre' => 'Galletas 200g', 'categoria_id' => $catSnacks->id, 'proveedor_id' => $prov2->id, 'costo' => 4.75, 'precio' => 7.00],
        ];

        $productosCreados = [];
        foreach ($productos as $prod) {
            $productosCreados[] = Producto::firstOrCreate(
                ['sku' => $prod['sku']],
                [
                    ...$prod,
                    'codigo_barras' => '750' . rand(1000000000, 9999999999),
                    'activo' => true,
                ]
            );
        }

        // Inventario en las 4 sucursales (algunos productos con stock bajo, para probar alertas)
        $sucursales = Sucursal::all();

        foreach ($productosCreados as $i => $producto) {
            foreach ($sucursales as $sucursal) {
                // Cada 5to producto queda con stock bajo a propósito (para probar alertas)
                $stockBajo = ($i % 5 === 0);

                Inventario::firstOrCreate(
                    ['producto_id' => $producto->id, 'sucursal_id' => $sucursal->id],
                    [
                        'stock_actual' => $stockBajo ? rand(5, 12) : rand(30, 100),
                        'stock_minimo' => 15,
                        'stock_maximo' => 150,
                    ]
                );
            }
        }

        // Empleados (2 por sucursal)
        foreach ($sucursales as $sucursal) {
            Empleado::firstOrCreate(
                ['nombre' => 'Encargado ' . $sucursal->nombre, 'sucursal_id' => $sucursal->id],
                [
                    'dpi' => '2500' . rand(10000000000, 99999999999),
                    'nit' => rand(1000000, 9999999) . '-' . rand(0, 9),
                    'puesto' => 'Encargado de tienda',
                    'fecha_ingreso' => now()->subMonths(rand(3, 24)),
                    'sueldo_base' => 3500,
                    'activo' => true,
                ]
            );

            Empleado::firstOrCreate(
                ['nombre' => 'Cajero ' . $sucursal->nombre, 'sucursal_id' => $sucursal->id],
                [
                    'dpi' => '2500' . rand(10000000000, 99999999999),
                    'nit' => rand(1000000, 9999999) . '-' . rand(0, 9),
                    'puesto' => 'Cajero',
                    'fecha_ingreso' => now()->subMonths(rand(1, 12)),
                    'sueldo_base' => 2800,
                    'activo' => true,
                ]
            );
        }

        // Usuarios de prueba
        $sucursal1 = $sucursales->first();

        User::firstOrCreate(
            ['email' => 'encargado1@prueba.com'],
            [
                'name' => 'Encargado Sucursal 1',
                'password' => Hash::make('password'),
                'role' => 'encargado',
                'sucursal_id' => $sucursal1->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'cajero1@prueba.com'],
            [
                'name' => 'Cajero Sucursal 1',
                'password' => Hash::make('password'),
                'role' => 'cajero',
                'sucursal_id' => $sucursal1->id,
            ]
        );
    }
}
