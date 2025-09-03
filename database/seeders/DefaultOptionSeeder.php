<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pais;

class DefaultOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $colombia = Pais::find('170');
       $colombia->nivel=5;
       $colombia->save();
       $venezuela = Pais::find('862');
       $venezuela->nivel=3;
       $venezuela->save();
       $peru = Pais::find('604');
       $peru->nivel=3;
       $peru->save();
       $ecuador = Pais::find('218');
       $ecuador->nivel=3;
       $ecuador->save();
       $brasil = Pais::find('076');
       $brasil->nivel=3;
       $brasil->save();
       $argentina = Pais::find('032');
       $argentina->nivel=3;
       $argentina->save();
       $chile = Pais::find('152');
       $chile->nivel=3;
       $chile->save();
       $bolivia = Pais::find('068');
       $bolivia->nivel=3;
       $bolivia->save();
       $paraguay = Pais::find('600');
       $paraguay->nivel=3;
       $paraguay->save();
       $uruguay = Pais::find('858');
       $uruguay->nivel=3;
       $uruguay->save();
       $mexico = Pais::find('484');
       $mexico->nivel=3;
       $mexico->save();
       $panama = Pais::find('591');
       $panama->nivel=3;
       $panama->save();
       $usa = Pais::find('840');
       $usa->nivel=2;
       $usa->save();
       $espania = Pais::find('724');
       $espania->nivel=2;
       $espania->save();
       $italia = Pais::find('380');
       $italia->nivel=2;
       $italia->save();
    }
}
