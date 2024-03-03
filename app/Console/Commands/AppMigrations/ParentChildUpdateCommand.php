<?php

namespace App\Console\Commands\AppMigrations;

use App\Models\Regulation;
use App\Models\RegulationType;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ParentChildUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app-migration:parent-child-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update table data to support parent child relation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line("Create main types");
        $time = Carbon::now();
        RegulationType::insert([
            ['name' => 'Spoljnotrgovinsko poslovanje', 'app' => 'exportinfo', 'created_at' => $time, 'updated_at' => $time],
            ['name' => 'Devizno poslovanje', 'app' => 'exportinfo', 'created_at' => $time, 'updated_at' => $time],
            ['name' => 'Carinsko poslovanje', 'app' => 'exportinfo', 'created_at' => $time, 'updated_at' => $time],
        ]);
        $this->line("Create subtypes");
        $regulationType = RegulationType::where('name', 'Spoljnotrgovinsko poslovanje')->first();
        $regulationType->children()->createMany([
            ['name' => 'Naredbe', 'app' => 'exportinfo'],
            ['name' => 'Odluke', 'app' => 'exportinfo'],
            ['name' => 'Pravilnici', 'app' => 'exportinfo'],
            ['name' => 'Uputstva', 'app' => 'exportinfo'],
            ['name' => 'Uredbe', 'app' => 'exportinfo'],
            ['name' => 'Uzanse', 'app' => 'exportinfo'],
            ['name' => 'Zakoni', 'app' => 'exportinfo'],
            ['name' => 'Ostali propisi', 'app' => 'exportinfo'],
        ]);
        $regulationType = RegulationType::where('name', 'Devizno poslovanje')->first();
        $regulationType->children()->createMany([
            ['name' => 'Naredbe', 'app' => 'exportinfo'],
            ['name' => 'Odluke', 'app' => 'exportinfo'],
            ['name' => 'Pravilnici', 'app' => 'exportinfo'],
            ['name' => 'Uputstva', 'app' => 'exportinfo'],
            ['name' => 'Uredbe', 'app' => 'exportinfo'],
            ['name' => 'Uzanse', 'app' => 'exportinfo'],
            ['name' => 'Zakoni', 'app' => 'exportinfo'],
            ['name' => 'Ostali propisi', 'app' => 'exportinfo'],
        ]);
        $regulationType = RegulationType::where('name', 'Carinsko poslovanje')->first();
        $regulationType->children()->createMany([
            ['name' => 'Naredbe', 'app' => 'exportinfo'],
            ['name' => 'Odluke', 'app' => 'exportinfo'],
            ['name' => 'Pravilnici', 'app' => 'exportinfo'],
            ['name' => 'Uputstva', 'app' => 'exportinfo'],
            ['name' => 'Uredbe', 'app' => 'exportinfo'],
            ['name' => 'Uzanse', 'app' => 'exportinfo'],
            ['name' => 'Zakoni', 'app' => 'exportinfo'],
            ['name' => 'Ostali propisi', 'app' => 'exportinfo'],
        ]);
        $this->line("Update regulations table");
        $oldTypesIds = [];
        //EI
        $regulations = Regulation::where('app', 'exportinfo')->get();
        foreach ($regulations as $regulation) {
            if ($regulation->regulation_subtype === 'foreign_trade_business') {
                $oldTypesIds[] = $regulation->regulationType->id;
                $oldTypeName = $regulation->regulationType->name;
                $newParentType = RegulationType::where('name', 'Spoljnotrgovinsko poslovanje')->first();
                $newSubType = $newParentType->children()->where('name', $oldTypeName)->first();
                $regulation->regulation_type_id = $newSubType->id;
                $regulation->save();
            } else if ($regulation->regulation_subtype === 'foreign_exchange_business') {
                $oldTypesIds[] = $regulation->regulationType->id;
                $oldTypeName = $regulation->regulationType->name;
                $newParentType = RegulationType::where('name', 'Devizno poslovanje')->first();
                $newSubType = $newParentType->children()->where('name', $oldTypeName)->first();
                $regulation->regulation_type_id = $newSubType->id;
                $regulation->save();
            } else if ($regulation->regulation_subtype === 'foreign_customs_business') {
                $oldTypesIds[] = $regulation->regulationType->id;
                $oldTypeName = $regulation->regulationType->name;
                $newParentType = RegulationType::where('name', 'Carinsko poslovanje')->first();
                $newSubType = $newParentType->children()->where('name', $oldTypeName)->first();
                $regulation->regulation_type_id = $newSubType->id;
                $regulation->save();
            }
        }

        $this->line("Delete old types");
        RegulationType::whereIn('id', $oldTypesIds)->delete();
        $this->info("Done!");
        $this->newLine();
    }
}
