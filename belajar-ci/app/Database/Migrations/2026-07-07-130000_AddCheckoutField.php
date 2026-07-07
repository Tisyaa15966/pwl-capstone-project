<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCheckoutField extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction',[
            'ppn'=>[
                'type'=>'DOUBLE',
                'null'=>true
            ],
            'biaya_admin'=>[
                'type'=>'DOUBLE',
                'null'=>true
            ],
            'kupon_code'=>[
                'type'=>'VARCHAR',
                'constraint'=>20,
                'null'=>true
            ],
            'diskon_kupon'=>[
                'type'=>'DOUBLE',
                'null'=>true
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction',
        [
            'ppn',
            'biaya_admin',
            'kupon_code',
            'diskon_kupon'
        ]);
    }
}