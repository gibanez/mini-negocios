<?php

use Phinx\Migration\AbstractMigration;

final class ExpandIndustryProducts extends AbstractMigration
{
    public function up(): void
    {
        // ── AMPLIAR RUBROS EXISTENTES ─────────────────────────────────────────

        // Almacén: agregar más categorías y productos
        $almacenRow = $this->fetchAll("SELECT id FROM industries WHERE name='Almacén' LIMIT 1");
        if (!empty($almacenRow)) {
            $almacenId = $almacenRow[0]['id'];

            // Cereales y pastas
            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$almacenId', 'Cereales y pastas', 5)");
            foreach ([
                ['Fideos tallarín','paquete'],['Fideos spaghetti','paquete'],['Fideos moño','paquete'],
                ['Arroz largo fino','kg'],['Arroz integral','kg'],['Lentejas','kg'],
                ['Porotos','kg'],['Garbanzos','kg'],['Avena','paquete'],['Polenta','paquete'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            // Aceites y condimentos
            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$almacenId', 'Aceites y condimentos', 6)");
            foreach ([
                ['Aceite de girasol','litro'],['Aceite de oliva','litro'],['Vinagre blanco','litro'],
                ['Sal fina','kg'],['Sal gruesa','kg'],['Azúcar blanca','kg'],
                ['Azúcar rubia','kg'],['Harina 0000','kg'],['Harina 000','kg'],['Maicena','paquete'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            // Almacén variado
            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$almacenId', 'Almacén variado', 7)");
            foreach ([
                ['Puré de tomate','unidad'],['Salsa de tomate','unidad'],['Caldo de verduras','caja'],
                ['Caldo de pollo','caja'],['Mayonesa','unidad'],['Ketchup','unidad'],
                ['Mostaza','unidad'],['Mermelada','unidad'],['Dulce de leche','kg'],['Miel','unidad'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            // Snacks y golosinas
            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$almacenId', 'Snacks y golosinas', 8)");
            foreach ([
                ['Papas fritas','paquete'],['Palitos salados','paquete'],['Alfajor','unidad'],
                ['Chocolate','unidad'],['Caramelos','bolsa'],['Chicles','paquete'],
                ['Maní','paquete'],['Pochoclo','paquete'],['Barritas de cereal','unidad'],['Galletitas dulces','paquete'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            // Higiene personal
            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$almacenId', 'Higiene personal', 9)");
            foreach ([
                ['Jabón de tocador','unidad'],['Shampoo','unidad'],['Acondicionador','unidad'],
                ['Pasta dental','unidad'],['Cepillo de dientes','unidad'],['Desodorante','unidad'],
                ['Afeitadora','unidad'],['Toallitas húmedas','paquete'],['Preservativos','caja'],['Algodón','bolsa'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }
        }

        // Verdulería: ampliar
        $verdRow = $this->fetchAll("SELECT id FROM industries WHERE name='Verdulería' LIMIT 1");
        if (!empty($verdRow)) {
            $verdId = $verdRow[0]['id'];

            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$verdId', 'Tubérculos y raíces', 3)");
            foreach ([
                ['Batata','kg'],['Boniato','kg'],['Remolacha','kg'],['Nabo','kg'],['Apio','unidad'],
                ['Puerro','unidad'],['Ajo','kg'],['Cebolla de verdeo','atado'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$verdId', 'Frutas tropicales', 4)");
            foreach ([
                ['Ananá','unidad'],['Mango','unidad'],['Papaya','unidad'],['Kiwi','unidad'],
                ['Maracuyá','unidad'],['Sandía','unidad'],['Melón','unidad'],['Pomelo','kg'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$verdId', 'Legumbres y otros', 5)");
            foreach ([
                ['Espinaca','atado'],['Acelga','atado'],['Brócoli','unidad'],['Coliflor','unidad'],
                ['Repollito de Bruselas','kg'],['Repollo','unidad'],['Calabaza','kg'],['Berenjena','kg'],
                ['Pepino','unidad'],['Rabanito','atado'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }
        }

        // Carnicería: ampliar
        $carnicRow = $this->fetchAll("SELECT id FROM industries WHERE name='Carnicería' LIMIT 1");
        if (!empty($carnicRow)) {
            $carnicId = $carnicRow[0]['id'];

            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$carnicId', 'Cerdo', 3)");
            foreach ([
                ['Lomo de cerdo','kg'],['Bondiola de cerdo','kg'],['Costilla de cerdo','kg'],
                ['Panceta','kg'],['Patitas de cerdo','kg'],['Matambre de cerdo','kg'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$carnicId', 'Achuras y menudencias', 4)");
            foreach ([
                ['Mondongo','kg'],['Corazón de vaca','kg'],['Hígado','kg'],['Riñón','kg'],
                ['Molleja','kg'],['Tripa gorda','kg'],['Chinchulín','kg'],['Morcilla','unidad'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$carnicId', 'Preparados', 5)");
            foreach ([
                ['Hamburguesa casera','unidad'],['Milanesa de ternera','kg'],['Milanesa de pollo','kg'],
                ['Brochette de pollo','unidad'],['Alitas marinadas','kg'],['Costilla adobada','kg'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }
        }

        // Ferretería: ampliar
        $ferrRow = $this->fetchAll("SELECT id FROM industries WHERE name='Ferretería' LIMIT 1");
        if (!empty($ferrRow)) {
            $ferrId = $ferrRow[0]['id'];

            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$ferrId', 'Plomería', 4)");
            foreach ([
                ['Caño PVC 1/2','metro'],['Caño PVC 3/4','metro'],['Llave de paso','unidad'],
                ['Teflón','rollo'],['Codo PVC','unidad'],['Unión PVC','unidad'],
                ['Pegamento PVC','unidad'],['Flexible metálico','unidad'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$ferrId', 'Abrasivos y soldadura', 5)");
            foreach ([
                ['Disco de corte 115mm','unidad'],['Disco de corte 230mm','unidad'],['Disco de desbaste','unidad'],
                ['Lija al agua 120','unidad'],['Lija al agua 220','unidad'],['Electrodo 3.2mm','kg'],
                ['Hilo de soldar','rollo'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }

            $catId = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$ferrId', 'Seguridad', 6)");
            foreach ([
                ['Candado mediano','unidad'],['Candado grande','unidad'],['Cerradura doble paleta','unidad'],
                ['Bisagra reforzada','unidad'],['Torniquete','unidad'],['Pasador','unidad'],
                ['Cadena de seguridad','metro'],
            ] as $i => $p) {
                $pid = $this->uuid();
                $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
            }
        }

        // ── NUEVOS RUBROS ─────────────────────────────────────────────────────

        // Kiosco
        $kioscoId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industries (id, name, description, status) VALUES ('$kioscoId', 'Kiosco', 'Golosinas, cigarrillos, diarios y artículos de papelería', 'active')");

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$kioscoId', 'Chocolates y alfajores', 0)");
        foreach ([
            ['Alfajor simple','unidad'],['Alfajor triple','unidad'],['Alfajor de maicena','unidad'],
            ['Chocolate con leche','unidad'],['Chocolate negro','unidad'],['Chocolate blanco','unidad'],
            ['Bocadito de chocolate','unidad'],['Tableta de chocolate','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$kioscoId', 'Caramelos y gomas', 1)");
        foreach ([
            ['Caramelos surtidos','bolsa'],['Pastillas de menta','caja'],['Goma de mascar','unidad'],
            ['Chupetín','unidad'],['Palito dulce','unidad'],['Gomitas','bolsa'],
            ['Caramelo masticable','unidad'],['Turrones','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$kioscoId', 'Cigarrillos', 2)");
        foreach ([
            ['Cigarrillos rubios 10u','atado'],['Cigarrillos rubios 20u','atado'],
            ['Cigarrillos suaves 20u','atado'],['Tabaco para armar','paquete'],
            ['Papelillo','caja'],['Encendedor','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$kioscoId', 'Bebidas frías', 3)");
        foreach ([
            ['Gaseosa 500ml','unidad'],['Agua 500ml','unidad'],['Jugo 1L','unidad'],
            ['Energizante','unidad'],['Agua saborizada','unidad'],['Cerveza lata','unidad'],['Isotónica','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$kioscoId', 'Papelería', 4)");
        foreach ([
            ['Lapicera azul','unidad'],['Lapicera negra','unidad'],['Lápiz negro','unidad'],
            ['Resaltador','unidad'],['Cuaderno','unidad'],['Block de hojas','unidad'],
            ['Corrector','unidad'],['Tijera','unidad'],['Pegamento en barra','unidad'],['Regla','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$kioscoId', 'Snacks y fiambres rápidos', 5)");
        foreach ([
            ['Papas fritas','paquete'],['Palitos','paquete'],['Maní','paquete'],
            ['Chizitos','paquete'],['Recortes de fiambre','paquete'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        // Panadería y confitería
        $panId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industries (id, name, description, status) VALUES ('$panId', 'Panadería', 'Pan, facturas, tortas y pastelería', 'active')");

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$panId', 'Panes', 0)");
        foreach ([
            ['Pan francés','unidad'],['Pan flauta','unidad'],['Pan lactal','unidad'],
            ['Pan integral','unidad'],['Pan de hamburguesa','unidad'],['Pan de hot dog','unidad'],
            ['Pan de campo','kg'],['Pan árabe','unidad'],['Pan rallado','kg'],['Tostadas','paquete'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$panId', 'Facturas y masas', 1)");
        foreach ([
            ['Medialunas de grasa','unidad'],['Medialunas de manteca','unidad'],['Vigilante','unidad'],
            ['Cañón','unidad'],['Cuerno','unidad'],['Facturas surtidas','kg'],
            ['Bizcochos de grasa','kg'],['Palmeritas','unidad'],['Trenzas','unidad'],['Orejitas','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$panId', 'Tortas y pasteles', 2)");
        foreach ([
            ['Torta de cumpleaños 1kg','unidad'],['Torta de cumpleaños 2kg','unidad'],
            ['Cheesecake','porción'],['Brownie','unidad'],['Muffin','unidad'],
            ['Budín de limón','unidad'],['Budín de chocolate','unidad'],
            ['Tarta de frutas','porción'],['Rogel','unidad'],['Pionono','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$panId', 'Galletería y masas secas', 3)");
        foreach ([
            ['Galletitas de agua','paquete'],['Galletitas dulces','paquete'],['Alfajorcitos','unidad'],
            ['Pepas','unidad'],['Pastafrola','unidad'],['Pasta de chocolate','kg'],
            ['Mantecadas','unidad'],['Polvorones','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$panId', 'Bebidas y cafetería', 4)");
        foreach ([
            ['Café expresso','unidad'],['Café con leche','unidad'],['Té con limón','unidad'],
            ['Chocolate caliente','unidad'],['Medialunas con café','combo'],
            ['Agua mineral','unidad'],['Jugo natural','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        // Farmacia / Perfumería
        $farmaId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industries (id, name, description, status) VALUES ('$farmaId', 'Farmacia / Perfumería', 'Medicamentos sin receta, higiene y cuidado personal', 'active')");

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$farmaId', 'Analgésicos y antifebriles', 0)");
        foreach ([
            ['Ibuprofeno 400mg','caja'],['Paracetamol 500mg','caja'],['Aspirina','caja'],
            ['Dipirona','caja'],['Naproxeno','caja'],['Diclofenac gel','unidad'],
            ['Gel antiinflamatorio','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$farmaId', 'Antisépticos y primeros auxilios', 1)");
        foreach ([
            ['Alcohol en gel','unidad'],['Alcohol 70°','unidad'],['Agua oxigenada','unidad'],
            ['Povidona yodada','unidad'],['Curitas surtidas','caja'],['Venda elástica','unidad'],
            ['Gasa estéril','paquete'],['Termómetro digital','unidad'],['Tijera de primeros auxilios','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$farmaId', 'Higiene y cuidado', 2)");
        foreach ([
            ['Jabón antibacterial','unidad'],['Shampoo','unidad'],['Acondicionador','unidad'],
            ['Crema corporal','unidad'],['Crema de manos','unidad'],['Protector solar SPF30','unidad'],
            ['Desodorante roll-on','unidad'],['Desodorante aerosol','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$farmaId', 'Anticoncepción e higiene íntima', 3)");
        foreach ([
            ['Preservativos x3','caja'],['Preservativos x12','caja'],['Toallitas femeninas','paquete'],
            ['Tampones','paquete'],['Copa menstrual','unidad'],['Gel íntimo','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$farmaId', 'Vitaminas y suplementos', 4)");
        foreach ([
            ['Vitamina C 1000mg','caja'],['Complejo B','caja'],['Vitamina D','caja'],
            ['Magnesio','caja'],['Omega 3','caja'],['Hierro','caja'],
            ['Multivitamínico','caja'],['Melatonina','caja'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        // Librería
        $librId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industries (id, name, description, status) VALUES ('$librId', 'Librería', 'Artículos de papelería, útiles escolares y oficina', 'active')");

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$librId', 'Escritura', 0)");
        foreach ([
            ['Lapicera azul','unidad'],['Lapicera negra','unidad'],['Lapicera roja','unidad'],
            ['Lápiz HB','unidad'],['Lápiz 2B','unidad'],['Marcador permanente negro','unidad'],
            ['Resaltador amarillo','unidad'],['Resaltador naranja','unidad'],
            ['Corrector en cinta','unidad'],['Corrector líquido','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$librId', 'Cuadernos y libretas', 1)");
        foreach ([
            ['Cuaderno tapa blanda 48h','unidad'],['Cuaderno tapa dura 96h','unidad'],
            ['Cuaderno universitario','unidad'],['Libreta pequeña','unidad'],
            ['Block de notas A4','unidad'],['Block de notas A5','unidad'],
            ['Agenda semanal','unidad'],['Carpeta 3 anillos','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$librId', 'Arte y manualidades', 2)");
        foreach ([
            ['Acuarelas','caja'],['Temperas','caja'],['Lápices de color x12','caja'],
            ['Lápices de color x24','caja'],['Tijera escolar','unidad'],['Pegamento en barra','unidad'],
            ['Plasticola','unidad'],['Cinta adhesiva','rollo'],['Papel lustrina','pliego'],['Cartulina','pliego'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$librId', 'Oficina y organización', 3)");
        foreach ([
            ['Clips x100','caja'],['Broches mariposa','caja'],['Post-it','paquete'],
            ['Separadores de colores','paquete'],['Perforadora','unidad'],
            ['Abrochadora','unidad'],['Regla 30cm','unidad'],['Escuadra','unidad'],
            ['Calculadora','unidad'],['Sello de fecha','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$librId', 'Tecnología básica', 4)");
        foreach ([
            ['Pendrive 16GB','unidad'],['Pendrive 32GB','unidad'],['Auriculares','unidad'],
            ['Cable USB-C','unidad'],['Pila AA x4','caja'],['Pila AAA x4','caja'],
            ['Funda de celular','unidad'],['Vidrio templado','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        // Dietética
        $dietId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industries (id, name, description, status) VALUES ('$dietId', 'Dietética', 'Productos naturales, integrales, veganos y sin TACC', 'active')");

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$dietId', 'Harinas y cereales', 0)");
        foreach ([
            ['Harina integral','kg'],['Harina de almendra','kg'],['Harina de arroz','kg'],
            ['Harina de garbanzo','kg'],['Avena instantánea','kg'],['Avena arrollada','kg'],
            ['Quinoa','kg'],['Mijo','kg'],['Amaranto','kg'],['Trigo burgol','kg'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$dietId', 'Semillas y frutos secos', 1)");
        foreach ([
            ['Chía','kg'],['Lino','kg'],['Sésamo','kg'],['Girasol pelado','kg'],
            ['Almendras','kg'],['Nueces','kg'],['Castañas de cajú','kg'],
            ['Maní sin sal','kg'],['Pistachos','kg'],['Mix de semillas','kg'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$dietId', 'Endulzantes y edulcorantes', 2)");
        foreach ([
            ['Stevia en polvo','unidad'],['Azúcar mascabo','kg'],['Miel de abejas','kg'],
            ['Jarabe de agave','unidad'],['Melaza de caña','unidad'],['Dátiles','kg'],
            ['Eritritol','kg'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$dietId', 'Proteínas y suplementos', 3)");
        foreach ([
            ['Proteína whey vainilla','kg'],['Proteína whey chocolate','kg'],
            ['Proteína vegana','kg'],['Creatina','unidad'],['BCAA','unidad'],
            ['Colágeno','unidad'],['Espirulina','unidad'],['Clorela','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$dietId', 'Sin TACC y veganos', 4)");
        foreach ([
            ['Galletitas sin gluten','paquete'],['Pan sin gluten','unidad'],
            ['Leche de almendra','litro'],['Leche de avena','litro'],['Leche de coco','litro'],
            ['Mantequilla de maní','unidad'],['Queso vegano','unidad'],['Hamburguesa vegana','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        // Perfumería
        $perfId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industries (id, name, description, status) VALUES ('$perfId', 'Perfumería', 'Cosméticos, fragancias y cuidado personal', 'active')");

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$perfId', 'Fragancias', 0)");
        foreach ([
            ['Perfume mujer 50ml','unidad'],['Perfume mujer 100ml','unidad'],
            ['Perfume hombre 50ml','unidad'],['Perfume hombre 100ml','unidad'],
            ['Colonia infantil','unidad'],['Body splash','unidad'],['Desodorante colonia','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$perfId', 'Maquillaje', 1)");
        foreach ([
            ['Base de maquillaje','unidad'],['Polvo compacto','unidad'],['Labial mate','unidad'],
            ['Labial brillo','unidad'],['Máscara de pestañas','unidad'],['Sombras de ojos','paleta'],
            ['Delineador negro','unidad'],['Rubor','unidad'],['Contorno','unidad'],['Iluminador','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$perfId', 'Cuidado capilar', 2)");
        foreach ([
            ['Shampoo hidratante','unidad'],['Shampoo anticaída','unidad'],['Acondicionador','unidad'],
            ['Mascarilla capilar','unidad'],['Aceite de argán','unidad'],
            ['Crema de peinar','unidad'],['Spray fijador','unidad'],['Tinte de cabello','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$perfId', 'Cuidado de la piel', 3)");
        foreach ([
            ['Crema hidratante facial','unidad'],['Crema de noche','unidad'],['Contorno de ojos','unidad'],
            ['Sérum vitamina C','unidad'],['Protector solar SPF50','unidad'],
            ['Limpiador facial','unidad'],['Tónico facial','unidad'],['Micellar water','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$perfId', 'Accesorios belleza', 4)");
        foreach ([
            ['Esponja de maquillaje','unidad'],['Pinceles de maquillaje','set'],
            ['Removedor de maquillaje','unidad'],['Algodones','paquete'],
            ['Lima de uñas','unidad'],['Esmalte de uñas','unidad'],
            ['Removedor de esmalte','unidad'],['Pinzas de depilar','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        // Ropa y textiles
        $ropaId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industries (id, name, description, status) VALUES ('$ropaId', 'Ropa y textiles', 'Prendas de vestir, calzado y accesorios', 'active')");

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$ropaId', 'Ropa de mujer', 0)");
        foreach ([
            ['Remera mujer','unidad'],['Blusa mujer','unidad'],['Vestido casual','unidad'],
            ['Jean mujer','unidad'],['Pantalón de vestir mujer','unidad'],['Calza','unidad'],
            ['Campera mujer','unidad'],['Buzo con capucha mujer','unidad'],
            ['Bombacha','unidad'],['Corpiño','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$ropaId', 'Ropa de hombre', 1)");
        foreach ([
            ['Remera hombre','unidad'],['Camisa manga corta','unidad'],['Camisa manga larga','unidad'],
            ['Jean hombre','unidad'],['Pantalón de vestir hombre','unidad'],
            ['Bermuda','unidad'],['Campera hombre','unidad'],['Buzo con capucha hombre','unidad'],
            ['Calzoncillo','unidad'],['Medias x3','pack'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$ropaId', 'Calzado', 2)");
        foreach ([
            ['Zapatillas mujer','unidad'],['Zapatillas hombre','unidad'],
            ['Zapatos de vestir mujer','unidad'],['Zapatos de vestir hombre','unidad'],
            ['Ojotas','unidad'],['Botas mujer','unidad'],['Botas hombre','unidad'],
            ['Pantuflas','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$ropaId', 'Ropa de niños', 3)");
        foreach ([
            ['Remera niño','unidad'],['Pantalón niño','unidad'],['Vestido niña','unidad'],
            ['Conjunto bebé','unidad'],['Pañales','paquete'],['Medias niño','pack'],
            ['Campera niño','unidad'],['Buzo niño','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $catId = $this->uuid();
        $this->execute("INSERT OR IGNORE INTO industry_categories (id, industry_id, name, position) VALUES ('$catId', '$ropaId', 'Accesorios', 4)");
        foreach ([
            ['Cartera mujer','unidad'],['Mochila','unidad'],['Cinturón','unidad'],
            ['Gorra','unidad'],['Bufanda','unidad'],['Guantes','unidad'],
            ['Gafas de sol','unidad'],['Reloj','unidad'],['Pulsera','unidad'],['Collar','unidad'],
        ] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT OR IGNORE INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$catId', '{$p[0]}', '{$p[1]}', $i)");
        }
    }

    public function down(): void
    {
        $newIndustries = ['Kiosco', 'Panadería', 'Farmacia / Perfumería', 'Librería', 'Dietética', 'Perfumería', 'Ropa y textiles'];
        foreach ($newIndustries as $name) {
            $rows = $this->fetchAll("SELECT id FROM industries WHERE name='$name' LIMIT 1");
            if (!empty($rows)) {
                $id = $rows[0]['id'];
                $cats = $this->fetchAll("SELECT id FROM industry_categories WHERE industry_id='$id'");
                foreach ($cats as $cat) {
                    $this->execute("DELETE FROM industry_products WHERE industry_category_id='{$cat['id']}'");
                }
                $this->execute("DELETE FROM industry_categories WHERE industry_id='$id'");
                $this->execute("DELETE FROM industries WHERE id='$id'");
            }
        }
    }

    private function uuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }
}
