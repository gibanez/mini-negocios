<?php

use Phinx\Migration\AbstractMigration;

final class SeedIndustries extends AbstractMigration
{
    public function up(): void
    {
        // Almacén
        $almacenId = $this->uuid();
        $this->execute("INSERT INTO industries (id, name, description, status) VALUES ('$almacenId', 'Almacén', 'Artículos de consumo diario, bebidas y limpieza', 'active')");

        $lacteosId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$lacteosId', '$almacenId', 'Lácteos', 0)");
        foreach ([['Leche entera','litro'],['Leche descremada','litro'],['Yogur natural','unidad'],['Queso en barra','kg'],['Manteca','unidad']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$lacteosId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $bebidasId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$bebidasId', '$almacenId', 'Bebidas', 1)");
        foreach ([['Agua mineral','litro'],['Gaseosa cola','litro'],['Jugo en caja','litro'],['Cerveza lata','unidad'],['Vino en botella','litro']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$bebidasId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $panId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$panId', '$almacenId', 'Panificados', 2)");
        foreach ([['Pan de molde','unidad'],['Galletitas','paquete'],['Facturas','unidad'],['Bizcochos','kg']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$panId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $limpId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$limpId', '$almacenId', 'Limpieza', 3)");
        foreach ([['Lavandina','litro'],['Detergente','litro'],['Jabón en polvo','kg'],['Papel higiénico','paquete'],['Desengrasante','litro']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$limpId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $enlId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$enlId', '$almacenId', 'Enlatados', 4)");
        foreach ([['Atún en aceite','unidad'],['Tomate triturado','unidad'],['Arvejas','unidad'],['Choclo','unidad'],['Sardinas','unidad']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$enlId', '{$p[0]}', '{$p[1]}', $i)");
        }

        // Verdulería
        $verdId = $this->uuid();
        $this->execute("INSERT INTO industries (id, name, description, status) VALUES ('$verdId', 'Verdulería', 'Frutas, verduras y hierbas frescas', 'active')");

        $verdurasId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$verdurasId', '$verdId', 'Verduras', 0)");
        foreach ([['Tomate','kg'],['Lechuga','unidad'],['Papa','kg'],['Cebolla','kg'],['Zanahoria','kg'],['Morrón','kg'],['Zapallito','kg'],['Chaucha','kg']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$verdurasId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $frutasId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$frutasId', '$verdId', 'Frutas', 1)");
        foreach ([['Manzana','kg'],['Naranja','kg'],['Banana','kg'],['Mandarina','kg'],['Pera','kg'],['Durazno','kg'],['Uva','kg']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$frutasId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $hierbaId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$hierbaId', '$verdId', 'Hierbas', 2)");
        foreach ([['Perejil','atado'],['Cilantro','atado'],['Albahaca','atado'],['Tomillo','atado']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$hierbaId', '{$p[0]}', '{$p[1]}', $i)");
        }

        // Carnicería
        $carnicId = $this->uuid();
        $this->execute("INSERT INTO industries (id, name, description, status) VALUES ('$carnicId', 'Carnicería', 'Carnes, aves y embutidos', 'active')");

        $rojasId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$rojasId', '$carnicId', 'Carnes rojas', 0)");
        foreach ([['Asado','kg'],['Bife de chorizo','kg'],['Paleta','kg'],['Nalga','kg'],['Carne picada','kg'],['Costilla','kg']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$rojasId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $avesId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$avesId', '$carnicId', 'Aves', 1)");
        foreach ([['Pollo entero','kg'],['Pechuga de pollo','kg'],['Muslo y contramuslo','kg'],['Pollo trozado','kg']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$avesId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $embutId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$embutId', '$carnicId', 'Embutidos', 2)");
        foreach ([['Chorizo','kg'],['Salchicha','kg'],['Morcilla','unidad'],['Bondiola','kg']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$embutId', '{$p[0]}', '{$p[1]}', $i)");
        }

        // Ferretería
        $ferrId = $this->uuid();
        $this->execute("INSERT INTO industries (id, name, description, status) VALUES ('$ferrId', 'Ferretería', 'Herramientas, materiales y artículos de construcción', 'active')");

        $herramId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$herramId', '$ferrId', 'Herramientas', 0)");
        foreach ([['Martillo','unidad'],['Destornillador plano','unidad'],['Destornillador Phillips','unidad'],['Alicate','unidad'],['Cinta métrica','unidad'],['Serrucho','unidad']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$herramId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $fijId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$fijId', '$ferrId', 'Fijaciones', 1)");
        foreach ([['Tornillos','caja'],['Clavos','kg'],['Tarugos','caja'],['Bulones','caja']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$fijId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $elecId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$elecId', '$ferrId', 'Electricidad', 2)");
        foreach ([['Foco LED','unidad'],['Tomacorriente','unidad'],['Llave de luz','unidad'],['Cable duplex','metro'],['Cinta aisladora','rollo']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$elecId', '{$p[0]}', '{$p[1]}', $i)");
        }

        $pintId = $this->uuid();
        $this->execute("INSERT INTO industry_categories (id, industry_id, name, position) VALUES ('$pintId', '$ferrId', 'Pinturas', 3)");
        foreach ([['Látex interior','litro'],['Látex exterior','litro'],['Esmalte sintético','litro'],['Aguarrás','litro'],['Pincel','unidad']] as $i => $p) {
            $pid = $this->uuid();
            $this->execute("INSERT INTO industry_products (id, industry_category_id, name, unit, position) VALUES ('$pid', '$pintId', '{$p[0]}', '{$p[1]}', $i)");
        }
    }

    public function down(): void
    {
        $this->execute('DELETE FROM industry_products');
        $this->execute('DELETE FROM industry_categories');
        $this->execute('DELETE FROM industries');
    }

    private function uuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }
}
